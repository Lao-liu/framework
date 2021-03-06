<?php
namespace Clarity\Console;

use Clarity\Console\CLI;

class AppTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $test_module_index = 'public/test_module.php';
        if ( file_exists($test_module_index) ) {
            di()->get('flysystem')->delete($test_module_index);
        }

        $test_module = 'app/test_module';
        if ( is_dir($test_module) ) {
            di()->get('flysystem')->deleteDir($test_module);
        }
    }

    public function testAppRoute()
    {
        CLI::bash([
            'php brood app:module test_module',
        ]);

        $has_file = file_exists(config()->path->app . 'test_module/routes.php');
        $this->assertTrue($has_file, 'check if module "test_module" were generated');



        CLI::bash([
            'php brood app:route test test_module'
        ]);

        $has_file = file_exists(config()->path->app . 'test_module/routes/TestRoutes.php');
        $this->assertTrue($has_file, 'check if route "test" were generated');



        CLI::bash([
            'php brood app:controller test test_module'
        ]);

        $has_file = file_exists(config()->path->app . 'test_module/controllers/TestController.php');
        $this->assertTrue($has_file, 'check if controller "test" were generated');
    }
}
