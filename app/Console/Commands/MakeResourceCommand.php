<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeResourceCommand extends Command
{
    protected $signature = 'make:resource {name} {--no-vue : Skip Vue component generation} {--no-service : Skip service generation}';
    protected $description = 'Generate a complete resource with controller, service, requests, and Vue components following project standards';

    public function handle()
    {
        $name = $this->argument('name');
        $modelName = Str::studly($name);
        $modelVariable = Str::camel($name);
        $modelVariablePlural = Str::camel(Str::plural($name));
        $modelPlural = Str::snake(Str::plural($name));
        $modelSnake = Str::snake($name);

        $this->info("Generating resource files for: {$modelName}");

        // Generate Controller
        $this->generateController($modelName, $modelVariable, $modelVariablePlural, $modelPlural);

        // Generate Service
        if (!$this->option('no-service')) {
            $this->generateService($modelName, $modelVariable, $modelVariablePlural, $modelSnake);
        }

        // Generate Requests
        $this->generateRequests($modelName, $modelPlural);

        // Generate Vue Components
        if (!$this->option('no-vue')) {
            $this->generateVueComponents($modelName, $modelVariable, $modelVariablePlural, $modelPlural);
        }

        // Generate Route suggestion
        $this->generateRoutesSuggestion($modelPlural);

        $this->info('✅ Resource generation completed!');
        $this->newLine();
        $this->info('Next steps:');
        $this->info('1. Add the routes to your routes/web.php file');
        $this->info('2. Create and run the migration for the model');
        $this->info('3. Update the model with relationships and fillable fields');
        $this->info('4. Register the service in a service provider if needed');
    }

    protected function generateController($modelName, $modelVariable, $modelVariablePlural, $modelPlural)
    {
        $stub = File::get(base_path('stubs/controller.resource.stub'));
        
        $stub = str_replace([
            '{{ namespace }}',
            '{{ namespacedModel }}',
            '{{ class }}',
            '{{ model }}',
            '{{ modelVariable }}',
            '{{ modelVariablePlural }}',
            '{{ modelPlural }}'
        ], [
            'App\\Http\\Controllers',
            "App\\Models\\{$modelName}",
            "{$modelName}Controller",
            $modelName,
            $modelVariable,
            $modelVariablePlural,
            $modelPlural
        ], $stub);

        $path = app_path("Http/Controllers/{$modelName}Controller.php");
        File::put($path, $stub);
        
        $this->info("✅ Controller created: {$path}");
    }

    protected function generateService($modelName, $modelVariable, $modelVariablePlural, $modelSnake)
    {
        $stub = File::get(base_path('stubs/service.stub'));
        
        $stub = str_replace([
            '{{ class }}',
            '{{ model }}',
            '{{ modelVariable }}',
            '{{ modelVariablePlural }}',
            '{{ modelSnake }}'
        ], [
            $modelName,
            $modelName,
            $modelVariable,
            $modelVariablePlural,
            $modelSnake
        ], $stub);

        $path = app_path("Services/{$modelName}Service.php");
        File::put($path, $stub);
        
        $this->info("✅ Service created: {$path}");
    }

    protected function generateRequests($modelName, $modelPlural)
    {
        // Store Request
        $storeStub = File::get(base_path('stubs/request.store.stub'));
        $storeStub = str_replace([
            '{{ namespace }}',
            '{{ class }}',
            '{{ modelPlural }}'
        ], [
            'App\\Http\\Requests',
            "Store{$modelName}Request",
            $modelPlural
        ], $storeStub);

        $storePath = app_path("Http/Requests/Store{$modelName}Request.php");
        File::put($storePath, $storeStub);
        $this->info("✅ Store Request created: {$storePath}");

        // Update Request
        $updateStub = File::get(base_path('stubs/request.update.stub'));
        $updateStub = str_replace([
            '{{ namespace }}',
            '{{ class }}',
            '{{ modelPlural }}'
        ], [
            'App\\Http\\Requests',
            "Update{$modelName}Request",
            $modelPlural
        ], $updateStub);

        $updatePath = app_path("Http/Requests/Update{$modelName}Request.php");
        File::put($updatePath, $updateStub);
        $this->info("✅ Update Request created: {$updatePath}");
    }

    protected function generateVueComponents($modelName, $modelVariable, $modelVariablePlural, $modelPlural)
    {
        $vueDir = resource_path("js/Pages/{$modelName}");
        
        if (!File::exists($vueDir)) {
            File::makeDirectory($vueDir, 0755, true);
        }

        // Index Component
        $indexStub = File::get(base_path('stubs/vue.index.stub'));
        $indexStub = str_replace([
            '{{ modelVariable }}',
            '{{ modelVariablePlural }}',
            '{{ modelPlural }}'
        ], [
            $modelVariable,
            $modelVariablePlural,
            $modelPlural
        ], $indexStub);

        $indexPath = "{$vueDir}/Index.vue";
        File::put($indexPath, $indexStub);
        $this->info("✅ Vue Index component created: {$indexPath}");

        // Create Component
        $createStub = File::get(base_path('stubs/vue.form.stub'));
        $createStub = str_replace([
            '{{ modelVariable }}',
            '{{ modelVariablePlural }}',
            '{{ modelPlural }}'
        ], [
            $modelVariable,
            $modelVariablePlural,
            $modelPlural
        ], $createStub);

        $createPath = "{$vueDir}/Create.vue";
        File::put($createPath, $createStub);
        $this->info("✅ Vue Create component created: {$createPath}");

        // Edit Component (same as create)
        $editPath = "{$vueDir}/Edit.vue";
        File::put($editPath, $createStub);
        $this->info("✅ Vue Edit component created: {$editPath}");
    }

    protected function generateRoutesSuggestion($modelPlural)
    {
        $routes = "
// Add these routes to your routes/web.php file:
Route::resource('{$modelPlural}', {$this->argument('name')}Controller::class);
Route::post('{$modelPlural}/{{$modelPlural}}/activate', [{$this->argument('name')}Controller::class, 'activate'])->name('{$modelPlural}.activate');
";

        $this->newLine();
        $this->info('📝 Suggested routes:');
        $this->line($routes);
    }
} 