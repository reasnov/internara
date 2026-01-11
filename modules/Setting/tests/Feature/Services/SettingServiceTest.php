<?php

namespace Modules\Setting\Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\Models\Setting;
use Modules\Setting\Services\SettingService;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new SettingService(new Setting());
});

test('it can get a setting by key', function () {
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App'])->create();

    $value = $this->service->get('app_name');
    expect($value)->toBe('Internara App');
});

test('it returns default if setting not found', function () {
    $value = $this->service->get('non_existent_key', 'Default Value');
    expect($value)->toBe('Default Value');
});

test('it retrieves settings from cache', function () {
    $expectedValue = 'Cached App Name';
    Setting::factory()->string(['key' => 'app_name', 'value' => $expectedValue])->create();

    Cache::shouldReceive('remember')
        ->once() // Expect cache::remember to be called once
        ->andReturn($expectedValue);

    $value = $this->service->get('app_name');
    expect($value)->toBe($expectedValue);
});

test('it bypasses cache when skipCached is true for get', function () {
    // Explicitly set group to null to test the scenario where the setting has no group.
    $setting = Setting::factory()->string(['key' => 'app_name', 'value' => 'DB Value', 'group' => null])->create();

    Cache::shouldReceive('forget')
        ->once() // Expect forget for individual key
        ->with('settings.app_name');

    // Cache::remember should not be called when skipCached is true
    // Instead, the callback is executed directly after clearing the cache.


    $value = $this->service->get('app_name', null, true); // This calls forget('app_name')
    expect($value)->toBe('DB Value');
});

test('it can get multiple settings by an array of keys', function () {
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App'])->create();
    Setting::factory()->integer(['key' => 'app_version', 'value' => 1])->create();

    $values = $this->service->get(['app_name', 'app_version']);
    expect($values)->toEqual([
        'app_name' => 'Internara App',
        'app_version' => 1,
    ]);
});

test('it returns default for missing keys in array request', function () {
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App'])->create();

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
    Setting::factory()->string(['key' => 'existing_setting', 'value' => 'Old Value'])->create();

    $success = $this->service->set('existing_setting', 'Updated Value');

    expect($success)->toBeTrue();
    expect(Setting::find('existing_setting')->value)->toBe('Updated Value');
});

test('it clears cache for individual setting on set', function () {
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App', 'group' => null])->create(); // Explicitly set group to null

    Cache::shouldReceive('forget')
        ->once() // Expect forget for individual key
        ->with('settings.app_name');
    Cache::shouldReceive('forget')
        ->once() // Expect forget for 'all' settings cache
        ->with('settings.all');

    $this->service->set('app_name', 'New Value');
});

test('it clears cache for group on set', function () {
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App', 'group' => 'general'])->create();

    Cache::shouldReceive('forget')
        ->once() // Expect forget for individual key
        ->with('settings.app_name');
    Cache::shouldReceive('forget')
        ->once() // Expect forget for group
        ->with('settings.group.general');
    Cache::shouldReceive('forget')
        ->once() // Expect forget for 'all' settings cache
        ->with('settings.all');

    $this->service->set('app_name', 'New Value', ['group' => 'general']);
});

test('it can set multiple settings by an array of key-value pairs', function () {
    $settingsData = [
        'setting_one' => 'Value One',
        'setting_two' => 2,
    ];

    $success = $this->service->set($settingsData);

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
    Setting::factory()->string(['key' => 'app_name', 'value' => 'Internara App', 'group' => 'general'])->create();
    Setting::factory()->integer(['key' => 'app_version', 'value' => 1, 'group' => 'general'])->create();
    Setting::factory()->string(['key' => 'other_setting', 'value' => 'Other Value', 'group' => 'other'])->create();

    $generalSettings = $this->service->getByGroup('general');
    expect($generalSettings)->toHaveCount(2)
        ->and($generalSettings->pluck('key')->toArray())->toEqual(['app_name', 'app_version']);
});

