<?php
declare(strict_types=1);

namespace SlartyBartfast;

use RuntimeException;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\AssetDeployer;
use SlartyBartfast\Services\BuildDeployer;
use SlartyBartfast\Services\FlySystemFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DoDeploysCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');

        if (!file_exists($config)) {
            throw new RuntimeException('Provided artifacts.json file does not exist');
        }

        $io                = new SymfonyStyle($input, $output);
        $applicationConfig = new ArtifactConfig($input->getOption('config'));

        $filesystem = FlySystemFactory::getAdapter(
            $applicationConfig->getRepositoryConfig()
        );

        // Get application list (filtered)
        $applications = $applicationConfig->getApplicationList($input->getOption('filter'));

        // calculate hash->archive name, download archive, extract to configured location
        $deployers = $applications->map(
            function (ApplicationModel $app) use ($filesystem) {
                return new BuildDeployer($app, $filesystem);
            }
        );

        $deployers->each(
            function (BuildDeployer $deployer) use ($output) {
                $deployer->deploy($output);
            }
        );

        return 0;
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
