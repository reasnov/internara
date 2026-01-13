<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Casts\SettingValueCast;
use Modules\Setting\Models\Setting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method seeds the application with default settings, converting values
     * via the SettingValueCast before using a single 'upsert' operation for efficiency.
     */
    public function run(): void
    {
        $rawSettings = [
            [
                'key' => 'app_name',
                'value' => config('app.name', 'Internara'),
                'type' => 'string',
                'description' => 'Application name',
                'group' => 'system',
            ],
            [
                'key' => 'app_logo',
                'value' => config('app.logo', ''),
                'type' => 'string',
                'description' => 'Application logo',
                'group' => 'system',
            ],
            [
                'key' => 'app_installed',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Indicates whether the application is installed',
                'group' => 'system',
            ],
            [
                'key' => 'brand_name',
                'value' => config('setting.brand_name', 'Internara'),
                'type' => 'string',
                'description' => 'The name of the institute brand',
                'group' => 'general',
            ],
            [
                'key' => 'brand_logo',
                'value' => '',
                'type' => 'string',
                'description' => 'The logo of the institute brand',
                'group' => 'general',
            ],
            [
                'key' => 'brand_logo_dark',
                'value' => '',
                'type' => 'string',
                'description' => 'The logo of the institute brand',
                'group' => 'general',
            ],
            [
                'key' => 'site_title',
                'value' => config('setting.site_title', 'Internara - Sistem Informasi Manajemen PKL'),
                'type' => 'string',
                'description' => 'The title of the site',
                'group' => 'general',
            ],
        ];

        $settingsToUpsert = [];
        $caster = new SettingValueCast;
        $dummyModel = new Setting; // A dummy model instance for the caster's set method

        foreach ($rawSettings as $setting) {
            // Apply the SettingValueCast::set logic manually to each setting
            $processed = $caster->set($dummyModel, 'value', $setting['value'], ['type' => $setting['type'] ?? 'string']);

            $settingsToUpsert[] = array_merge($setting, [
                'value' => $processed['value'],
                'type' => $processed['type'],
            ]);
        }

        Setting::upsert(
            $settingsToUpsert,
            ['key'],
            ['value', 'type', 'description', 'group']
        );
    }
}
