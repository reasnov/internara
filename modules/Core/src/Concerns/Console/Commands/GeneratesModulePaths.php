<?php

namespace Modules\Core\Concerns\Console\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Nwidart\Modules\Module as ModuleModel;
use Nwidart\Modules\Support\Config\GenerateConfigReader;

trait GeneratesModulePaths
{
    /**
     * Get the module being operated on.
     */
    protected function getModule(): ModuleModel
    {
        return Module::findOrFail($this->argument('module'));
    }

    /**
     * Get the module's base namespace.
     */
    protected function getModuleNamespace(ModuleModel $module): string
    {
        $baseNamespace = config('modules.namespace', 'Modules');

        return $baseNamespace.'\\'.$module->getStudlyName(); // getStudlyName() returns StudlyCase
    }

    /**
     * Get the namespace of the generated class/interface/trait.
     *
     * @param  string  $configReaderKey  The key used for GenerateConfigReader (e.g., 'class', 'interfaces', 'traits').
     */
    protected function getBaseNamespace(ModuleModel $module, string $configReaderKey): string
    {
        $namespace = $this->getModuleNamespace($module);
        $componentNamespace = GenerateConfigReader::read($configReaderKey)->getNamespace();

        // Only append backslash and componentNamespace if componentNamespace is not empty
        return $namespace.($componentNamespace ? '\\'.$componentNamespace : '');
    }

    /**
     * Construct the full namespace for the generated component (class, interface, trait).
     *
     * @param  string  $configReaderKey  The key used for GenerateConfigReader.
     * @param  string  $nameArgument  The 'name' argument from the console command (can include subdirectories).
     */
    protected function getComponentNamespace(ModuleModel $module, string $configReaderKey, string $nameArgument): string
    {
        $baseNamespace = $this->getBaseNamespace($module, $configReaderKey);

        if (Str::contains($nameArgument, '/')) {
            $subNamespace = Str::of($nameArgument)
                ->beforeLast('/')
                ->replace('/', '\\');

            return $baseNamespace.'\\'.$subNamespace;
        }

        return $baseNamespace;
    }

    /**
     * Get the destination file path for the generated component.
     *
     * @param  string  $configReaderKey  The key used for GenerateConfigReader.
     * @param  string  $nameArgument  The 'name' argument from the console command.
     * @param  string  $fileExtension  The file extension (e.g., '.php').
     */
    protected function getComponentDestinationFilePath(ModuleModel $module, string $configReaderKey, string $nameArgument, string $fileExtension = '.php'): string
    {
        $path = $module->getPath(); // e.g., /path/to/internara/modules/Core

        $componentConfig = GenerateConfigReader::read($configReaderKey);
        $componentPath = $componentConfig->getPath(); // e.g., 'src/Services', or 'src/Concerns/Console/Commands'

        // The nameArgument might contain subdirectories, e.g., 'Subdir/MyClass'
        // We need to extract these subdirectories to append them to the componentPath.
        $subDirectoryFromName = '';
        if (Str::contains($nameArgument, '/')) {
            $subDirectoryFromName = Str::of($nameArgument)
                ->beforeLast('/')
                ->replace('\\', '/') // Ensure forward slashes for path consistency
                ->prepend('/'); // Add leading slash for concatenation
        }

        $fileName = $this->getBaseFileName($nameArgument); // Get just the file name without subdirectories

        return $path.'/'.$componentPath.$subDirectoryFromName.'/'.$fileName.$fileExtension;
    }

    /**
     * Get the file name from the 'name' argument, typically StudlyCase.
     * This helper extracts the base file name from a potential path (e.g., 'Services/MyService' -> 'MyService').
     */
    protected function getBaseFileName(string $nameArgument): string
    {
        return Str::studly(Str::afterLast($nameArgument, '/'));
    }
}
