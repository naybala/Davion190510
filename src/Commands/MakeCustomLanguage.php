<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeCustomLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:customLanguage {name} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cmd will make en and mm file for feature template';

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getEnLang()
    {
        return __DIR__ . '/stubs/customLanguageEn.stub';
    }

    public function getMmLang()
    {
        return __DIR__ . '/stubs/customLanguageMm.stub';
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getLanguageVariables()
    {
        return [
            'FILENAME' => $this->argument('name'),
        ];
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getLanguageEnSourceFile()
    {
        return $this->getStubLangEnContents($this->getEnLang(), $this->getLanguageVariables());
    }

    public function getLanguageMmSourceFile()
    {
        return $this->getStubLangMmContents($this->getMmLang(), $this->getLanguageVariables());
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getLangEnFilePath(): string
    {
        return base_path("lang" . DIRECTORY_SEPARATOR . "en" . DIRECTORY_SEPARATOR . ($this->argument('name') . ".php"));
    }

    public function getLangMmFilePath(): string
    {
        return base_path("lang" . DIRECTORY_SEPARATOR . "mm" . DIRECTORY_SEPARATOR . ($this->argument('name') . ".php"));
    }

    ///////////////////////////////This is Method Divider///////////////////////////////////////////

    public function getStubLangEnContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    public function getStubLangMmContents($stub, $stubVariables = [])
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
     *
     * @return int
     */
    public function handle()
    {
        $smallLetter = $this->argument('name');
        $capitalLetter = ucfirst($this->argument('name'));
        $path = $this->getLangEnFilePath();
        $this->makeDirectory(dirname($path));
        $contents = $this->getLanguageEnSourceFile();

        $pathTwo = $this->getLangMmFilePath();
        $this->makeDirectory(dirname($path));
        $contentTwo = $this->getLanguageMmSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->files->put($pathTwo, $contentTwo);
            $this->info("File : {$path} created");
            $this->info("File : {$pathTwo} created");
        } else {
            $this->info("File : {$path} already exits");
            $this->info("File : {$pathTwo} already exits");
        }
    }
}
