<?php

namespace Modules\Core\Console\Commands;

use Modules\Core\Console\Concerns\HandlesModuleMakeGenerator;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeClassCommand extends GeneratorCommand
{
    use HandlesModuleMakeGenerator;

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
     * The configuration key for the command.
     *
     * @var string
     */
    protected $configKey = 'class';

    /**
     * Get the stub file path for the generator.
     *
     * @return string The stub file path.
     */
    protected function getStub(): string
    {
        return '/class.stub';
    }

    /**
     * Get the template contents for the generator, populated with dynamic values.
     *
     * @return string The populated template contents.
     */
    protected function getTemplateContents(): string
    {
        return (new Stub($this->getStub(), [
            'NAMESPACE' => $this->getTargetNamespace(),
            'CLASS' => $this->getTargetName(),
        ]))->render();
    }

    /**
     * Get the destination file path for the generated class.
     *
     * @return string The destination file path.
     */
    protected function getDestinationFilePath(): string
    {
        return $this->getTargetFilePath();
    }

    /**
     * Get the console command arguments.
     *
     * @return array The array of arguments.
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
     *
     * @return array The array of options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the class already exists.'],
        ];
    }
}
