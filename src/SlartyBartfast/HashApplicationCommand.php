<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HashApplicationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');

        if (!file_exists($config)) {
            throw new \RuntimeException('Provided artifacts.json file does not exist');
        }

        // load configuration (make a class)

        // get application configurations from configuration object

        // filter configurations if filters are provided

        // loop over remaining configurations

        // dump table of application name and hashes

    }

    protected function configure()
    {
        $this->setName('hash-application')
            ->setDescription('Hash Application(s) from an artifacts.json')
            ->setHelp('Allows you to hash configured applications')
            ->addOption(
                'config',
                null,
                InputOption::VALUE_REQUIRED,
                'artifacts.json location',
                './artifacts.json'
            )
            ->addOption(
                'filter',
                'f',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Limit to only some applications'
            );
    }
}
