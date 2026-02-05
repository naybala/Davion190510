<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repo {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make and repository class';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function getRepository()
    {
        return __DIR__ . '/stubs/customRepository.stub';
    }

    ////////////////////////////////////////////////////////////

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

    public function getStubRepositoryVariables()
    {
        $projectName = $this->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->filterFolderName($this->getSingularClassName($this->argument('name')));
        $modelName = $this->filterModelName($this->getSingularClassName($this->argument('name')));
        return [
            'NAMESPACE' => "$projectName\\Foundations\\Domain\\$folderName\\Repositories\\Eloquent",
            'PURE_NAME_SPACE' => "$projectName\\Foundations\\Domain",
            'MODEL_PATH' => "$projectName\\Foundations\\Domain\\$folderName",
            'INTERFACE_PATH' => "$projectName\\Foundations\\Domain\\$folderName\\Repositories",
            'CLASS_NAME' => $modelName,
            'PROJECT_NAME' => $projectName,
            'FOLDER_NAME' => $folderName,
            'PlURAl_CLASS_NAME' => Pluralizer::plural($modelName),
        ];
    }

    ////////////////////////////////////////////////////////////

    public function getRepositorySourceFile()
    {
        return $this->getStubRepositoryContents($this->getRepository(), $this->getStubRepositoryVariables());
    }

    public function getStubRepositoryContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getRepositoryFilePath()
    {
        $folderName = $this->filterFolderName($this->getSingularClassName($this->argument('name')));
        $modelName = $this->filterModelName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . "Foundations" . DIRECTORY_SEPARATOR . "Domain" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Repositories" . DIRECTORY_SEPARATOR . "Eloquent") . DIRECTORY_SEPARATOR . $modelName . "Repository.php";
    }

    //Make Directory For custom Artisan
    protected $files;

    public function __construct(Filesystem $files)
    {
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
        $path = $this->getRepositoryFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getRepositorySourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
}
