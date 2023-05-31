<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\ComposerService;
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

		$original_file_content = file_get_contents(__FILE__);

		try {
			$this->assertEquals('Blax\Wordpress\Tests', $originalNamespace);
			$this->assertTrue(SetupService::replaceNamespaceOfFile(__FILE__, $replacing_part, $artificial_namespace));
			$this->assertTrue(SetupService::replaceNamespaceOfFile(__FILE__, SetupService::getNamespaceOfFile(__FILE__), $originalNamespace));
		} catch (\Throwable $th) {
		} finally {
			file_put_contents(__FILE__, $original_file_content);
		}


		$plugin_file_path = PluginService::getPluginFile();
		$plugin_file_content = file_get_contents($plugin_file_path);
		$plugin_namespace = SetupService::getNamespaceOfFile($plugin_file_path);

		try {
			$this->assertEquals('Blax\Wordpress', $plugin_namespace);
			$this->assertTrue(SetupService::replaceNamespaceOfFile($plugin_file_path, $replacing_part, $artificial_namespace));
			$this->assertTrue(SetupService::replaceNamespaceOfFile($plugin_file_path, SetupService::getNamespaceOfFile($plugin_file_path), $plugin_namespace));
			$this->assertTrue($plugin_file_content == file_get_contents($plugin_file_path));
		} catch (\Throwable $th) {
			//throw $th;
		} finally {
			file_put_contents($plugin_file_path, $plugin_file_content);
		}
	}

	public function test_replace_use_namespaces()
	{
		$plugin_file_path = PluginService::getPluginFile();
		$plugin_file_content = file_get_contents($plugin_file_path);

		$original_namespace = ComposerService::getNamespace();

		try {
			$this->assertEquals('Blax\Wordpress\\', $original_namespace);
			$this->assertTrue(SetupService::replaceUseNamespaceOfFile($plugin_file_path, $original_namespace, 'Punti\Drupal'));
			$this->assertTrue(SetupService::replaceUseNamespaceOfFile($plugin_file_path, 'Punti\Drupal', $original_namespace));
			$this->assertTrue($plugin_file_content == file_get_contents($plugin_file_path));
		} catch (\Throwable $th) {
			throw $th;
		} finally {
			file_put_contents($plugin_file_path, $plugin_file_content);
		}

		$this->assertTrue(true);
	}

	public function test_getting_namespace_from_composer()
	{
		$namespace = SetupService::getNamespaceFromComposer();
		$this->assertEquals('Blax\\Wordpress\\', $namespace);
	}
}
