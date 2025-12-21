<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modules\Support\General\Filter;
use Illuminate\Support\Facades\Log;

class AutoBindingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = Filter::make(array_merge(
            $this->getDefaultBindings(),
            $this->getBindings()
        ))->clean()->get();

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

        // Scan App/Contracts
        $appContractPath = app_path('Contracts');
        if (file_exists($appContractPath)) {
            $bindings = array_merge($bindings, $this->discoverBindingsInPath($appContractPath, 'App'));
        }

        // Scan Modules/*/Contracts
        $modulesPath = base_path('modules');
        try {
            foreach (new \DirectoryIterator($modulesPath) as $moduleDir) {
                if ($moduleDir->isDir() && !$moduleDir->isDot()) {
                    $moduleName = $moduleDir->getBasename();
                    $moduleBaseNamespace = 'Modules\\' . $moduleName;
                    $moduleContractPath = base_path("modules/{$moduleName}/app/Contracts");

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
     * Discovers bindings (interface to concrete) within a given contract path by scanning PHP files.
     *
     * @param string $contractPath The path to the contracts directory.
     * @param string $baseNamespace The base namespace for the contracts (e.g., 'App', 'Modules\User').
     * @return array<string, string>
     */
    private function discoverBindingsInPath(string $contractPath, string $baseNamespace): array
    {
        $discoveredBindings = [];
        if (!is_dir($contractPath)) {
            Log::debug("AutoBindingServiceProvider: Contract path '{$contractPath}' does not exist. Skipping.");
            return $discoveredBindings;
        }

        $phpFiles = glob($contractPath . '/**/*.php');

        foreach ($phpFiles as $filePath) {
            try {
                $content = @file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                if (!preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
                    $fileNamespace = $baseNamespace;
                } else {
                    $fileNamespace = trim($namespaceMatches[1]);
                }

                if (!preg_match('/^interface\s+(\w+)\b/m', $content, $interfaceMatches)) {
                    Log::debug("AutoBindingServiceProvider: No interface found in file '{$filePath}'. Skipping.");
                    continue;
                }
                $interfaceName = trim($interfaceMatches[1]);

                $abstract = $fileNamespace . '\\' . $interfaceName;

                if (!interface_exists($abstract)) {
                    continue;
                }

                $concrete = $this->deriveConcreteClass($abstract, $fileNamespace, $interfaceName);

                if ($concrete && class_exists($concrete) && !interface_exists($concrete)) {
                    $discoveredBindings[$abstract] = $concrete;
                } else {
                    Log::debug("AutoBindingServiceProvider: Could not find valid concrete for '{$abstract}' (tried '{$concrete}' or multiple patterns). Skipping.");
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
     * This method correctly handles deep subdirectories in the Contracts namespace and removes the
     * 'App' segment only for module namespaces (Modules\...). Repository patterns are prioritized
     * with the non-Eloquent class first.
     *
     * @param string $abstract The fully qualified name of the interface.
     * @param string $fileNamespace The namespace found in the contract file.
     * @param string $interfaceName The short name of the interface.
     * @return string|null The fully qualified name of the potential concrete class.
     */
    private function deriveConcreteClass(string $abstract, string $fileNamespace, string $interfaceName): ?string
    {
        // 1. Find the base namespace (App or Modules\X) by cutting off 'Contracts' and any subdirs.
        $contractsPosition = strpos($fileNamespace, 'Contracts');

        if ($contractsPosition === false) {
            $potentialConcreteNamespaceBase = $fileNamespace;
        } else {
            // Cut the FQCN right before the 'Contracts' segment and the preceding backslash.
            $potentialConcreteNamespaceBase = Str::substr($fileNamespace, 0, $contractsPosition - 1);
        }

        // 2. Prepare the interface short name.
        $shortenedInterfaceName = $interfaceName;

        if (Str::endsWith($interfaceName, 'Interface')) {
            $shortenedInterfaceName = Str::replaceLast('Interface', '', $interfaceName);
        } elseif (Str::endsWith($interfaceName, 'Contract')) {
            $shortenedInterfaceName = Str::replaceLast('Contract', '', $interfaceName);
        }

        $potentialConcreteClassNames = [];

        // Pattern 1 & 2: Repositories (PRIORITY SWAPPED: General first, then Eloquent)
        $potentialConcreteClassNames[] = $potentialConcreteNamespaceBase . '\\Repositories\\' . $shortenedInterfaceName;
        $potentialConcreteClassNames[] = $potentialConcreteNamespaceBase . '\\Repositories\\Eloquent' . $shortenedInterfaceName;

        // Pattern 3 & 4: Services
        $potentialConcreteClassNames[] = $potentialConcreteNamespaceBase . '\\Services\\Eloquent' . $shortenedInterfaceName;
        $potentialConcreteClassNames[] = $potentialConcreteNamespaceBase . '\\Services\\' . $shortenedInterfaceName;

        // Pattern 5: Direct match in the base namespace
        $potentialConcreteClassNames[] = $potentialConcreteNamespaceBase . '\\' . $shortenedInterfaceName;

        // Pattern 6: Generic replacement of 'Contracts' segment from abstract FQCN (as a fallback).
        $genericConcreteFromAbstract = Str::replaceFirst('\\Contracts\\', '\\', $abstract);
        $potentialConcreteClassNames[] = $genericConcreteFromAbstract;


        $potentialConcreteClassNames = array_unique(array_filter($potentialConcreteClassNames));

        foreach ($potentialConcreteClassNames as $potentialConcrete) {
            if (is_string($potentialConcrete) && $potentialConcrete !== '' && class_exists($potentialConcrete) && !interface_exists($potentialConcrete)) {
                return $potentialConcrete;
            }
        }

        return null;
    }
}
