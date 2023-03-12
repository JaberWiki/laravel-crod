<?php

namespace Milwad\LaravelCrod\Commands\Modules;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Milwad\LaravelCrod\Facades\LaravelCrodServiceFacade;
use Milwad\LaravelCrod\Traits\CommonTrait;
use Milwad\LaravelCrod\Traits\StubTrait;

class MakeCrudModuleCommand extends Command
{
    use StubTrait, CommonTrait;

    protected $signature = 'crud:make-module {module_name}';

    protected $description = 'Command description';

    private string $module_name_space;

    public Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
        $this->module_name_space = config('laravel-crod.modules.module_namespace', 'Modules');
    }

    public function handle()
    {
        $this->alert('Publishing crud files for module...');

        $name = $this->argument('module_name');

        $this->makeModel($name);
        $this->makeMigration($name);
        $this->makeController($name);
        $this->makeRequest($name);
        $this->makeView($name);
        $this->makeProvider($name);

        /*
         * When all files created, after say to user need to make more files like: factory, seeder, etc.
         */
        $this->extraOptionOperation($name);

        $this->info('Crud files successfully generated...');
    }

    /**
     *  Build model file with call command for module.
     *
     *
     * @return void
     */
    private function makeModel(string $name)
    {
        $model = config('laravel-crod.modules.model_path', 'Entities');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$model",
            $name,
            '',
            '/../Stubs/module/model.stub'
        );
    }

    /**
     * Build migration file with call command.
     *
     *
     * @return void
     */
    private function makeMigration(string $name)
    {
        $migrationPath = config('laravel-crod.modules.migration_path', 'Database\Migrations');
        $path = "$this->module_name_space\\$name\\$migrationPath";

        $this->call('make:migration', [
            'name' => LaravelCrodServiceFacade::getCurrentNameWithCheckLatestLetter($name),
            '--path' => $path,
            '--create',
        ]);
    }

    /**
     * Build controller file with call command for module.
     *
     *
     * @return void
     */
    private function makeController(string $name)
    {
        $controllerPath = config('laravel-crod.modules.controller_path', 'Http\Controllers');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$controllerPath",
            $name,
            'Controller',
            '/../Stubs/module/controller.stub'
        );
    }

    /**
     * Build request file with call command for module.
     *
     *
     * @return void
     */
    private function makeRequest(string $name)
    {
        $requestPath = config('laravel-crod.modules.request_path', 'Http\Requests');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$requestPath",
            $name,
            'StoreRequest',
            '/../Stubs/module/request.stub'
        );
        $this->makeStubFile(
            $this->module_name_space."\\$name\\$requestPath",
            $name,
            'UpdateRequest',
            '/../Stubs/module/request.stub'
        );
    }

    /**
     * Build view file with call command for module.
     *
     *
     * @return void
     */
    private function makeView(string $name)
    {
        $viewPath = config('laravel-crod.modules.view_path', 'Resources/Views');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$viewPath",
            LaravelCrodServiceFacade::getCurrentNameWithCheckLatestLetter($name),
            '.blade',
            '/../Stubs/module/blade.stub',
            false,
        );
    }

    /**
     * Build provider for module.
     *
     *
     * @return void
     */
    private function makeProvider(string $name)
    {
        $providerPath = config('laravel-crod.modules.provider_path', 'Providers');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$providerPath",
            LaravelCrodServiceFacade::getCurrentNameWithCheckLatestLetter($name, false),
            'ServiceProvider',
            '/../Stubs/module/provider.stub',
        );
    }

    /**
     * Build service file with call command for module.
     *
     *
     * @return void
     */
    private function makeService(string $name)
    {
        $servicePath = config('laravel-crod.modules.service_path', 'Services');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$servicePath",
            $name,
            'Service',
            '/../Stubs/module/service.stub'
        );
    }

    /**
     * Build repository file with call command for module.
     *
     *
     * @return void
     */
    private function makeRepository(string $name)
    {
        $repositoryPath = config('laravel-crod.modules.repository_path', 'Repositories');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$repositoryPath",
            $name,
            config('laravel-crod.repository_namespace', 'Repo'),
            '/../Stubs/module/repo.stub'
        );
    }

    /**
     * Build feature & unit test.
     *
     *
     * @return void
     */
    private function makeTest(string $name)
    {
        $featureTestPath = config('laravel-crod.modules.feature_test_path', 'Tests\Feature');
        $unitTestPath = config('laravel-crod.modules.unit_test_path', 'Tests\Unit');

        $this->makeStubFile(
            $this->module_name_space."\\$name\\$featureTestPath",
            $name,
            'Test',
            '/../Stubs/module/feature-test.stub'
        );
        $this->makeStubFile(
            $this->module_name_space."\\$name\\$unitTestPath",
            $name,
            'Test',
            '/../Stubs/module/unit-test.stub'
        );
    }

    /**
     * Build seeder file with call command.
     *
     * @param  string $name
     * @return void
     */
    private function makeSeeder(string $name)
    {
        $filename = $name . 'Seeder';
        $filenameWithExt = "$filename.php";
        $seederPath = config('laravel-crod.modules.seeder_path', 'Database\Seeders');
        $correctPath = LaravelCrodServiceFacade::changeBackSlashToSlash($seederPath);

        $this->call('make:seeder', [
            'name' => $filename
        ]);

        try {
            rename($filenameWithExt, $this->module_name_space."/$name/$correctPath/$filenameWithExt");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Build factory file with call command.
     *
     * @param  string $name
     * @return void
     */
    private function makeFactory(string $name)
    {
        $filename = $name . 'Factory';
        $filenameWithExt = "$filename.php";
        $factoryPath = config('laravel-crod.modules.factory_path', 'Database\Factories');
        $correctPath = LaravelCrodServiceFacade::changeBackSlashToSlash($factoryPath);

        $this->call('make:factory', [
            'name' => $filename
        ]);

        try {
            rename($filenameWithExt, $this->module_name_space."/$name/$correctPath/$filenameWithExt");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
