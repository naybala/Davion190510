<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Davion190510\MiniCRUDGenerator\Commands\MakeCustomCommon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomService extends Command
{
    //get customService.stub
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customService {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make and Service class';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getService()
    {
        return __DIR__ . '/stubs/customService.stub';
    }

    public function getMobileService()
    {
        return __DIR__ . '/stubs/customMobileService.stub';
    }

    public function getSpaService()
    {
        return __DIR__ . '/stubs/customSpaService.stub';
    }

    public function getStubServiceVariables()
    {
        $projectName = $this->makeCustomCommon->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $serviceName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $service = substr($serviceName, 0, -7); // Remove "Service"
        $capital = $service;
        $serviceCamel = lcfirst($capital);

        return [
            'NAMESPACE' => "$projectName\\$pathName\\$folderName\\Services",
            'CLASS_NAME' => $serviceName,
            'FOLDER_NAME' => $folderName,
            'PROJECT_NAME' => $projectName,
            'PATH_NAME' => $pathName,
            'CAMEL_CASE' => $serviceCamel,
            'CAPITAL' => $capital,
        ];
    }

    public function getServiceSourceFile()
    {
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $result = null;
        switch ($pathName) {
            case "Web":
                $result = $this->getStubServiceContents($this->getService(), $this->getStubServiceVariables());
                break;
            case "Mobile":
                $result = $this->getStubServiceContents($this->getMobileService(), $this->getStubServiceVariables());
                break;
            case "Spa":
                $result = $this->getStubServiceContents($this->getSpaService(), $this->getStubServiceVariables());
                break;
            default:
                "Hee Hee";
        }
        return $result;
    }

    public function getStubServiceContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getServiceFilePath()
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $serviceName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . $pathName . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Services") . DIRECTORY_SEPARATOR . $serviceName . ".php";
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
        $path = $this->getServiceFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getServiceSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
}
