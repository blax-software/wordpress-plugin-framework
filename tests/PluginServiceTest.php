<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\PluginService;

class PluginServiceTest extends \PHPUnit\Framework\TestCase
{
    public function test_get_plugin_file()
    {
        $test_path = "/home/alexander/Documents/Repos/wordpress-plugin-project";
        $test_path = null;

        $got_plugin_file = PluginService::getPluginFile($test_path);

        echo 'Got plugin file: ' . $got_plugin_file . PHP_EOL;

        // assert $got_plugin_file not null
        $this->assertNotNull($got_plugin_file);
    }
}
