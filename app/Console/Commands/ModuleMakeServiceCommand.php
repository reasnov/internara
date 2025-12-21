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
 * Class ModuleMakeServiceCommand
 *
 * @package App\Console\Commands
 */
class ModuleMakeServiceCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class for the specified module.';

    /**
     * The argument name of the module.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return '/service.stub';
    }

    /**
     * Get the template contents for the generator.
     *
     * @return string
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
     * Get the namespace of the class.
     *
     * @param  ModuleModel  $module
     * @return string
     */
    public function getClassNamespace($module): string
    {
        // Get the base namespace for services from the modules config, e.g., "Services"
        $serviceNamespace = GenerateConfigReader::read('service')->getNamespace();

        // Get the module's base namespace, e.g., "Modules\User"
        $namespace = parent::getClassNamespace($module);

        // Combine them to get the base service namespace, e.g., "Modules\User\Services"
        $baseNamespace = $namespace . '\\' . $serviceNamespace;

        $name = $this->argument('name');

        // If the name argument includes a path, append it to the namespace
        if (Str::contains($name, '/')) {
            $subNamespace = Str::of($name)
                ->beforeLast('/')
                ->replace('/', '\\');

            return $baseNamespace . '\\' . $subNamespace;
        }

        return $baseNamespace;
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = Module::getModulePath($this->argument('module'));

        $generatorPath = GenerateConfigReader::read('service');

        return $path . $generatorPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the service class. Subdirectories are allowed.'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the class already exists.'],
        ];
    }

    /**
     * Get the default namespace for the class.
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        return '';
    }

    /**
     * Get the module being operated on.
     *
     * @return ModuleModel
     */
    protected function getModule(): ModuleModel
    {
        return Module::findOrFail($this->argument('module'));
    }

    /**
     * Get the file name.
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }
}
