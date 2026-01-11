<?php

namespace Modules\Setting\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Setting\Models\Setting;

uses(RefreshDatabase::class);

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

test('it casts \'value\' attribute correctly based on its type', function () {
    // Test with string value
    $settingString = Setting::factory()->string(['value' => 'test_string'])->create();
    $settingString->refresh(); // Ensure cast is applied on retrieval
    expect($settingString->value)->toBe('test_string')
        ->and($settingString->type)->toBe('string');

    // Test with integer value
    $settingInt = Setting::factory()->integer(['value' => 123])->create();
    $settingInt->refresh(); // Ensure cast is applied on retrieval
    expect($settingInt->value)->toBe(123)
        ->and($settingInt->type)->toBe('integer');

    // Test with float value
    $settingFloat = Setting::factory()->float(['value' => 123.45])->create();
    $settingFloat->refresh(); // Ensure cast is applied on retrieval
    expect($settingFloat->value)->toBe(123.45)
        ->and($settingFloat->type)->toBe('float');

    // Test with boolean value (true)
    $settingBoolTrue = Setting::factory()->boolean(['value' => true])->create();
    $settingBoolTrue->refresh(); // Ensure cast is applied on retrieval
    expect($settingBoolTrue->value)->toBeTrue()
        ->and($settingBoolTrue->type)->toBe('boolean');

    // Test with boolean value (false)
    $settingBoolFalse = Setting::factory()->boolean(['value' => false])->create();
    $settingBoolFalse->refresh(); // Ensure cast is applied on retrieval
    expect($settingBoolFalse->value)->toBeFalse()
        ->and($settingBoolFalse->type)->toBe('boolean');

    // Test with array value
    $arrayValue = ['a' => 1, 'b' => 'test', 'c' => ['nested' => true]];
    $settingArray = Setting::factory()->array(['value' => $arrayValue])->create();
    $settingArray->refresh(); // Ensure cast is applied on retrieval
    expect($settingArray->value)->toEqual($arrayValue)
        ->and($settingArray->type)->toBe('json'); // Array is cast to JSON string internally

    // Test with json value (which is also array behind the scenes for Eloquent)
    $jsonValue = ['x' => 'y', 'z' => 100];
    $settingJson = Setting::factory()->json(['value' => $jsonValue])->create();
    $settingJson->refresh(); // Ensure cast is applied on retrieval
    expect($settingJson->value)->toEqual($jsonValue)
        ->and($settingJson->type)->toBe('json'); // JSON is cast to JSON string internally

    // Test with null value
    $settingNull = Setting::factory()->nullType(['value' => null])->create();
    $settingNull->refresh(); // Ensure cast is applied on retrieval
    expect($settingNull->value)->toBeNull()
        ->and($settingNull->type)->toBe('null');

    // Test with an object value (should be cast to JSON and retrieved as array)
    $objectValue = new class { public $prop = 'value'; public $num = 1; };
    $settingObject = Setting::factory()->json(['value' => $objectValue])->create(); // Use json factory type
    $settingObject->refresh(); // Ensure cast is applied on retrieval
    expect($settingObject->value)->toEqual(json_decode(json_encode($objectValue), true)) // Compare with JSON decoded array
        ->and($settingObject->type)->toBe('json');
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

test('it can create a float setting via factory', function () {
    $setting = Setting::factory()->float()->create();
    expect($setting->type)->toBe('float');
    expect($setting->value)->toBeFloat();
});

test('it can create a boolean setting via factory', function () {
    $setting = Setting::factory()->boolean()->create();
    expect($setting->type)->toBe('boolean');
    expect($setting->value)->toBeBool();
});

test('it can create an array setting via factory', function () {
    $setting = Setting::factory()->array()->create();
    expect($setting->type)->toBe('json'); // Harmonized to 'json' by factory
    expect($setting->value)->toBeArray();
});

test('it can create a json setting via factory', function () {
    $setting = Setting::factory()->json()->create();
    expect($setting->type)->toBe('json');
    expect($setting->value)->toBeArray(); // JSON is typically cast to array in PHP
});

test('it can create a null setting via factory', function () {
    $setting = Setting::factory()->nullType()->create();
    expect($setting->type)->toBe('null');
    expect($setting->value)->toBeNull();
});
