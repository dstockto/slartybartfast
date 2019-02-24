<?php
declare(strict_types=1);

namespace SlartyBartfast;

use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\DirectoryHasher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HashApplicationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');

        if (!file_exists($config)) {
            throw new \RuntimeException('Provided artifacts.json file does not exist');
        }

        $io                = new SymfonyStyle($input, $output);
        $applicationConfig = new ArtifactConfig($input->getOption('config'));

        // Get application list (filtered)
        $applications = $applicationConfig->getApplicationList($input->getOption('filter'));

        if ($applications->isEmpty()) {
            $io->error('No applications match provided filter');
            return 1;
        }

        // Loop and get hash
        $hashes = $applications->map(
            function (ApplicationModel $app) {
                return [
                    $app->getName(),
                    (new DirectoryHasher(
                        $app->getRoot(),
                        $app->getDirectories()
                    ))->getHash(),
                ];
            }
        );

        // Dump table of application names and hashes
        $io->table(
            ['Application', 'Hash'],
            $hashes->all()
        );

        return 0;
    }

    protected function configure()
    {
        $this->setName('hash-application')
            ->setDescription('Hash Application(s) from an artifacts.json')
            ->setHelp('Allows you to hash configured applications')
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
