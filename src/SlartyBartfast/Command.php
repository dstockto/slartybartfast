<?php

declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function greetUser(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            [
                '====**** User Greetings Console App ****====',
                '============================================',
            ]
        );

        $output->writeln(
            $this->getGreeting() . ', ' .
            $input->getArgument('username')
        );
    }

    private function getGreeting(): string
    {
        $time = date('H');
        if ($time < 12) {
            return 'Good morning';
        }

        if ($time < 17) {
            return 'Good afternoon';
        }

        return 'Good night';
    }
}
