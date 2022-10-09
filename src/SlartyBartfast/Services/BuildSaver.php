<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use SlartyBartfast\Model\ApplicationModel;
use Symfony\Component\Console\Output\OutputInterface;

class BuildSaver
{
    public function __construct(
        private readonly ApplicationModel $application,
        private readonly FilesystemAdapter $filesystem
    ) {
    }

    public function saveBuild(OutputInterface $output): void
    {
        $currentDir = getcwd();
        chdir($this->application->getRoot());

        $namer = new ArtifactNamer(
            $this->application,
            (new DirectoryHasher(
                $this->application->getRoot(),
                $this->application->getDirectories()
            ))->getHash()
        );

        $artifactName = $namer->getArtifactName();

        // tar the output directory
        chdir($this->application->getOutputDirectory());
        $command = "tar -cvzf $artifactName .";
        shell_exec($command);

        $archiveFile = fopen($artifactName, 'rb');
        // transfer that tar to the filesystem with the name provided
        $this->filesystem->writeStream($artifactName, $archiveFile, new Config());
        $output->writeln(["-- Saved $artifactName to repository."]);
        // Remove local tar file
        unlink($artifactName);
        chdir($currentDir);
    }
}
