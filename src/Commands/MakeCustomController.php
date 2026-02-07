<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Davion190510\MiniCRUDGenerator\Commands\MakeCustomCommon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customController {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make and Controller class';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getController()
    {
        return __DIR__ . '/stubs/customController.stub';
    }

    public function getMobileController()
    {
        return __DIR__ . '/stubs/customMobileController.stub';
    }

    public function getSpaController()
    {
        return __DIR__ . '/stubs/customSpaController.stub';
    }

    public function getStubControllerVariables()
    {
        $projectName = $this->makeCustomCommon->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $controllerName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $controller = substr($controllerName, 0, -10); // Remove "Controller"
        $capital = $controller;
        $controllerCamel = lcfirst($capital);
        return [
            'NAMESPACE' => "$projectName\\$pathName\\$folderName\\Controllers",
            'CLASS_NAME' => $controllerName,
            'FOLDER_NAME' => $folderName,
            'PROJECT_NAME' => $projectName,
            'PATH_NAME' => $pathName,
            'CAMEL_CASE' => $controllerCamel,
            'CAPITAL' => $capital,
        ];
    }

    public function getControllerSourceFile()
    {
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $result = null;
        switch ($pathName) {
            case "Web":
                $result = $this->getStubControllerContents($this->getController(), $this->getStubControllerVariables());
                break;
            case "Mobile":
                $result = $this->getStubControllerContents($this->getMobileController(), $this->getStubControllerVariables());
                break;
            case "Spa":
                $result = $this->getStubControllerContents($this->getSpaController(), $this->getStubControllerVariables());
                break;
            default:
                "Hee Hee";
        }
        return $result;
    }

    public function getStubControllerContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getControllerFilePath()
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $controllerName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . $pathName . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Controllers") . DIRECTORY_SEPARATOR . $controllerName . ".php";
    }

    //Make Directory For custom Artisan
    protected $files;

    public function __construct(
        Filesystem $files,
        private MakeCustomCommon $makeCustomCommon,
    ) {
        parent::__construct();
        $this->files = $files;
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $path = $this->getControllerFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getControllerSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
}
