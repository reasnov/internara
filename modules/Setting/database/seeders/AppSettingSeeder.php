<?php

namespace Modules\Setting\Database\Seeders;

class AppSettingSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        $settings = [
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
            [
                'key' => 'app_installed',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Indicates whether the application is installed',
                'group' => 'system',
            ],
        ];

        foreach ($settings as $setting) {
            \Modules\Setting\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
