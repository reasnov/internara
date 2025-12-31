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

        $namespace = $this->getModuleNamespace($module);

        $classNamespace = GenerateConfigReader::read('class')->getNamespace();

        $baseNamespace = $namespace.'\\'.$classNamespace;

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
     * Get the destination file path.
     */
    protected function getDestinationFilePath(): string
    {
        $module = $this->getModule();
        $path = $module->getPath();
        $appFolder = config('modules.paths.app_folder', 'src/');

        $classConfig = GenerateConfigReader::read('class');
        $classPath = $classConfig->getPath();

        $relativeClassPath = Str::after($classPath, $appFolder);
        if ($relativeClassPath === $classPath) {
            $finalRelativePath = $appFolder.$classPath;
        } else {
            $finalRelativePath = $appFolder.$relativeClassPath;
        }

        return $path.'/'.$finalRelativePath.'/'.$this->getFileName().'.php';
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
