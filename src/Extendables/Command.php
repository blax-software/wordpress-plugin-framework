<?php

namespace Blax\Wordpress\Extendables;

use Symfony\Component\Console\Command\Command as SymfCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

        // ignore too many arguments
        $this->ignoreValidationErrors();
    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

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
        $question = is_string($question)
            ? new Question($question, $default)
            : $question;

        return $this->getHelper('question')->ask($this->input, $this->output, $question);
    }
}
