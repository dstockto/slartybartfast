<?php

namespace SlartyBartfast;

use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\ArtifactNamer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArtifactNamesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
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
                return new ArtifactNamer(
                    $app,
                    (new DirectoryHasher(
                        $app->getRoot(),
                        $app->getDirectories())
                    )->getHash()
                );
            }
        );

        // Dump table of application names and artifact names
        $io->table(
            ['Application', 'Artifact Name'],
            $hashes->map(
                function (ArtifactNamer $namer) {
                    return [
                        $namer->getApplicationName(),
                        $namer->getArtifactName(),
                    ];
                }
            )->all()
        );
    }

    protected function configure()
    {
        $this->setName('artifact-names')
            ->setDescription('List artifact names for applications')
            ->setHidden(true)
            ->setHelp('Show the names of the artifacts that will be created for each application')
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
