<?php

namespace Modules\Core\Console\Commands;

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

        $interfaceName = Str::afterLast($this->argument('name'), '/');

        return (new Stub($this->getStub(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => Str::studly($interfaceName),
        ]))->render();
    }

    /**
     * Get the namespace of the class.
     *
     * @param  ModuleModel  $module
     */
    public function getClassNamespace($module): string
    {

        $namespace = $this->getModuleNamespace($module);

        $interfaceNamespace = GenerateConfigReader::read('interfaces')->getNamespace();

        $baseNamespace = $namespace.'\\'.$interfaceNamespace;

        $name = $this->argument('name');

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
        $module = $this->getModule();
        $path = $module->getPath();
        $appFolder = config('modules.paths.app_folder', 'src/');

        $interfaceConfig = GenerateConfigReader::read('interfaces');
        $interfacePath = $interfaceConfig->getPath();

        $relativeInterfacePath = Str::after($interfacePath, $appFolder);
        if ($relativeInterfacePath === $interfacePath) {

            $finalRelativePath = $appFolder.$interfacePath;
        } else {

            $finalRelativePath = $appFolder.$relativeInterfacePath;
        }

        return $path.'/'.$finalRelativePath.'/'.$this->getFileName().'.php';
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

        return Str::studly($this->argument('name'));
    }
}
