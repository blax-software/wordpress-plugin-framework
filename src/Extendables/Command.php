<?php

namespace Blax\Wordpress\Extendables;

use Symfony\Component\Console\Command\Command as SymfCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfCommand
{
    public static $signature;
    public static $description;

    protected function configure()
    {
        $this->setName(static::$signature)
            ->setDescription(static::$description ?? '[No description]');

        // ignore too many arguments
        $this->ignoreValidationErrors();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return SymfCommand::SUCCESS;
    }
}
