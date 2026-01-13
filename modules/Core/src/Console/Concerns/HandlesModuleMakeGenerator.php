<?php

namespace Modules\Core\Console\Concerns;

use Illuminate\Support\Str;
use Modules\Shared\Support\Formatter;
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

    protected function getModuleName(): string
    {
        return $this->getModule()->getStudlyName();
    }

    /**
     * Get the base namespace for the given module.
     *
     * @return string The module's base namespace.
     */
    protected function getModuleNamespace(): string
    {
        $moduleName = $this->getModuleName();
        $baseNamespace = config('modules.namespace', 'Modules');

        return Formatter::namespace($baseNamespace, $moduleName);
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
     * Get the target namespace for the generated class.
     *
     * @return string The target namespace.
     */
    protected function getTargetNamespace(): string
    {
        $appPath = Str::finish($this->getAppPath(), '/');
        $targetPath = str_replace($appPath, '', $this->getTargetPath());

        $moduleNamespace = $this->getModuleNamespace();
        $baseNamespace = str_replace('/', '\\', $targetPath);

        return Formatter::namespace($moduleNamespace, $baseNamespace);
    }

    /**
     * Get the full file path for the generated file.
     *
     * @return string The target file path.
     */
    protected function getTargetFilePath(): string
    {
        $filePath = Formatter::path($this->getTargetPath(), $this->getTargetName()).'.php';

        return module_path($this->getModule()->getName(), $filePath);
    }

    /**
     * Get the full target path for the generated file.
     *
     * @return string The full target path.
     */
    protected function getTargetPath(): string
    {
        $appPath = $this->getAppPath();
        $basePath = $this->getBasePath();
        $subPath = $this->getTargetSubPath();

        if ($subPath && Str::contains($subPath, $basePath)) {
            return Formatter::path($appPath, $subPath);
        }

        return Formatter::path($appPath, $basePath, $subPath);
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
            return Formatter::path(Str::beforeLast($name, '/'));
        }

        return '';
    }

    /**
     * Get the base path for module files.
     *
     * @return string The base path.
     */
    protected function getBasePath(): string
    {
        $appPath = Str::finish($this->getAppPath(), '/');
        $basePath = Str::after($this->getConfigReader($this->getConfigKey())->getPath(), $appPath);

        return Formatter::path($basePath);
    }

    /**
     * Get the application path for the module.
     *
     * @return string The application path.
     */
    protected function getAppPath(): string
    {
        $appPath = config('modules.paths.app_folder', 'src/');

        return Formatter::path($appPath);
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
     * @param  string  $key  The configuration key.
     * @return \Nwidart\Modules\Support\Config\GeneratorPath The generator path configuration.
     */
    protected function getConfigReader(string $key): GeneratorPath
    {
        return GenerateConfigReader::read($key);
    }
}
