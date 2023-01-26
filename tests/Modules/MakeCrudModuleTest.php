<?php

namespace Milwad\LaravelCrod\Tests\Modules;

use Carbon\Carbon;
use Milwad\LaravelCrod\Tests\BaseTest;

class MakeCrudModuleTest extends BaseTest
{
    /**
     * @var string
     */
    private string $name = 'Product';

    /**
     * @var string
     */
    private string $command = "crud:make-module";

    /**
     * @var string|null
     */
    private ?string $module;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->module = config('laravel-crod.modules.module_namespace') ?? 'Modules';
    }

    /**
     * Test check all files create when user run command 'crud:make'.
     *
     * @test
     * @return void
     */
    public function check_to_create_files_with_command_crud_make()
    {
        $this->artisan($this->command, [
            'module_name' => $this->name,
        ]);

        $this->checkAllToModelIsCreatedWithOriginalName();
        $this->checkAllToMigrationIsCreatedWithOriginalName();
        $this->checkAllToControllerIsCreatedWithOriginalName();
//        $this->checkAllToRequestIsCreatedWithOriginalName();
//        $this->checkAllToViewIsCreatedWithOriginalName();
    }

    /**
     * @return void
     */
    private function checkAllToModelIsCreatedWithOriginalName(): void
    {
        $modelFolderName = config('laravel-crod.modules.model_path') ?? 'Entities';
        $filename = base_path("$this->module\\$this->name\\$modelFolderName\\$this->name.php");

        $this->assertEquals(1, file_exists($filename));
        $this->assertEquals($this->name, basename($filename, '.php'));
    }

    /**
     * @return void
     */
    private function checkAllToMigrationIsCreatedWithOriginalName(): void
    {
//        $name = strtolower($this->name);
// TODO
//        $file = !str_ends_with($name, 'y')
//            ? $this->migrationExists("create_{$name}ies_table")
//            : $this->migrationExists("create_{$name}s_table");
//dd(now());
//        $this->assertEquals(1, $file);
    }

    /**
     * Check migration file is exists.
     *
     * @param  string $mgr
     * @return bool
     */
    private function migrationExists(string $mgr)
    {
        $modelFolderName = config('laravel-crod.modules.migration_path') ?? 'Database\Migrations';
        $path = base_path("$this->module\\$this->name\\$modelFolderName");
        $files = scandir($path);

        foreach ($files as &$value) {
            $pos = strpos($value, $mgr);
            if ($pos !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    private function checkAllToControllerIsCreatedWithOriginalName(): void
    {
        $modelFolderName = config('laravel-crod.modules.controller_path') ?? 'Http\Controllers';
        $name = $this->name . 'Controller';
        $filename = base_path("$this->module\\$this->name\\$modelFolderName\\$name.php");

        $this->assertEquals(1, file_exists($filename));
        $this->assertEquals($name, basename($filename, '.php'));
    }
}