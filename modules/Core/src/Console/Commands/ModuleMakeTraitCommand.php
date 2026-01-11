<?php

namespace Modules\Core\Console\Commands;

use Modules\Core\Console\Concerns\HandlesModuleMakeGenerator;
use Nwidart\Modules\Commands\Make\GeneratorCommand;
use Nwidart\Modules\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ModuleMakeTraitCommand
 */
class ModuleMakeTraitCommand extends GeneratorCommand
{
    use HandlesModuleMakeGenerator;

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
     * The configuration key for the command.
     *
     * @var string
     */
    protected $configKey = 'traits';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return '/trait.stub';
    }

    /**
     * Get the template contents for the generator.
     *
     * @return string
     */
    protected function getTemplateContents(): string
    {
        return (new Stub($this->getStub(), [
            'NAMESPACE' => $this->getTargetNamespace(),
            'CLASS' => $this->getTargetName(),
        ]))->render();
    }

    /**
     * Get the destination file path for the generated trait.
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
            ['name', InputArgument::REQUIRED, 'The name of the trait. Subdirectories are allowed (e.g., Concerns/MyTrait).'],
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
            ['force', null, InputOption::VALUE_NONE, 'Create the trait even if the trait already exists.'],
        ];
    }
}
