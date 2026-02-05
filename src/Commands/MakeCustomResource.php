<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Davion190510\MiniCRUDGenerator\Commands\MakeCustomCommon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customResource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getResource()
    {
        return __DIR__ . '/stubs/customResource.stub';
    }

    public function getStubResourceVariables()
    {
        $projectName = $this->makeCustomCommon->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $serviceName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $service = substr($serviceName, 0, -8);
        $capital = $service;
        return [
            //namespace Garment\Web\Sizes\Services;
            'NAMESPACE' => "$projectName\\$pathName\\$folderName\\Resources",
            'ClASS' => $capital,
        ];
    }

    public function getResourceSourceFile()
    {
        return $this->getStubResourceContents($this->getResource(), $this->getStubResourceVariables());
    }

    public function getStubResourceContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getResourceFilePath()
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $resourceName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . "$pathName" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Resources") . DIRECTORY_SEPARATOR . $resourceName . ".php";
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
        $path = $this->getResourceFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getResourceSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
}
