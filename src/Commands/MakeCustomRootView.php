<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;

class MakeCustomRootView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:coreFeature--view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cmd will make mini crud views(UI) for feature';

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
        $this->info("Enter the feature name.It should be plural.");
        $feature = $this->ask('Enter Model ( if you implement demo feature the input should be "Demos" )');
        if ($feature != "") {
            $view = $this->ask("Enter the view path(UI directory resources/views/??)['admin/user']");
            $this->viewRun($feature, $view);
        } else {
            $this->info("");
            $this->info("========================= Sorry you can't use all views features (developed by  Davion :feat.hfourpsix38)==========================");
            $this->info("");
        }
    }

    private function viewRun($feature, $view)
    {
        $model = ucwords(Pluralizer::singular($feature));
        $this->call("make:customView", [
            'name' => "." . $view . " ",
            'model' => $model,
        ]);
        $this->info("");
        $this->info("========================= Congratulation you unlock all views features! (developed by  Davion :feat.hfourpsix38) =========================");
        $this->info("");
    }

}
