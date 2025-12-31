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

class ModuleMakeClassCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-class';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plain class for the specified module, with a direct namespace.';

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
        return '/class.stub';
    }

    /**
     * Get the template contents for the generator.
     */
    protected function getTemplateContents(): string
    {
        $module = $this->getModule();

        return (new Stub($this->getStub(), [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    /**
     * Get the namespace of the module.
     * It will be based on the module namespace and the optional sub-path from the input name.
     *
     * @param  ModuleModel  $module
     */
    public function getClassNamespace($module): string
    {
        // Modules\Example
        $namespace = $this->getModuleNamespace($module);

        // Get the base namespace for classes from the modules config
        $classNamespace = GenerateConfigReader::read('class')->getNamespace();

        // Combine them to get the base class namespace
        $baseNamespace = $namespace.'\\'.$classNamespace;

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
     * Get the destination file path.
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->getModule()->getPath();
        $generatorPath = GenerateConfigReader::read('class');

        // The getPath() method from the generator config gives us the base directory (e.g., 'src').
        // The getFileName() method will include any subdirectories from the input name.
        return $path.'/'.$generatorPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * Get the file name.
     * This will include any subdirectories from the 'name' argument.
     * e.g., 'Services/MyService'
     */
    public function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }

    public function getModuleNamespace($module): string
    {
        $baseNamespace = config('modules.namespace') ?: 'Modules';

        return $baseNamespace.'\\'.$module->getName();
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class.'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the class already exists.'],
        ];
    }

    /**
     * Get the default namespace for the class.
     */
    public function getDefaultNamespace(): string
    {
        // We return an empty string to prevent the 'Classes' sub-namespace from being
        // automatically appended, allowing for a direct namespace under the module.
        return '';
    }

    /**
     * Get the module being operated on.
     */
    protected function getModule(): ModuleModel
    {
        return Module::findOrFail($this->argument('module'));
    }
}
