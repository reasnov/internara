<?php

namespace Modules\Setting\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\Models\Setting;
use Modules\Setting\Services\SettingService;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::fake(); // Ensure the Cache facade is ready for testing/mocking
    $this->service = new SettingService;
});

test('it can get a setting by key', function () {
    Setting::factory()->string(key: 'app_name', value: 'Internara App')->create();

    $value = $this->service->get('app_name');
    expect($value)->toBe('Internara App');
});

test('it returns default if setting not found', function () {
    $value = $this->service->get('non_existent_key', 'Default Value');
    expect($value)->toBe('Default Value');
});

test('it retrieves settings from cache', function () {
    Cache::shouldReceive('remember')
        ->once() // Expect cache::remember to be called once
        ->andReturn('Cached Value');

    $value = $this->service->get('app_name');
    expect($value)->toBe('Cached Value');
});

test('it bypasses cache when skipCached is true', function () {
    Setting::factory()->string(key: 'app_name', value: 'DB Value')->create();

    Cache::shouldReceive('forget')
        ->once() // Expect cache::forget to be called
        ->with('settings.app_name');

    Cache::shouldReceive('remember')
        ->once() // Still expect remember, but the forget will force fresh data
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback(); // Execute the callback to simulate fetching from DB
        });

    $value = $this->service->get('app_name', null, true);
    expect($value)->toBe('DB Value');
});

test('it can get multiple settings by an array of keys', function () {
    Setting::factory()->string(key: 'app_name', value: 'Internara App')->create();
    Setting::factory()->integer(key: 'app_version', value: 1)->create();

    $values = $this->service->get(['app_name', 'app_version']);
    expect($values)->toEqual([
        'app_name' => 'Internara App',
        'app_version' => 1,
    ]);
});

test('it returns default for missing keys in array request', function () {
    Setting::factory()->string(key: 'app_name', value: 'Internara App')->create();

    $values = $this->service->get(['app_name', 'non_existent_key'], 'Default');
    expect($values)->toEqual([
        'app_name' => 'Internara App',
        'non_existent_key' => 'Default',
    ]);
});

test('it can create a new setting', function () {
    $success = $this->service->set('new_setting', 'New Value', ['type' => 'string', 'description' => 'A new setting']);

    expect($success)->toBeTrue();
    expect(Setting::find('new_setting'))->not->toBeNull()
        ->and(Setting::find('new_setting')->value)->toBe('New Value')
        ->and(Setting::find('new_setting')->description)->toBe('A new setting');
});

test('it can update an existing setting', function () {
    Setting::factory()->string(key: 'existing_setting', value: 'Old Value')->create();

    $success = $this->service->set('existing_setting', 'Updated Value');

    expect($success)->toBeTrue();
    expect(Setting::find('existing_setting')->value)->toBe('Updated Value');
});

test('it clears cache for individual setting on set', function () {
    Setting::factory()->string(key: 'app_name', value: 'Internara App')->create();

    Cache::shouldReceive('forget')
        ->once() // Expect forget for individual key
        ->with('settings.app_name');
    Cache::shouldReceive('forget')
        ->once() // Expect forget for group (if group is null)
        ->with('settings.group.');

    $this->service->set('app_name', 'New Value');
});

test('it clears cache for group on set', function () {
    Setting::factory()->string(key: 'app_name', value: 'Internara App', group: 'general')->create();

    Cache::shouldReceive('forget')
        ->once() // Expect forget for individual key
        ->with('settings.app_name');
    Cache::shouldReceive('forget')
        ->once() // Expect forget for group
        ->with('settings.group.general');

    $this->service->set('app_name', 'New Value', ['group' => 'general']);
});

test('it can set multiple settings by an array of key-value pairs', function () {
    $settingsToSet = [
        'setting_one' => 'Value One',
        'setting_two' => 2,
    ];
    $success = $this->service->set($settingsToSet, null, ['type' => 'string']); // extraAttributes will be applied to all if not specified per item

    expect($success)->toBeTrue();
    expect(Setting::find('setting_one')->value)->toBe('Value One');
    expect(Setting::find('setting_two')->value)->toBe(2);
});

test('it handles extra attributes for a single setting', function () {
    $this->service->set('advanced_setting', true, ['type' => 'boolean', 'description' => 'Advanced config', 'group' => 'advanced']);

    $setting = Setting::find('advanced_setting');
    expect($setting->value)->toBeTrue()
        ->and($setting->type)->toBe('boolean')
        ->and($setting->description)->toBe('Advanced config')
        ->and($setting->group)->toBe('advanced');
});

test('it handles extra attributes for multiple settings', function () {
    $settingsData = [
        'setting_a' => ['value' => 'Val A', 'type' => 'string', 'group' => 'general'],
        'setting_b' => ['value' => 123, 'type' => 'integer', 'group' => 'numeric'],
    ];

    $success = $this->service->set($settingsData);

    expect($success)->toBeTrue();

    $settingA = Setting::find('setting_a');
    expect($settingA->value)->toBe('Val A')
        ->and($settingA->type)->toBe('string')
        ->and($settingA->group)->toBe('general');

    $settingB = Setting::find('setting_b');
    expect($settingB->value)->toBe(123)
        ->and($settingB->type)->toBe('integer')
        ->and($settingB->group)->toBe('numeric');
});

test('it can get settings by group', function () {
    Setting::factory()->string(key: 'app_name', group: 'general')->create();
    Setting::factory()->integer(key: 'app_version', group: 'general')->create();
    Setting::factory()->string(key: 'other_setting', group: 'other')->create();

    $generalSettings = $this->service->getByGroup('general');
    expect($generalSettings)->toHaveCount(2)
        ->and($generalSettings->pluck('key')->toArray())->toEqual(['app_name', 'app_version']);
});

test('it retrieves group settings from cache', function () {
    Setting::factory()->string(key: 'app_name', group: 'general')->create();

    Cache::shouldReceive('remember')
        ->once() // Expect cache::remember to be called once
        ->andReturn(collect([Setting::find('app_name')])); // Return a collection for group

    $generalSettings = $this->service->getByGroup('general');
    expect($generalSettings->first()->value)->toBe('app_name');
});

test('it returns empty collection if group not found', function () {
    $generalSettings = $this->service->getByGroup('non_existent_group');
    expect($generalSettings)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($generalSettings)->toBeEmpty();
});
