<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\LoggerService;
use Blax\Wordpress\Services\PluginService;

class LoggerServiceTest extends \PHPUnit\Framework\TestCase
{
	public function test_get_logfile()
	{
		$dir = PluginService::getPluginDir();

		$this->assertDirectoryExists($dir);
		$this->assertFileDoesNotExist($dir . '/log.log');
		$this->assertFileExists(LoggerService::getLogFile());
		$this->assertFileExists($dir . '/log.log');

		unlink($dir . '/log.log');

		$this->assertFileDoesNotExist($dir . '/log.log');
	}

	public function test_get_custom_logger_file()
	{
		$dir = PluginService::getPluginDir();

		$this->assertDirectoryExists($dir);
		$this->assertFileDoesNotExist($dir . '/logs/custom.log');
		$this->assertFileExists(LoggerService::getLogFile('custom.log'));
		$this->assertFileExists($dir . '/logs/custom.log');

		unlink($dir . '/logs/custom.log');
		rmdir($dir . '/logs');

		$this->assertFileDoesNotExist($dir . '/logs/custom.log');
		$this->assertDirectoryDoesNotExist($dir . '/logs');
	}

	public function test_logging_a_message_in_default_logger()
	{
		$dir = PluginService::getPluginDir();
		$test_message = 'testing log message';

		LoggerService::log($test_message);

		$this->assertFileExists($dir . '/log.log');
		$this->assertStringContainsString($test_message, file_get_contents($dir . '/log.log'));

		LoggerService::clear();
		$this->assertStringContainsString('', LoggerService::getContent());

		unlink($dir . '/log.log');
	}

	public function test_logging_a_message_in_default_logger_with_null_channel()
	{
		$dir = PluginService::getPluginDir();
		$test_message = 'testing log message';

		LoggerService::channel()->log($test_message);

		$this->assertFileExists($dir . '/log.log');
		$this->assertStringContainsString($test_message, file_get_contents($dir . '/log.log'));

		LoggerService::channel()->clear();
		$this->assertStringContainsString('', LoggerService::channel()->getContent());

		unlink($dir . '/log.log');
	}

	public function test_logging_a_message_in_custom_logger()
	{
		$dir = PluginService::getPluginDir();
		$channel = 'custom';
		$message = 'testing log message';

		LoggerService::channel($channel)->log($message);

		$this->assertFileExists($dir . '/logs/' . $channel . '.log');
		$this->assertStringContainsString($message, file_get_contents($dir . '/logs/' . $channel . '.log'));

		LoggerService::channel($channel)->clear();
		$this->assertStringContainsString('', LoggerService::channel($channel)->getContent());

		unlink($dir . '/logs/custom.log');
		rmdir($dir . '/logs');
	}
}
