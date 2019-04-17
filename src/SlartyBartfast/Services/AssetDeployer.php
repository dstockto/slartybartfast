<?php

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use RuntimeException;
use SlartyBartfast\Model\AssetModel;
use Symfony\Component\Console\Output\OutputInterface;

class AssetDeployer
{
    /**
     * @var AssetModel
     */
    private $asset;
    /**
     * @var AdapterInterface
     */
    private $filesystem;

    /**
     * AssetDeployer constructor.
     *
     * @param AssetModel       $asset
     * @param AdapterInterface $filesystem
     */
    public function __construct(AssetModel $asset, AdapterInterface $filesystem)
    {
        $this->asset      = $asset;
        $this->filesystem = $filesystem;
    }

    public function deploy(OutputInterface $output): void
    {
        $currentDir = getcwd();
        $output->writeln('Deployer current dir: ' . $currentDir);
        $changed = chdir($this->asset->getRoot());
        if ($changed) {
            $output->writeln('Changed to ' . $this->asset->getRoot());
        } else {
            $output->writeln('Unable to change to app root directory: ' . $this->asset->getRoot());
        }

        // check if it exists
        $finder = new AssetFinder($this->asset, $this->filesystem);

        if (!$finder->assetExists()) {
            $output->writeln('Asset is not found. Switching directory back to ' . $currentDir);
            chdir($currentDir);
            throw new RuntimeException('Missing asset ' . $this->asset->getFilename());
        }

        $output->writeln(
            'Found ' . $this->asset->getName() . ' asset: ' . $this->asset->getFilename()
        );

        if (!file_exists($this->asset->getDeployLocation())) {
            $output->writeln(
                'Deployment location does not exist, creating: ' .
                $this->asset->getDeployLocation()
            );
            if (!mkdir(
                    $concurrentDirectory = $this->asset->getDeployLocation(),
                    0755,
                    true
                ) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(
                    sprintf('Directory "%s" was not created', $concurrentDirectory)
                );
            }
            $output->writeln('Created deploy location');
        }

        // download it
        chdir($this->asset->getDeployLocation());
        $output->writeln('Changed directory to (deploy location): ' . getcwd());
        $file       = fopen($this->asset->getFilename(), 'wb');
        $readStream = $this->filesystem->read($this->asset->getFilename());
        fwrite($file, $readStream['contents']);
        fclose($file);

        $output->writeln([' - Downloaded asset']);

        if (!file_exists(getcwd() . '/' . $this->asset->getFilename())) {
            $output->writeln('Unable to location file where we thought it was');
        }

        // untar it
        $command = "tar -xzvf {$this->asset->getFilename()}";
        $output->writeln('Running ' . $command);
        exec($command, $shellOut, $exitCode);
        $output->writeln($shellOut);

        $output->writeln([' - Untarred asset']);

        unlink($this->asset->getFilename());

        $output->writeln([' - Deleted (tar) asset']);

        $output->writeln('Restoring location');
        chdir($currentDir);
    }
}
