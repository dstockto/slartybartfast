<?php
declare(strict_types=1);

namespace SlartyBartfast;

use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\BuildFinder;
use SlartyBartfast\Services\FlySystemFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShouldBuildApplicationsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        // load configuration
        $applicationConfig = new ArtifactConfig($input->getOption('config'));

        if ($input->getOption('local')) {
            $applicationConfig->doLocalOverride();
        }

        $filesystem = FlySystemFactory::getAdapter(
            $applicationConfig->getRepositoryConfig()
        );

        // Get application list (filtered)
        $applications = $applicationConfig->getApplicationList($input->getOption('filter'));

        if ($applications->isEmpty()) {
            $io->error('No applications match provided filter');
            return 1;
        }

        // determine if artifact exists in storage location
        $buildNeeded = $applications->map(
            function (ApplicationModel $app) use ($filesystem) {
                return [
                    $app->getName(),
                    (new BuildFinder($app, $filesystem))->isBuildNeeded() ? 'YES' : 'NO',
                ];
            }
        );

        // dump table based on results of above
        $io->table(
            ['Application', 'Build Needed'],
            $buildNeeded->all()
        );

        return 0;
    }

    protected function configure()
    {
        $this->setName('should-build')
            ->setDescription('Determine if applications should be built')
            ->setHelp('Determines if application artifact exists in S3 or should be built')
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
            )
            ->addOption(
                'local',
                null,
                null,
                'Determine build status using local artifact repo'
            );
    }

}
