<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use SlartyBartfast\Model\ApplicationModel;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDeployer
{
    /**
     * @var ApplicationModel
     */
    private $application;
    /**
     * @var AdapterInterface
     */
    private $filesystem;

    /**
     * BuildDeployer constructor.
     *
     * @param ApplicationModel $application
     * @param AdapterInterface $filesystem
     */
    public function __construct(ApplicationModel $application, AdapterInterface $filesystem)
    {
        $this->application = $application;
        $this->filesystem  = $filesystem;
    }

    public function deploy(OutputInterface $output): void
    {
        $currentDir = getcwd();
        $output->writeln('Deployer current dir: ' . $currentDir);
        $changed = chdir($this->application->getRoot());
        if ($changed) {
            $output->writeln('Changed to ' . $this->application->getRoot());
        } else {
            $output->writeln('Unable to change to app root directory: ' . $this->application->getRoot());
        }

        // determine archive name
        $namer = new ArtifactNamer(
            $this->application,
            (new DirectoryHasher(
                $this->application->getRoot(),
                $this->application->getDirectories()
            ))->getHash()
        );

        // check if it exists
        $finder = new BuildFinder($this->application, $this->filesystem);

        if ($finder->isBuildNeeded()) {
            $output->writeln('Build is not found. Switching directory back to ' . $currentDir);
            chdir($currentDir);
            throw new \RuntimeException('Missing build ' . $namer->getArtifactName());
        }

        $output->writeln('Found artifact ' . $namer . ' for ' . $this->application->getName());

        if (!file_exists($this->application->getDeployLocation())) {
            $output->writeln(
                'Deployment location does not exist, creating: ' .
                $this->application->getDeployLocation()
            );
            if (!mkdir(
                $concurrentDirectory = $this->application->getDeployLocation(),
                0755,
                true
                ) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(
                    sprintf('Directory "%s" was not created', $concurrentDirectory)
                );
            }
            $output->writeln('Created deploy location');
        }

        // download it
        chdir($this->application->getDeployLocation());
        $output->writeln('Changed directory to (deploy location): ' . getcwd());
        $file = fopen($namer->getArtifactName(), 'wb');
        $readStream = $this->filesystem->read($namer->getArtifactName());
        fwrite($file, $readStream['contents']);
        fclose($file);

        $output->writeln([' - Downloaded artifact']);

        if (!file_exists(getcwd() . '/' . $namer->getArtifactName())) {
            $output->writeln('Unable to location file where we thought it was');
        }

        // untar it
        $command = "tar -xzvf {$namer->getArtifactName()}";
        $output->writeln('Running ' . $command);
        exec($command, $shellOut, $exitCode);
        $output->writeln($shellOut);

        $output->writeln([' - Untarred artifact']);

        unlink($namer->getArtifactName());

        $output->writeln([' - Deleted (tar) artifact']);

        $output->writeln('Restoring location');
        chdir($currentDir);
    }
}
