<?php

namespace Modules\Core\Console\Concerns;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Config\GeneratorPath;

/**
 * @mixin \Nwidart\Modules\Commands\Make\GeneratorCommand
 */
trait HandlesModuleMakeGenerator
{
    /**
     * Get the module instance being operated on by the command.
     *
     * @return \Nwidart\Modules\Module The module instance.
     */
    protected function getModule(): \Nwidart\Modules\Module
    {
        return \Nwidart\Modules\Facades\Module::findOrFail($this->argument('module'));
    }

    /**
     * Get the base namespace for the given module.
     *
     * @return string The module's base namespace.
     */
    protected function getModuleNamespace(): string
    {
        $module = $this->getModule();
        $baseNamespace = config('modules.namespace', 'Modules');

        return $baseNamespace.'\\'.$module->getStudlyName();
    }

    /**
     * Get the target name for the generated file, extracting it from the full path if provided.
     *
     * @return string The target name.
     */
    protected function getTargetName(): string
    {
        $name = $this->argument('name');
        return Str::contains($name, '/') ? Str::afterLast($name, '/') : $name;
    }

    /**
     * Get the subdirectory path for the generated file.
     *
     * @return string The target subdirectory path.
     */
    protected function getTargetSubPath(): string
    {
        $name = $this->argument('name');

        if (Str::contains($name, '/')) {
            return $this->normalizePath(Str::beforeLast($name, '/'));
        }

        return '';
    }

    /**
     * Get the full target path for the generated file.
     *
     * @return string The full target path.
     */
    protected function getTargetPath(): string
    {
        $name = $this->argument('name');

        $appPath = $this->getAppPath();
        $basePath = $this->getBasePath();

        if (Str::contains($name, Str::finish($basePath, '/')) && Str::contains($name, '/')) {
            // 'src/Services/Contracts'
            return $appPath . '/' . $this->getTargetSubPath();
        };

        // 'src/Contracts'
        return $this->normalizePath(($appPath . '/' . $basePath));
    }

    /**
     * Get the target namespace for the generated class.
     *
     * @return string The target namespace.
     */
    protected function getTargetNamespace(): string
    {
        $classPath = str_replace(($this->getAppPath() . '/'), '', $this->getTargetPath());

        $moduleNamespace = $this->getModuleNamespace();
        $baseNamespace = str_replace('/', '\\', $classPath);

        // Modules\ModuleName\Example\
        return $moduleNamespace . '\\' . $baseNamespace;
    }

    /**
     * Get the full file path for the generated file.
     *
     * @return string The target file path.
     */
    protected function getTargetFilePath(): string
    {
        $filePath = $this->getTargetPath() . '/' . $this->getTargetName() . '.php';
        return module_path($this->getModule()->getName(), $filePath);
    }

    /**
     * Get the base path for module files.
     *
     * @return string The base path.
     */
    protected function getBasePath(): string
    {
        $path = Str::after($this->getAppPath() . '/', $this->getConfigReader($this->getConfigKey())->getPath()) ?? '';
        return $this->normalizePath($path);
    }

    /**
     * Get the application path for the module.
     *
     * @return string The application path.
     */
    protected function getAppPath(): string
    {
        $appPath = config('modules.paths.app_folder', 'src/');

        return $this->normalizePath($appPath);
    }

    /**
     * Get the configuration key for the generator.
     *
     * @return string The configuration key.
     */
    protected function getConfigKey(): string
    {
        return property_exists($this, 'configKey') ? $this->configKey : 'base';
    }

    /**
     * Get the configuration reader for a given key.
     *
     * @param  string  $key The configuration key.
     * @return \Nwidart\Modules\Support\Config\GeneratorPath The generator path configuration.
     */
    protected function getConfigReader(string $key): GeneratorPath
    {
        return GenerateConfigReader::read($key);
    }

    /**
     * Normalize a given path by removing leading and trailing slashes.
     *
     * @param  string  $path The path to normalize.
     * @return string The normalized path.
     */
    private function normalizePath(string $path): string
    {
        // remove leading '/' if exists
        $path = Str::startsWith($path, '/') ? Str::replaceFirst('/', '', $path) : $path;

        // remove trailing '/' if exists
        $path = Str::endsWith($path, '/') ? Str::replaceLast('/', '', $path) : $path;

        return $path;
    }
}
