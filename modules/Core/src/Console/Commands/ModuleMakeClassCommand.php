<?php

namespace Modules\Core\Console\Commands;

use Modules\Core\Concerns\Console\Commands\GeneratesModulePaths;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeClassCommand extends GeneratorCommand
{
    use GeneratesModulePaths;

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

    public function getClassNamespace($module): string
    {
        return $this->getComponentNamespace($module, 'class', $this->argument('name'));
    }

    protected function getDestinationFilePath(): string
    {
        $module = $this->getModule();

        return $this->getComponentDestinationFilePath($module, 'class', $this->argument('name'));
    }

    protected function getFileName(): string
    {
        return $this->getBaseFileName($this->argument('name'));
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

        return '';
    }
}
