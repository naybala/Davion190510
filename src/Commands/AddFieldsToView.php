<?php

namespace Davion190510\MiniCRUDGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class AddFieldsToView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-fields-to-view {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Blade view files (create, edit, show) based on the migration for a given model.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = $this->option('model');

        if (!$modelName) {
            $this->error('Please provide a model name using --model=ModelName.');
            return 1;
        }

        $migrationPath = database_path('migrations');
        $filesystem = new Filesystem();
        $migrationFiles = $filesystem->files($migrationPath);
        $modelTable = Str::snake(Str::pluralStudly($modelName));
        $targetMigration = null;

        foreach ($migrationFiles as $file) {
            if (Str::contains($file->getFilename(), "create_{$modelTable}_table")) {
                $targetMigration = $file;
                break;
            }
        }

        if (!$targetMigration) {
            $this->error("Migration file for {$modelName} not found.");
            return 1;
        }

        $migrationContent = $filesystem->get($targetMigration->getPathname());
        preg_match_all('/\$table->(\w+)\(([^)]+)\)/', $migrationContent, $matches, PREG_SET_ORDER);

        $fields = [];
        foreach ($matches as $match) {
            $type = $match[1];
            $params = str_replace(['"', '\''], '', $match[2]);
            $fieldName = explode(',', $params)[0];
            $fields[] = ['name' => $fieldName, 'type' => $type];
        }

        // Generate all views
        $views = ['index', 'create', 'edit', 'show'];
        foreach ($views as $view) {
            $viewPath = resource_path("views/admin/" . Str::snake($modelName) . "/{$view}.blade.php");

            if ($filesystem->exists($viewPath)) {
                if (!$this->confirm("The {$view} view file already exists at {$viewPath}. Do you want to overwrite it?")) {
                    $this->info("Skipping {$view} view generation.");
                    continue;
                }
            }

            $viewContent = $this->generateViewContent($modelName, $fields, $view);
            $filesystem->put($viewPath, $viewContent);
            $this->info("{$view} view file generated at {$viewPath}.");
        }

        return 0;
    }

    /**
     * Generate the Blade view content for different types.
     */
    private function generateViewContent(string $modelName, array $fields, string $viewType = 'create'): string
    {
        $modelTitle = Str::title($modelName);
        $smallLetterTitle = strtolower($modelTitle);
        $smallPluralTitle = Str::plural($smallLetterTitle);
    
        $action = $viewType === 'create' ? "{$smallPluralTitle}.store" : "{$smallPluralTitle}.update";
        $method = $viewType === 'edit' ? "@method('PUT')" : '';
        $route = $viewType === 'edit' ? "{{ route('$action', \$data['id']) }}" : "{{ route('$action') }}";
    
        $fieldComponents = '';
        if ($viewType === 'index') {
            $headerFields = [];
            $bodyColumns = '';
            foreach ($fields as $field) {
                $name = $field['name'];
                $type = $field['type'];

                if (in_array($name, ['created_by', 'updated_by', 'deleted_by', 'deleted_at', 'password', 'remember_token', 'email_verified_at'])) {
                    continue;
                }

                $headerFields[] = "'$name'";
                $bodyColumns .= "                            <x-table.body-column :field=\"\$record['$name']\" limit=\"20\" />\n";
            }
            $headerFieldsStr = "[" . implode(', ', $headerFields) . "]";

            return <<<BLADE
<x-master-layout name="{$modelTitle}" headerName="{{ __('sidebar.{$smallLetterTitle}') }}">
   <main class="h-full overflow-y-auto">
        <div class="container px-1 md:px-6 mx-auto grid">
            <div class="container md:flex justify-start md:justify-between items-start mx-auto mt-5">
                <x-common.search keyword="{{ request()->keyword }}" />
                <x-common.create-button route="{$smallPluralTitle}.create" permission="create {$smallPluralTitle}" />
            </div>
            <x-table.wrapper>
                <x-table.header :fields="{$headerFieldsStr}" />
                <x-table.body :checkData="count(\$data['data'])>0">
                    @foreach (\$data['data'] as \$record)
                        <x-table.body-row>
{$bodyColumns}                            <x-table.action-new :id="\$record['id']" field="{$smallPluralTitle}" />
                        </x-table.body-row>
                    @endforeach
                </x-table.body>
            </x-table.wrapper>
            <x-pagination.index route="{$smallPluralTitle}.index" :meta="\$data['meta']" />
        </div>
    </main>
    @vite('resources/js/common/deleteConfirm.js') 
</x-master-layout>
BLADE;
        }

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
    
            if(in_array($name,['created_by','updated_by','deleted_by'])){
                continue;
            }

            // Skip index fields
            if ($type === 'index') {
                continue;
            }
    
            $capitalizeName = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
            $valueBinding = $viewType === 'edit' ? ":value=\"\$data['{$name}']\"" : '';

            if($viewType==='show'){
                $valueBinding = ":data=\"\$data['{$name}']\"";
                $fieldComponents .= "\n                {{-- {$name} --}}\n                <x-show.text-group title='{$smallLetterTitle}.{$name}' {$valueBinding} />\n                {{-- {$name} --}}\n";
            } else{
                //create , edit
                if ($type === 'enum') {
                    $fieldComponents .= "\n                {{-- {$name} --}}\n                <x-form.enum-select title='{$smallLetterTitle}.{$name}' name='{$name}' id='{$name}' enumClass='{$capitalizeName}' {$valueBinding} />\n                {{-- {$name} --}}\n";
                } else {
                    $fieldComponents .= "\n                {{-- {$name} --}}\n                <x-form.input-group title='{$smallLetterTitle}.{$name}' name='{$name}' id='{$name}' {$valueBinding} />\n                {{-- {$name} --}}\n";
                }
            }

        }
    
        if($viewType=='show'){
            $id = ":id=\"\$data['id']\"";
    return <<<BLADE
    <x-master-layout name="{$modelTitle}" headerName="{{ __('sidebar.{$smallLetterTitle}') }}">
        <x-form.layout>
            <x-show.go-to-edit model="{$smallPluralTitle}" {$id} />
            <x-form.grid>
                {$fieldComponents} 
            </x-form.grid>
        </x-form.layout>
    </x-master-layout>
    BLADE;
        }

        return <<<BLADE
    <x-master-layout name="{$modelTitle}" headerName="{{ __('sidebar.{$smallLetterTitle}') }}">
        <x-form.layout>
            <form action="{$route}" method="post" enctype="multipart/form-data">
                @csrf
                {$method}
                <x-form.grid>
                    {$fieldComponents}
                </x-form.grid>
                {{-- Save And Cancel --}}
                <x-form.submit :operate="__('messages.save')" :cancel="__('messages.cancel')" url="{$smallPluralTitle}.index" />
                {{-- Save And Cancel --}}
            </form>
        </x-form.layout>
    </x-master-layout>
    BLADE;
    }
    
}
