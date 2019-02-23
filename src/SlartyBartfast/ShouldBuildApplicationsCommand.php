<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShouldBuildApplicationsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('TODO: Implement this');

        // load configuration

        // extract application configs from configuration

        // filter if needed

        // calculate hashes for each application

        // calculate archive name based on hash

        // determine if artifact exists in storage location

        // dump table based on results of above
    }

    protected function configure()
    {
        $this->setName('should-build')
            ->setDescription('Determine if applications should be built')
            ->setHelp('Determines if application artifact exists in S3 or should be built')
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
