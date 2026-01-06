<?php

namespace Modules\Core\Console\Commands;

use Illuminate\Support\Str;
use Modules\Core\Concerns\Console\Commands\GeneratesModulePaths;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ModuleMakeInterfaceCommand
 */
class ModuleMakeInterfaceCommand extends GeneratorCommand
{
    use GeneratesModulePaths;

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

        $interfaceName = Str::afterLast($this->argument('name'), '/');

        return (new Stub($this->getStub(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => Str::studly($interfaceName),
        ]))->render();
    }

    public function getClassNamespace($module): string
    {
        return $this->getComponentNamespace($module, 'interfaces', $this->argument('name'));
    }

    protected function getDestinationFilePath(): string
    {
        $module = $this->getModule();

        return $this->getComponentDestinationFilePath($module, 'interfaces', $this->argument('name'));
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the interface. Subdirectories are allowed (e.g., Services/SomeInterface).'],
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

        return '';
    }

    protected function getFileName(): string
    {
        return $this->getBaseFileName($this->argument('name'));
    }
}
