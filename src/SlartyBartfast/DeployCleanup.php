<?php

namespace SlartyBartfast;

use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactConfig;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeployCleanup extends SymfonyCommand
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

        // Use exclusion list too
        $exclude = $input->getOption('exclude');
        if ($exclude) {
            $exclude = collect($exclude)->map(function ($x) {
                return strtolower($x);
            })->all();

            $applications = $applications->filter(function (ApplicationModel $applicationModel) use ($exclude) {
                return !in_array(strtolower($applicationModel->getName()), $exclude, true);
            });
        }

        $applications->each(
            function (ApplicationModel $app) use ($io) {
                $dir = $app->getDeployLocation();
                $this->deleteFiles($dir, $io);
                $io->success('Deploy directory for ' . $app->getName() . ' cleaned up.');
            }
        );
        return 0;
    }

    protected function configure()
    {
        $this->setName('do-cleanup')
            ->setDescription('Cleanup deploy directories')
            ->setHelp('Deletes the contents of asset deploy directories')
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
                'exclude',
                'e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Exclude some applications'
            );
    }

    protected function deleteFiles($target, SymfonyStyle $io): void
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->deleteFiles($file, $io);
            }
            $io->text('- Deleting directory ' . $target);
            // TODO - figure out a way to make this not try to double delete the directory
            @rmdir($target);
        } else {
            if (is_file($target)) {
                $io->text('- rm ' . $target);
                unlink($target);
            }
        }
    }
}
