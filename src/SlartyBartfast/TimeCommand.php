<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TimeCommand extends Command
{
    public function configure()
    {
        $this->setName('greet')
            ->setHidden(true)
            ->setDescription('Greet a user based on the time of the day')
            ->setHelp('This command allows you to greet a user based on the time of day')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->greetUser($input, $output);
    }
}
