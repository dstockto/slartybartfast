<?php

declare(strict_types=1);

namespace SlartyBartfast;

use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\AppBuilder;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\BuildSaver;
use SlartyBartfast\Services\FlySystemFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DoBuildsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');

        if (!file_exists($config)) {
            throw new \RuntimeException('Provided artifacts.json file does not exist');
        }

        $io                = new SymfonyStyle($input, $output);
        $applicationConfig = new ArtifactConfig($input->getOption('config'));

        if ($input->getOption('local')) {
            $applicationConfig->doLocalOverride();
        }

        $filesystem = FlySystemFactory::getAdapter(
            $applicationConfig->getRepositoryConfig()
        );

        // Get application list (filtered)
        $applications = $applicationConfig->getApplicationList($input->getOption('filter'));

        $force = $input->getOption('force');

        $builders = $applications->map(
            function (ApplicationModel $app) use ($filesystem, $force) {
                return new AppBuilder($app, $filesystem, $force);
            }
        );

        $builders->each(
            function (AppBuilder $builder) use ($io, $output, &$sections) {
                $io->writeln(
                    [
                        'Doing build for ' . $builder->getApplicationName()
                        . ' - ' . ($builder->shouldBuild() ? 'YES' : 'NO'),
                    ]
                );
            }
        );

        $progressSection = $output->section();

        $requiredBuilders = $builders->filter(
            function (AppBuilder $builder) {
                return $builder->shouldBuild();
            }
        );

        $progress = new ProgressBar($progressSection);

        $neededBuildCount = $requiredBuilders->count();

        $progress->setMaxSteps($neededBuildCount);
        $requiredBuilders->each(
            function (AppBuilder $builder) use ($filesystem, $output, $input, $progress) {
                $success = $builder->doBuild($input, $output);
                $progress->advance();

                if (!$success) {
                    throw new \RuntimeException('Build failure on ' . $builder->getApplicationName());
                }

                $saver = new BuildSaver($builder->getApplication(), $filesystem);
                $saver->saveBuild($output);
            }
        );

        $progress->finish();
        return 0;
    }

    protected function configure()
    {
        $this->setName('do-builds')
            ->setDescription('Run builds that are required')
            ->setHelp('Runs builds, creates artifacts, stores them in cache location')
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
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the build and storage even if it already exists'
            )->addOption(
                'local',
                null,
                null,
                'Build and store in local repo'
            );
    }
}
