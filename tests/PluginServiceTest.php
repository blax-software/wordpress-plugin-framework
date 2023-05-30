<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\PluginService;

class PluginServiceTest extends \PHPUnit\Framework\TestCase
{
	public function test_get_plugin_file()
	{
		$got_plugin_file = PluginService::getPluginFile();

		// assert $got_plugin_file not null
		$this->assertNotNull($got_plugin_file);
		$this->assertTrue(file_exists($got_plugin_file));
		$this->assertTrue(
			strpos(basename($got_plugin_file), basename(dirname($got_plugin_file))) !== false
				|| strpos(file_get_contents($got_plugin_file), 'Plugin Name:') !== false
		);
	}
}
