<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Facades\Module;
use Nwidart\Modules\Module as ModuleModel;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ModuleMakeInterfaceCommand
 */
class ModuleMakeInterfaceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface for the specified module, with a direct namespace.';

    /**
     * The argument name of the module.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return '/interface.stub';
    }

    /**
     * Get the template contents for the generator.
     */
    protected function getTemplateContents(): string
    {
        $module = $this->getModule();

        // Extract the base class name from the full name argument
        $interfaceName = Str::afterLast($this->argument('name'), '/');

        return (new Stub($this->getStub(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'INTERFACE' => Str::studly($interfaceName),
        ]))->render();
    }

    /**
     * Get the namespace of the class.
     *
     * @param  ModuleModel  $module
     */
    public function getClassNamespace($module): string
    {
        // Modules\Example
        $namespace = $this->getModuleNamespace($module);

        // Get the base namespace for interfaces from the modules config, e.g., "Contracts"
        $interfaceNamespace = GenerateConfigReader::read('interface')->getNamespace();

        // Combine them to get the base interface namespace, e.g., "Modules\Example\Contracts"
        $baseNamespace = $namespace.'\\'.$interfaceNamespace;

        $name = $this->argument('name');

        // If the name argument includes a path, append it to the namespace
        if (Str::contains($name, '/')) {
            $subNamespace = Str::of($name)
                ->beforeLast('/')
                ->replace('/', '\\');

            return $baseNamespace.'\\'.$subNamespace;
        }

        return $baseNamespace;
    }

    /**
     * Get the module's base namespace.
     *
     * @param  ModuleModel  $module
     */
    public function getModuleNamespace($module): string
    {
        $moduleBaseNamespace = config('modules.namespace');

        return $moduleBaseNamespace.'\\'.$module->getStudlyName();
    }

    /**
     * Get the destination file path.
     */
    protected function getDestinationFilePath(): string
    {
        $path = Module::getModulePath($this->argument('module'));

        $generatorPath = GenerateConfigReader::read('interface');

        // This correctly uses the full 'name' argument to create the path
        return $path.$generatorPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the interface. Subdirectories are allowed (e.g., Services/MyInterface).'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the interface even if the interface already exists.'],
        ];
    }

    /**
     * Get the default namespace for the interface.
     */
    public function getDefaultNamespace(): string
    {
        // The namespace is constructed manually in getClassNamespace()
        return '';
    }

    /**
     * Get the module being operated on.
     */
    protected function getModule(): ModuleModel
    {
        return Module::findOrFail($this->argument('module'));
    }

    /**
     * Get the file name.
     */
    protected function getFileName(): string
    {
        // Returns the full name argument, e.g., "Services/ExampleContract"
        return Str::studly($this->argument('name'));
    }
}
