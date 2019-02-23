<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DoBuildsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Implement do builds');

        // load config
        // load applications
        // determine which builds are needed
        // do builds that are needed
        // store artifacts in storage location
        // output progress and results
    }

    protected function configure()
    {
        $this->setName('do-builds')
            ->setDescription('Run builds that are required')
            ->setHelp('Runs builds, creates artifacts, stores them in cache location')
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
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_OPTIONAL,
                'Force the build and storage even if it already exists'
            );
    }

}
