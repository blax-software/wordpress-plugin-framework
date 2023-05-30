<?php

namespace Blax\Wordpress\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Blax\Wordpress\Extendables\Command;

class CommandExtendableTest extends \PHPUnit\Framework\TestCase
{
	private function getAppWithTestCommand()
	{
		$test_class = function () {
			return new class extends Command
			{
				public static $signature = 'test-command';
				public static $description = 'This is a test command.';

				public function handle()
				{
					$this->writelnColor('Hello World!');
					return Command::SUCCESS;
				}
			};
		};


		$app = new Application();
		$command = $app->add($test_class());

		return [
			'app' => $app,
			'command' => $command
		];
	}

	public function test_adding_a_custom_command()
	{
		// test if the command is added to the application
		$this->assertInstanceOf(\Symfony\Component\Console\Command\Command::class, $this->getAppWithTestCommand()['command']);

		// test if command is available
		$this->assertTrue($this->getAppWithTestCommand()['app']->has('test-command'));
	}

	public function test_test_if_custom_command_works()
	{
		$command = $this->getAppWithTestCommand()['app']->find('test-command');
		$commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
		$commandTester->execute([
			'command' => $command->getName(),
			'username' => 'John Doe'
		]);

		$this->assertEquals('This is a test command.', $command->getDescription());

		$this->assertEquals('Hello World!' . PHP_EOL, $commandTester->getDisplay());
	}

	public function test_test_if_custom_command_arguments_can_be_configured_properly()
	{
		$command = $this->getAppWithTestCommand()['app']->find('test-command');
		$commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);

		$command->addArgument('age', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Pass the age.');
		$command->addArgument('lastname', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Pass the lastname.');

		// check if argument is required
		$this->assertTrue($command->getDefinition()->hasArgument('age'));
		$this->assertTrue($command->getDefinition()->getArgument('age')->isRequired());

		// check if argument is optional
		$this->assertTrue($command->getDefinition()->hasArgument('lastname'));
		$this->assertFalse($command->getDefinition()->getArgument('lastname')->isRequired());

		$commandTester->execute([
			'command' => $command->getName(),
			'age' => 20,
		]);

		$this->assertEquals('Hello World!' . PHP_EOL, $commandTester->getDisplay());
		$this->assertNotEquals('Hello World!20' . PHP_EOL, $commandTester->getDisplay());
	}

	public function test_test_if_custom_command_can_ask_questions()
	{
		$app = $this->getAppWithTestCommand()['app'];

		$test_class = function () {
			return new class extends Command
			{
				public static $signature = 'test-asl';
				public static $description = 'This command asks for age';

				public function handle()
				{
					$age = $this->ask('How old are you?');
					$this->writelnColor("You are {$age} years old.");
					return Command::SUCCESS;
				}
			};
		};

		$command = $app->add($test_class());

		$commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
		$commandTester->setInputs(['20']);
		$commandTester->execute([
			'command' => $command->getName(),
		]);

		$this->assertEquals('This command asks for age', $command->getDescription());

		$this->assertStringContainsString('How old are you?', $commandTester->getDisplay());
		$this->assertStringContainsString('You are 20 years old.', $commandTester->getDisplay());
	}

	public function test_arguments()
	{
		$app = $this->getAppWithTestCommand()['app'];

		global $tester;
		$tester = $this;

		$test_class = function () use ($tester) {
			return new class extends Command
			{
				public static $signature = 'test-options {age?}';
				public static $description = 'This command asks for age';

				public function handle()
				{
					global $tester;

					$tester->assertEquals(20, $this->getArgument('age'));
					$tester->assertEquals(20, $this->getArguments()['age']);

					// $this->writelnColor("You are {$age} years old.");
					return Command::SUCCESS;
				}
			};
		};

		$command = $app->add($test_class());

		$commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
		$commandTester->execute([
			'command' => $command->getName(),
			'age' => 20
		]);
	}

	public function test_options()
	{
		$app = $this->getAppWithTestCommand()['app'];

		global $tester;
		$tester = $this;

		$test_class = function () use ($tester) {
			return new class extends Command
			{
				public static $signature = 'test-options {--age}';
				public static $description = 'This command asks for age';

				public function handle()
				{
					global $tester;

					$tester->assertEquals(20, $this->getOption('age'));
					$tester->assertEquals(20, $this->getOptions()['age']);

					return Command::SUCCESS;
				}
			};
		};

		$command = $app->add($test_class());

		$commandTester = new \Symfony\Component\Console\Tester\CommandTester($command);
		$commandTester->execute([
			'command' => $command->getName(),
			'--age' => 20
		]);
	}
}
