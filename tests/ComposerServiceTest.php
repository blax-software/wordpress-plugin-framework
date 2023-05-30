<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\ComposerService;

class ComposerServiceTest extends \PHPUnit\Framework\TestCase
{
	public function test_get_absolute_path()
	{
		$absolute_path = ComposerService::getAbsolutePath();
		$this->assertNotNull($absolute_path);
		$this->assertTrue(file_exists($absolute_path));
	}

	public function test_get_json()
	{
		$json = ComposerService::getJson();
		$this->assertNotNull($json);
		$this->assertTrue(is_array($json));
	}

	public function test_set_json()
	{
		$json = ComposerService::getJson();
		$json['name'] = 'blax/wordpress';
		ComposerService::setJson($json);
		$this->assertEquals('blax/wordpress', ComposerService::getJson()['name']);
	}

	public function test_get_namespace()
	{
		$namespace = ComposerService::getNamespace();
		$this->assertEquals('Blax\\Wordpress\\', $namespace);
	}

	public function test_set_namespace()
	{
		$original_namespace = ComposerService::getNamespace();
		$artificial_namespace = 'Punti\\Drupal\\';

		$this->assertEquals('Blax\\Wordpress\\', $original_namespace);
		$this->assertTrue(ComposerService::setNamespace($artificial_namespace));
		$this->assertEquals($artificial_namespace, ComposerService::getNamespace());
		$this->assertTrue(ComposerService::setNamespace($original_namespace));
		$this->assertEquals('Blax\\Wordpress\\', $original_namespace);
	}
}
