<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DoDeploysCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Implement Do Deploys');

        // load configuration
        // load applications (filter if needed)
        // calculate hash->archive name, download archive, extract to configured location
        // if archive isn't found, error
    }

    protected function configure()
    {
        $this->setName('do-deploys')
            ->setDescription('Deploys the applications')
            ->setHelp('Deploys the applications in the locations configured')
            ->addOption(
                'config',
                'c',
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
