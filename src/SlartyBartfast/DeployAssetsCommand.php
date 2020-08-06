<?php
declare(strict_types=1);

namespace SlartyBartfast;

use RuntimeException;
use SlartyBartfast\Model\AssetModel;
use SlartyBartfast\Services\ArtifactConfig;
use SlartyBartfast\Services\AssetDeployer;
use SlartyBartfast\Services\FlySystemFactory;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployAssetsCommand extends SymfonyCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');

        if (!file_exists($config)) {
            throw new RuntimeException('Provided artifacts.json file does not exist');
        }

        $applicationConfig = new ArtifactConfig($input->getOption('config'));

        if ($input->getOption('local')) {
            $applicationConfig->doLocalOverride();
        }

        $filesystem = FlySystemFactory::getAdapter(
            $applicationConfig->getRepositoryConfig()
        );

        $assets = $applicationConfig->getAssetList($input->getOption('filter'));

        $deployers = $assets->map(
            function (AssetModel $asset) use ($filesystem) {
                return new AssetDeployer($asset, $filesystem);
            }
        );

        $deployers->each(
            function (AssetDeployer $deployer) use ($output) {
                $deployer->deploy($output);
            }
        );

        return 0;
    }

    protected function configure()
    {
        $this->setName('deploy-assets')
            ->setDescription('Deploys assets for the application')
            ->setHelp('Deploys assets to the configured locations')
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
                'Limit to only some assets'
            )->addOption(
                'local',
                null,
                null,
                'Deploy assets from local location'
            );
    }
}
