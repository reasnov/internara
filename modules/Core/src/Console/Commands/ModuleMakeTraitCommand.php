<?php

namespace Modules\Core\Console\Commands;

use Illuminate\Support\Str;
use Modules\Core\Concerns\Console\Commands\GeneratesModulePaths;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ModuleMakeTraitCommand
 */
class ModuleMakeTraitCommand extends GeneratorCommand
{
    use GeneratesModulePaths;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:make-trait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait for the specified module, with a direct namespace.';

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
        return '/trait.stub';
    }

    /**
     * Get the template contents for the generator.
     */
    protected function getTemplateContents(): string
    {
        $module = $this->getModule();

        // Extract the base class name from the full name argument
        $traitName = Str::afterLast($this->argument('name'), '/');

        return (new Stub($this->getStub(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => Str::studly($traitName),
        ]))->render();
    }

    public function getClassNamespace($module): string
    {
        return $this->getComponentNamespace($module, 'traits', $this->argument('name'));
    }

    protected function getDestinationFilePath(): string
    {
        $module = $this->getModule();

        return $this->getComponentDestinationFilePath($module, 'traits', $this->argument('name'));
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the trait. Subdirectories are allowed (e.g., Concerns/MyTrait).'],
            ['module', InputArgument::REQUIRED, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the trait even if the trait already exists.'],
        ];
    }

    /**
     * Get the default namespace for the trait.
     */
    public function getDefaultNamespace(): string
    {
        // The namespace is constructed manually in getClassNamespace()
        return '';
    }

    protected function getFileName(): string
    {
        return $this->getBaseFileName($this->argument('name'));
    }
}
