<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\PluginService;
use Blax\Wordpress\Services\SetupService;

class SetupServiceTest extends \PHPUnit\Framework\TestCase
{
	public function test_get_and_change_namespaces()
	{
		// get current namespace
		$originalNamespace = SetupService::getNamespaceOfFile(__FILE__);
		$artificialNamespace = 'Blax\Wordpress\Tests';

		// assert current namespace
		$this->assertEquals('Blax\Wordpress\Tests', $originalNamespace);

		$this->assertTrue(SetupService::changeNamespaceOfFile(__FILE__, $artificialNamespace));
		$this->assertEquals($artificialNamespace, SetupService::getNamespaceOfFile(__FILE__));
		$this->assertTrue(SetupService::changeNamespaceOfFile(__FILE__, $originalNamespace));
	}

	public function test_replace_namespaces()
	{
		// get current namespace
		$originalNamespace = SetupService::getNamespaceOfFile(__FILE__);
		$replacing_part = 'Blax\\Wordpress';
		$artificial_namespace = 'Punti\\Drupal';

		$this->assertEquals('Blax\Wordpress\Tests', $originalNamespace);
		$this->assertTrue(SetupService::replaceNamespaceOfFile(__FILE__, $replacing_part, $artificial_namespace));
		$this->assertTrue(SetupService::replaceNamespaceOfFile(__FILE__, SetupService::getNamespaceOfFile(__FILE__), $originalNamespace));

		$plugin_file_path = PluginService::getPluginFile();
		$plugin_namespace = SetupService::getNamespaceOfFile($plugin_file_path);
		$this->assertEquals('Blax\Wordpress', $plugin_namespace);
		$this->assertTrue(SetupService::replaceNamespaceOfFile($plugin_file_path, $replacing_part, $artificial_namespace));
		$this->assertTrue(SetupService::replaceNamespaceOfFile($plugin_file_path, SetupService::getNamespaceOfFile($plugin_file_path), $plugin_namespace));
	}

	public function test_getting_namespace_from_composer()
	{
		$namespace = SetupService::getNamespaceFromComposer();
		$this->assertEquals('Blax\\Wordpress\\', $namespace);
	}
}
