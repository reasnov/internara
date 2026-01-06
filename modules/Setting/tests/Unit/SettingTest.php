<?php

namespace Modules\Setting\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Setting\Models\Setting;

uses(\Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Add any setup needed before each test
});

test('it uses \'key\' as primary key', function () {
    $setting = Setting::factory()->create();
    expect($setting->getKeyName())->toBe('key');
});

test('it sets \'keyType\' to string', function () {
    $setting = Setting::factory()->create();
    expect($setting->getKeyType())->toBe('string');
});

test('it sets \'incrementing\' to false', function () {
    $setting = Setting::factory()->create();
    expect($setting->incrementing)->toBeFalse();
});

test('it has fillable attributes', function () {
    $setting = new Setting;
    expect($setting->getFillable())->toEqual(['key', 'value', 'type', 'description', 'group']);
});

test('it casts \'value\' attribute', function () {
    // Test with string value
    $settingString = Setting::factory()->string(value: 'test_string')->create();
    expect($settingString->value)->toBe('test_string');

    // Test with integer value
    $settingInt = Setting::factory()->integer(value: 123)->create();
    expect($settingInt->value)->toBe(123);

    // Test with boolean value
    $settingBool = Setting::factory()->boolean(value: true)->create();
    expect($settingBool->value)->toBeTrue();

    // Test with array value
    $settingArray = Setting::factory()->array(value: ['a' => 1, 'b' => 'test'])->create();
    expect($settingArray->value)->toEqual(['a' => 1, 'b' => 'test']);

    // Test with json value (which is also array behind the scenes for Eloquent)
    $settingJson = Setting::factory()->json(value: ['x' => 'y'])->create();
    expect($settingJson->value)->toEqual(['x' => 'y']);
});

test('it can scope settings by group', function () {
    Setting::factory()->create(['key' => 'setting1', 'group' => 'general']);
    Setting::factory()->create(['key' => 'setting2', 'group' => 'general']);
    Setting::factory()->create(['key' => 'setting3', 'group' => 'display']);

    $generalSettings = Setting::group('general')->get();

    expect($generalSettings)->toHaveCount(2)
        ->and($generalSettings->pluck('key')->toArray())->toEqual(['setting1', 'setting2']);
});

test('it can create a default string setting via factory', function () {
    $setting = Setting::factory()->create();
    expect($setting->type)->toBe('string');
    expect($setting->value)->toBeString();
});

test('it can create an integer setting via factory', function () {
    $setting = Setting::factory()->integer()->create();
    expect($setting->type)->toBe('integer');
    expect($setting->value)->toBeInt();
});

test('it can create a boolean setting via factory', function () {
    $setting = Setting::factory()->boolean()->create();
    expect($setting->type)->toBe('boolean');
    expect($setting->value)->toBeBool();
});

test('it can create an array setting via factory', function () {
    $setting = Setting::factory()->array()->create();
    expect($setting->type)->toBe('array');
    expect($setting->value)->toBeArray();
});

test('it can create a json setting via factory', function () {
    $setting = Setting::factory()->json()->create();
    expect($setting->type)->toBe('json');
    expect($setting->value)->toBeArray(); // JSON is typically cast to array in PHP
});
