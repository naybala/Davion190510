<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomModel extends Command
{
    ////////////////////////////////////////////////////////////////////////////
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customModel {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make and module class';
    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    //get custom stub from stubs Folder
    public function getStubPath()
    {
        return __DIR__ . '/stubs/customModel.stub';
    }
    // public function getProviderPath()
    // {
    //     return __DIR__ . '/stubs/customProvider.stub';
    // }

    // public function getRepositoryInterfacePath()
    // {
    //     return __DIR__ . '/stubs/customRepositoryInterface.stub';
    // }

    /////////////////////////////////////////////////////////////

    private function filterProjectName($names)
    {
        $newPosition = strpos($names, "~");
        $projectName = substr($names, $newPosition + 1);
        $projectPosition = strpos($projectName, '.');
        $finalProjectName = substr($projectName, 0, $projectPosition);
        return $finalProjectName;
    }

    private function filterFolderName($names)
    {
        $projectPosition = strpos($names, '.');
        $position = strpos($names, '/');
        $folderName = substr($names, 0, $position);
        $folderName = substr($folderName, $projectPosition + 1);
        return $folderName;
    }

    private function filterModelName($names)
    {
        $position = strpos($names, '/');
        $modelName = substr($names, $position + 1);
        return $modelName;
    }

    private function filterModuleName($names)
    {
        $position = strpos($names, '~');
        $moduleName = substr($names, 0, $position);
        return $moduleName;
    }

    //get Name Space And Class Name
    public function getStubVariables()
    {
        $projectName = $this->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->filterFolderName($this->getSingularClassName($this->argument('name')));
        $modelName = $this->filterModelName($this->getSingularClassName($this->argument('name')));
        return [
            'NAMESPACE' => "$projectName\\Foundations\\Domain\\$folderName",
            'CLASS_NAME' => $modelName,
        ];
    }



    /////////////////////////////////////////////////////////////

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /////////////////////////////////////////////////////////////
    //get ALl Content form Stub
    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    /////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////

    public function getSourceFilePath()
    {
        $folderName = $this->filterFolderName($this->getSingularClassName($this->argument('name')));
        $modelName = $this->filterModelName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . "Foundations" . DIRECTORY_SEPARATOR . "Domain" . DIRECTORY_SEPARATOR . $folderName) . DIRECTORY_SEPARATOR . $modelName . '.php';
    }

    /////////////////////////////////////////////////////////////

    //Make Directory For custom Artisan
    protected $files;

    public function __construct(Filesystem $files)
    {
        ini_set('memory_limit', -1);
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
    /////////////////////////////////////////////////////////////

    //Last Final Execute
    public function handle()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));
        $contents = $this->getSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
    /////////////////////////////////////////////////////////////
}
