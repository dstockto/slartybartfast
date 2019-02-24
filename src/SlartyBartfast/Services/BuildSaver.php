<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use SlartyBartfast\Model\ApplicationModel;

class BuildSaver
{
    /**
     * @var ApplicationModel
     */
    private $application;
    /**
     * @var AdapterInterface
     */
    private $filesystem;

    public function __construct(ApplicationModel $application, AdapterInterface $filesystem)
    {
        $this->application = $application;
        $this->filesystem  = $filesystem;
    }

    public function saveBuild()
    {
        $currentDir = getcwd();

        $namer = new ArtifactNamer(
            $this->application,
            (new DirectoryHasher(
                $this->application->getRoot(),
                $this->application->getDirectories()
            ))->getHash()
        );

        $artifactName = $namer->getArtifactName();

        // zip the output directory
        chdir($this->application->getOutputDirectory());
        $command = "zip -r $artifactName .";
        shell_exec($command);

        $archiveFile = fopen($artifactName, 'rb');
        // transfer that zip to the filesystem with the name provided
        $this->filesystem->writeStream($artifactName, $archiveFile, new Config());

        // Remove local zip file
        unlink($artifactName);
        chdir($currentDir);
    }
}
