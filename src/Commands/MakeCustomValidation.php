<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Davion190510\MiniCRUDGenerator\Commands\MakeCustomCommon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customValidation {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make and Validation class';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreRequest()
    {
        return __DIR__ . '/stubs/customStoreValidation.stub';
    }

    public function getUpdateRequest()
    {
        return __DIR__ . '/stubs/customUpdateValidation.stub';
    }

    public function getDeleteRequest()
    {
        return __DIR__ . '/stubs/customDeleteValidation.stub';
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStubStoreRequestVariables()
    {
        $projectName = $this->makeCustomCommon->filterProjectName($this->getSingularClassName($this->argument('name')));
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $requestName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $storeUpdateRequest = substr($requestName, 0, -7);
        return [
            'NAMESPACE' => "$projectName\\$pathName\\$folderName\\Validation",
            'FOLDER_NAME' => $folderName,
            'PROJECT_NAME' => $projectName,
            'CAPITAL' => $storeUpdateRequest,
        ];
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreRequestSourceFile()
    {
        return $this->getStubStoreRequestContents($this->getStoreRequest(), $this->getStubStoreRequestVariables());
    }

    public function getUpdateRequestSourceFile()
    {
        return $this->getStubUpdateRequestContents($this->getUpdateRequest(), $this->getStubStoreRequestVariables());
    }

    public function getDeleteRequestSourceFile()
    {
        return $this->getStubDeleteRequestContents($this->getDeleteRequest(), $this->getStubStoreRequestVariables());
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStubStoreRequestContents($stub, $stubVariables = []): string
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubUpdateRequestContents($stub, $stubVariables = []): string
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubDeleteRequestContents($stub, $stubVariables = []): string
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreRequestFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $requestName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . $pathName . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Validation") . DIRECTORY_SEPARATOR . "Store" . $requestName . ".php";
    }

    public function getUpdateRequestFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $requestName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . $pathName . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Validation") . DIRECTORY_SEPARATOR . "Update" . $requestName . ".php";
    }

    public function getDeleteRequestFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->getSingularClassName($this->argument('name')));
        $requestName = $this->makeCustomCommon->filterMainName($this->getSingularClassName($this->argument('name')));
        $pathName = $this->makeCustomCommon->filterApiName($this->getSingularClassName($this->argument('name')));
        $moduleName = $this->makeCustomCommon->filterModuleName($this->argument('name'));
        return base_path($moduleName . DIRECTORY_SEPARATOR . $pathName . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . "Validation") . DIRECTORY_SEPARATOR . "Delete" . $requestName . ".php";
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    //Make Directory For custom Artisan
    protected $files;

    public function __construct(
        Filesystem $files,
        private MakeCustomCommon $makeCustomCommon,
    ) {
        parent::__construct();
        $this->files = $files;
    }

    protected function makeDirectory($path): mixed
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $path = $this->getStoreRequestFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getStoreRequestSourceFile();

        $pathTwo = $this->getUpdateRequestFilePath();
        $this->makeDirectory(dirname($path));
        $contentTwo = $this->getUpdateRequestSourceFile();

        $pathThree = $this->getDeleteRequestFilePath();
        $this->makeDirectory(dirname($path));
        $contentThree = $this->getDeleteRequestSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->files->put($pathTwo, $contentTwo);
            $this->files->put($pathThree, $contentThree);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }
}