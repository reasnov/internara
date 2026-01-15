<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nwidart\Modules\Facades\Module;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $moduleSeeders = $this->getModuleSeeders();

        $this->call($moduleSeeders);
    }

    protected function getModuleSeeders(): array
    {
        $seeders = [];

        $enabledModules = Module::allEnabled();

        foreach ($enabledModules as $module) {
            $seederClass = "Modules\\{$module->getName()}\\Database\\Seeders\\{$module->getName()}DatabaseSeeder";
            if (class_exists($seederClass)) {
                $seeders[] = $seederClass;
            }
        }

        return $seeders;
    }
}
