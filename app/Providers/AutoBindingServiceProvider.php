<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class AutoBindingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = array_merge(
            $this->getDefaultBindings(),
            $this->getBindings()
        );

        foreach ($bindings as $abstract => $concrete) {
            /** Ensure that the abstract is an existing interface and the concrete is an existing class (not an interface itself) */
            if (interface_exists($abstract) && class_exists($concrete) && !interface_exists($concrete)) {
                $this->app->bind($abstract, $concrete);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the default service bindings for the application, discovered via file system.
     *
     * @return array<string, string>
     */
    protected function getDefaultBindings(): array
    {
        $bindings = [];

        // Scan App/src/Contracts (Assuming structure: app/src/Contracts) - Adjusted for potential structure
        // If 'app' folder uses standard Laravel structure, contracts might be in app/Contracts
        $appContractPath = app_path('Contracts');
        if (file_exists($appContractPath)) {
            $bindings = array_merge($bindings, $this->discoverBindingsInPath($appContractPath, 'App'));
        }

        // Scan Modules/*/src/Contracts
        $modulesPath = config('modules.paths.modules', base_path('modules'));
        // Use 'app_folder' from config which should be 'src/' now
        $moduleAppPath = config('modules.paths.app_folder', 'src/');

        try {
            foreach (new \DirectoryIterator($modulesPath) as $moduleDir) {
                if ($moduleDir->isDir() && !$moduleDir->isDot()) {
                    $moduleName = $moduleDir->getBasename();
                    // Assuming namespace convention Modules\ModuleName
                    $moduleBaseNamespace = config('modules.namespace', 'Modules') . '\\' . $moduleName;

                    // Correctly construct path: modules/ModuleName/src/Contracts
                    $moduleContractPath = $moduleDir->getPathname() . '/' . trim($moduleAppPath, '/') . '/Contracts';

                    if (file_exists($moduleContractPath)) {
                        $bindings = array_merge($bindings, $this->discoverBindingsInPath($moduleContractPath, $moduleBaseNamespace));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("AutoBindingServiceProvider: Failed to scan module directories. Error: " . $e->getMessage());
        }

        return $bindings;
    }

    /**
     * Get the manually configured service bindings.
     *
     * @return array<string, string>
     */
    protected function getBindings(): array
    {
        return config('bindings', []);
    }

    /**
     * Discovers bindings (interface to concrete) within a given contract path by scanning PHP files recursively.
     *
     * @param string $contractPath The path to the contracts directory.
     * @param string $baseNamespace The base namespace for the contracts (e.g., 'App', 'Modules\User').
     * @return array<string, string>
     */
    private function discoverBindingsInPath(string $contractPath, string $baseNamespace): array
    {
        $discoveredBindings = [];
        if (!is_dir($contractPath)) {
            return $discoveredBindings;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($contractPath));

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getPathname();

            try {
                $content = @file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                if (!preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
                    // Fallback to base namespace if no namespace declaration found (unlikely for valid PHP code)
                    $fileNamespace = $baseNamespace;
                } else {
                    $fileNamespace = trim($namespaceMatches[1]);
                }

                if (!preg_match('/^interface\s+(\w+)\b/m', $content, $interfaceMatches)) {
                    continue;
                }
                $interfaceName = trim($interfaceMatches[1]);

                $abstract = $fileNamespace . '\\' . $interfaceName;

                if (!interface_exists($abstract)) {
                    continue;
                }

                // Determine the root namespace for the module/app to find concrete classes
                // If namespace is 'Modules\User\Contracts\Services', we want 'Modules\User'
                // If namespace is 'Modules\User\src\Contracts', we want 'Modules\User' (assuming PSR-4 maps 'src' to 'Modules\User')

                // Strategy: Find where 'Contracts' starts and take everything before it.
                $contractsPos = strpos($fileNamespace, 'Contracts');
                if ($contractsPos !== false) {
                    // Get 'Modules\User' from 'Modules\User\Contracts...'
                    // Remove trailing backslash if exists
                    $rootNamespace = rtrim(substr($fileNamespace, 0, $contractsPos), '\\');
                } else {
                    $rootNamespace = $baseNamespace;
                }

                $concrete = $this->deriveConcreteClass($abstract, $rootNamespace, $interfaceName);

                if ($concrete) {
                    $discoveredBindings[$abstract] = $concrete;
                }
            } catch (\Throwable $e) {
                Log::error("AutoBindingServiceProvider: Failed to process file '{$filePath}'. Error: " . $e->getMessage());
                continue;
            }
        }
        return $discoveredBindings;
    }

    /**
     * Derives the concrete class name from an abstract (interface) name based on convention.
     *
     * @param string $abstract The fully qualified name of the interface.
     * @param string $rootNamespace The root namespace of the module or app (e.g. 'Modules\User').
     * @param string $interfaceName The short name of the interface.
     * @return string|null The fully qualified name of the potential concrete class.
     */
    private function deriveConcreteClass(string $abstract, string $rootNamespace, string $interfaceName): ?string
    {
        // 1. Prepare the interface short name (remove Interface/Contract suffix).
        $shortName = $interfaceName;
        if (Str::endsWith($interfaceName, 'Interface')) {
            $shortName = Str::replaceLast('Interface', '', $interfaceName);
        } elseif (Str::endsWith($interfaceName, 'Contract')) {
            $shortName = Str::replaceLast('Contract', '', $interfaceName);
        }

        // Define potential naming patterns for concrete classes
        // We look in standard locations relative to the root namespace
        $potentialConcreteClassNames = [
            // Services
            $rootNamespace . '\\Services\\' . $shortName,
            $rootNamespace . '\\Services\\' . $shortName . 'Service', // e.g. User -> UserService

            // Repositories
            $rootNamespace . '\\Repositories\\Eloquent' . $shortName,
            $rootNamespace . '\\Repositories\\' . $shortName,
            $rootNamespace . '\\Repositories\\' . $shortName . 'Repository', // e.g. User -> UserRepository

            // Direct implementation (e.g. Models or root classes)
            $rootNamespace . '\\' . $shortName,
        ];

        // Filter and check existence
        foreach ($potentialConcreteClassNames as $potentialConcrete) {
            if (class_exists($potentialConcrete) && !interface_exists($potentialConcrete)) {
                return $potentialConcrete;
            }
        }

        return null;
    }
}