test('it retrieves group settings from cache', function () {
    $expectedValue = collect([
        (new Setting(['key' => 'app_name', 'value' => 'My Cached App Name', 'type' => 'string', 'group' => 'general'])),
    ]);
    Setting::factory()->string(['key' => 'app_name', 'value' => 'My Cached App Name', 'group' => 'general'])->create();

    Cache::shouldReceive('remember')
        ->once() // Expect cache::remember to be called once
        // Mock the return value to be a collection of Setting objects with the expected value
        ->andReturn($expectedValue);

    $generalSettings = $this->service->getByGroup('general');
    expect($generalSettings->first()->value)->toBe('My Cached App Name'); // Expect the explicitly set value
});

test('it returns empty collection if group not found', function () {
    $generalSettings = $this->service->getByGroup('non_existent_group');
    expect($generalSettings)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($generalSettings)->toBeEmpty();
});

test('it returns an empty collection when no settings are present for all()', function () {
    $allSettings = $this->service->all();
    expect($allSettings)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($allSettings)->toBeEmpty();
});

test('it returns all existing settings for all()', function () {
    Setting::factory()->count(3)->create();

    $allSettings = $this->service->all();
    expect($allSettings)->toHaveCount(3);
});

test('it retrieves all settings from cache for all()', function () {
    $expectedValue = collect([
        (new Setting(['key' => 'cached_all_setting', 'value' => 'Cached Value', 'type' => 'string'])),
    ]);
    Setting::factory()->string(['key' => 'cached_all_setting', 'value' => 'Cached Value'])->create();

    Cache::shouldReceive('remember')
        ->once()
        ->andReturn($expectedValue);

    $allSettings = $this->service->all();
    expect($allSettings->first()->value)->toBe('Cached Value');
});

test('it bypasses cache when skipCache is true for all()', function () {
    Setting::factory()->string(['key' => 'db_all_setting', 'value' => 'DB Value'])->create();

    Cache::shouldReceive('forget')
        ->once()
        ->with('settings.all');

    // Cache::remember should not be called when skipCache is true
    // Instead, the callback is executed directly after clearing the cache.


    $allSettings = $this->service->all(skipCache: true);
    expect($allSettings->first()->value)->toBe('DB Value');
});

test('it can delete an existing setting', function () {
    Setting::factory()->string(['key' => 'setting_to_delete', 'value' => 'Value'])->create();

    $deleted = $this->service->delete('setting_to_delete');
    expect($deleted)->toBeTrue();
    expect(Setting::find('setting_to_delete'))->toBeNull();
});

test('it returns false when deleting a non-existent setting', function () {
    $deleted = $this->service->delete('non_existent_delete_setting');
    expect($deleted)->toBeFalse();
});

test('it clears cache after deleting a setting', function () {
    Setting::factory()->string(['key' => 'setting_to_delete_cache', 'value' => 'Value', 'group' => 'test_group'])->create();

    Cache::shouldReceive('forget')
        ->once()
        ->with('settings.setting_to_delete_cache');
    Cache::shouldReceive('forget')
        ->once()
        ->with('settings.group.test_group');
    Cache::shouldReceive('forget')
        ->once()
        ->with('settings.all');

    $this->service->delete('setting_to_delete_cache');
});

test('it returns an eloquent builder instance for query()', function () {
    $builder = $this->service->query();
    expect($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

test('it can retrieve settings using the query() builder', function () {
    Setting::factory()->string(['key' => 'query_setting_1', 'value' => 'Value 1', 'group' => 'query_group'])->create();
    Setting::factory()->string(['key' => 'query_setting_2', 'value' => 'Value 2', 'group' => 'other_group'])->create();

    $settings = $this->service->query()->where('group', 'query_group')->get();
    expect($settings)->toHaveCount(1)
        ->and($settings->first()->key)->toBe('query_setting_1');
});