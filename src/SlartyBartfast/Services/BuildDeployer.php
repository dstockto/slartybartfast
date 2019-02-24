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
        chdir($this->application->getRoot());
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
            chdir($currentDir);
            throw new \RuntimeException('Missing build ' . $namer->getArtifactName());
        }

        $output->writeln('Found artifact ' . $namer . ' for ' . $this->application->getName());

        if (!file_exists($this->application->getDeployLocation())) {
            if (!mkdir(
                $concurrentDirectory = $this->application->getDeployLocation(),
                0755,
                true
                ) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(
                    sprintf('Directory "%s" was not created', $concurrentDirectory)
                );
            }
        }

        // download it
        chdir($this->application->getDeployLocation());
        $file = fopen($namer->getArtifactName(), 'wb');
        $readStream = $this->filesystem->read($namer->getArtifactName());
        fwrite($file, $readStream['contents']);
        fclose($file);

        // unzip it
        shell_exec("unzip -f {$namer->getArtifactName()}");

        unlink($namer->getArtifactName());

        chdir($currentDir);
    }
}
