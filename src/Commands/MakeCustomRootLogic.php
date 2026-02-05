<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;

class MakeCustomRootLogic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:coreFeature--logic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cmd will make crud logic(Controller , Service , Resource and Validation) for feature';

    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $password = $this->secret('What is the password?(Hint : can you guess my current girlfriend count! It should be probably more than 15!)');
        if ($password < 15) {
            $this->info("");
            $this->info("========================= You don't have access to use this command! You still think of me. (developed by  Davion)==========================");
            $this->info("");
            die();
        }
        $nameSpace = config('minicrud.namespace');
        $module = config('minicrud.modules');
        $this->info("Enter the feature name.It should be plural.");
        $feature = $this->ask('For example (if you implement demo feature the input should be "Demos" )');
        if ($feature != "") {
            $this->info('Put your path root paths of');
            $logic = $this->choice('Controller , Resource , Service and Validation.', ['Mobile', 'Spa', 'Web', 'false'], '0');
            $this->logicRun($module, $logic, $nameSpace, $feature);
        } else {
            $this->info("");
            $this->info("========================= Sorry you can't use all Logic features (developed by  Davion :feat.hfourpsix38)==========================");
            $this->info("");
        }
    }

    private function logicRun($pathName, $logicPath, $nameSpace, $feature)
    {
        $model = ucwords(Pluralizer::singular($feature));
        $controllerCommand = "{$pathName}~{$nameSpace}.{$feature}/{$model}Controller?path={$logicPath}";
        $resourceCommand = "{$pathName}~{$nameSpace}.{$feature}/{$model}Resource?path={$logicPath}";
        $serviceCommand = "{$pathName}~{$nameSpace}.{$feature}/{$model}Service?path={$logicPath}";
        $requestCommand = "{$pathName}~{$nameSpace}.{$feature}/{$model}Request?path={$logicPath}";

        $this->call("make:customController", [
            'name' => $controllerCommand,
        ]);
        $this->call("make:customResource", [
            'name' => $resourceCommand,
        ]);
        $this->call("make:customService", [
            'name' => $serviceCommand,
        ]);
        $this->call("make:customValidation", [
            'name' => $requestCommand,
        ]);
        $this->info("");
        $this->info("========================= Congratulation you unlock all Logic features! (developed by  Davion :feat.hfourpsix38) =========================");
        $this->info("");
    }

}
