<?php

namespace Blax\Wordpress\Extendables;

use Symfony\Component\Console\Command\Command as SymfCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

abstract class Command extends SymfCommand
{
	public static $signature;
	public static $description;

	public InputInterface $input;
	public OutputInterface $output;

	protected function configure()
	{
		$this->setName(static::$signature)
			->setDescription(static::$description ?? '[No description]');

		$this->configureArguments();

		// ignore too many arguments
		$this->ignoreValidationErrors();
	}

	final protected function configureOptions()
	{
		$options = $this->input->getOptions();

		preg_match_all('/\{--(\S+)\}/', static::$signature, $matches);

		foreach ($matches[1] as $match) {
			if (array_key_exists($match, $options)) {
				$this->writelnColor("Option <fg=yellow>--{$match}</> already exists", 'red');
				continue;
			}

			// if match contains "=", set a default value
			$default = '';
			if (strpos($match, '=')) {
				$parts = explode('=', $match);
				// if contains more than 2 parts throw
				if (count($parts) > 2) {
					$this->writelnColor("Option default <fg=yellow>--{$match}</> is invalid", 'red');
					continue;
				}
				$match   = $parts[0];
				$default = $parts[1];
			}

			$option_description = null;
			if (strpos($match, ':')) {
				$parts = explode(':', $match);
				// if contains more than 2 parts throw
				if (count($parts) > 2) {
					$this->writelnColor("Option description <fg=yellow>--{$match}</> is invalid", 'red');
					continue;
				}
			}

			$this->addOption($match, null, $option_description, $default);
		}
	}

	final protected function configureArguments()
	{
		preg_match_all('/\{(?!--)(\S+)\}/', static::$signature, $matches);


		foreach ($matches[1] as $match) {
			// if match contains "=", set a default value
			$default = null;
			if (strpos($match, '=')) {
				$parts = explode('=', $match);
				// if contains more than 2 parts throw
				if (count($parts) > 2) {
					$this->writelnColor("Argument default <fg=yellow>{$match}</> is invalid", 'red');
					continue;
				}
				$match   = $parts[0];
				$default = $parts[1];
			}

			$description = '';
			if (strpos($match, ':')) {
				$parts = explode(':', $match);
				// if contains more than 2 parts throw
				if (count($parts) > 2) {
					$this->writelnColor("Argument description <fg=yellow>{$match}</> is invalid", 'red');
					continue;
				}
			}

			$is_optional = (substr($match, -1) === '?')
				? InputArgument::OPTIONAL
				: InputArgument::REQUIRED;

			$match = (substr($match, -1) === '?')
				? substr($match, 0, -1)
				: $match;

			$this->addArgument($match, $is_optional, $description, $default);
		}
	}


	final protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->input  = $input;
		$this->output = $output;

		$this->configureOptions();

		return $this->handle();
	}

	abstract function handle();

	public function write()
	{
		$this->output->write(...func_get_args());
	}

	public function writeln()
	{
		$this->output->writeln(...func_get_args());
	}

	public function writelnColor($messages, $color = 'white', $options = 0)
	{
		$this->output->writeln("<fg={$color}>{$messages}</>", $options);
	}

	public function writeColor($messages, $color = 'white', $options = 0)
	{
		$this->output->write("<fg={$color}>{$messages}</>", $options);
	}

	public function ask(String|Question $question, $default = null)
	{
		if ($question instanceof Question) {
			return $this->getHelper('question')->ask($this->input, $this->output, $question);
		}

		if ($default && substr($default, -1) === '\\') {
			$default = " " . $default . " ";
		}

		$visible_default = $default ? ' [<fg=yellow>' . $default . '</>]' : '';

		$question = new Question('<fg=green>' . $question . '</>' . $visible_default . PHP_EOL . '> ', $default);

		$this->output->writeln('');
		$answer = $this->getHelper('question')->ask($this->input, $this->output, $question);
		$this->output->writeln('');

		return $answer;
	}

	public function confirm(String|ConfirmationQuestion $question, $default = false)
	{
		if ($question instanceof ConfirmationQuestion) {
			return $this->getHelper('question')->ask($this->input, $this->output, $question);
		}

		$visible_default = $default ? ' [<fg=yellow>yes</>]' : ' [<fg=yellow>no</>]';

		$question = new ConfirmationQuestion('<fg=green>' . $question . '</> (yes/no)' . $visible_default . PHP_EOL . '> ', $default);

		$this->output->writeln('');
		$answer = $this->getHelper('question')->ask($this->input, $this->output, $question);
		$this->output->writeln('');

		return $answer;
	}

	public function getOption($name)
	{
		return $this->input->getOption($name);
	}

	public function hasOption($name)
	{
		return $this->input->hasOption($name);
	}

	public function getArguments()
	{
		return $this->input->getArguments();
	}

	public function getArgument($name)
	{
		return $this->input->getArgument($name);
	}

	public function hasArgument($name)
	{
		return $this->input->hasArgument($name);
	}
}
