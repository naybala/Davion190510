<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Davion190510\MiniCRUDGenerator\Commands\MakeCustomCommon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class MakeCustomView extends Command
{
    protected $signature = 'make:customView {name} {model}';

    protected $description = 'Make Custom View ".admin/users" "User"';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreView()
    {
        return __DIR__ . '/stubs/customViewCreate.stub';
    }

    public function getEditView()
    {
        return __DIR__ . '/stubs/customViewEdit.stub';
    }

    public function getIndexView()
    {
        return __DIR__ . '/stubs/customViewIndex.stub';
    }

    public function getShowView()
    {
        return __DIR__ . '/stubs/customViewShow.stub';
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStubStoreViewVariables()
    {
        $featureName = $this->argument('model');
        $pluralFeatureName = Pluralizer::plural($this->argument('model'));
        return [
            'FEATURE' => $featureName,
            'PLURALIZER_FEATURE' => lcfirst($pluralFeatureName),
            'LOWER_CASE_FEATURE' => lcfirst($featureName),
        ];
    }

    public function getStubEditViewVariables()
    {
        $featureName = $this->argument('model');
        $pluralFeatureName = Pluralizer::plural($this->argument('model'));
        return [
            'FEATURE' => $featureName,
            'PLURALIZER_FEATURE' => lcfirst($pluralFeatureName),
            'LOWER_CASE_FEATURE' => lcfirst($featureName),
        ];
    }

    public function getStubIndexViewVariables()
    {
        $featureName = $this->argument('model');
        $pluralFeatureName = Pluralizer::plural($this->argument('model'));
        return [
            'FEATURE' => $featureName,
            'PLURALIZER_FEATURE' => lcfirst($pluralFeatureName),
            'LOWER_CASE_FEATURE' => lcfirst($featureName),
        ];
    }

    public function getStubShowViewVariables()
    {
        $featureName = $this->argument('model');
        $pluralFeatureName = Pluralizer::plural($this->argument('model'));
        return [
            'FEATURE' => $featureName,
            'PLURALIZER_FEATURE' => lcfirst($pluralFeatureName),
            'LOWER_CASE_FEATURE' => lcfirst($featureName),
        ];
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreViewSourceFile()
    {
        return $this->getStubStoreViewContents($this->getStoreView(), $this->getStubStoreViewVariables());
    }

    public function getEditViewSourceFile()
    {
        return $this->getStubEditViewContents($this->getEditView(), $this->getStubEditViewVariables());
    }

    public function getIndexViewSourceFile()
    {
        return $this->getStubIndexViewContents($this->getIndexView(), $this->getStubIndexViewVariables());
    }

    public function getShowViewSourceFile()
    {
        return $this->getStubShowViewContents($this->getShowView(), $this->getStubShowViewVariables());
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStoreViewFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->argument('name'));
        $subFolderName = $this->makeCustomCommon->filterSubFolderName($this->argument('name'));
        return base_path("resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $subFolderName) . DIRECTORY_SEPARATOR . "create.blade.php";
    }

    public function getEditViewFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->argument('name'));
        $subFolderName = $this->makeCustomCommon->filterSubFolderName($this->argument('name'));
        return base_path("resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $subFolderName) . DIRECTORY_SEPARATOR . "edit.blade.php";
    }

    public function getIndexViewFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->argument('name'));
        $subFolderName = $this->makeCustomCommon->filterSubFolderName($this->argument('name'));
        return base_path("resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $subFolderName) . DIRECTORY_SEPARATOR . "index.blade.php";
    }

    public function getShowViewFilePath(): string
    {
        $folderName = $this->makeCustomCommon->filterFolderName($this->argument('name'));
        $subFolderName = $this->makeCustomCommon->filterSubFolderName($this->argument('name'));
        return base_path("resources" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR . $subFolderName) . DIRECTORY_SEPARATOR . "show.blade.php";
    }
    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStubStoreViewContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubEditViewContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubIndexViewContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubShowViewContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
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

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->getStoreViewFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getStoreViewSourceFile();

        $pathTwo = $this->getEditViewFilePath();
        $this->makeDirectory(dirname($path));
        $contentTwo = $this->getEditViewSourceFile();

        $pathThree = $this->getIndexViewFilePath();
        $this->makeDirectory(dirname($pathThree));
        $contentThree = $this->getIndexViewSourceFile();

        $pathFour = $this->getShowViewFilePath();
        $this->makeDirectory(dirname($pathFour));
        $contentFour = $this->getShowViewSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->files->put($pathTwo, $contentTwo);
            $this->files->put($pathThree, $contentThree);
            $this->files->put($pathFour, $contentFour);
            $this->info("File : {$path} created");
            $this->info("File : {$pathTwo} created");
            $this->info("File : {$pathThree} created");
            $this->info("File : {$pathFour} created");
        } else {
            $this->info("File : {$path} already exits");
            $this->info("File : {$pathTwo} already exits");
            $this->info("File : {$pathThree} already exits");
            $this->info("File : {$pathFour} already exits");
        }
    }
}
