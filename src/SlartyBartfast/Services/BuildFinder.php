<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use SlartyBartfast\Model\ApplicationModel;

class BuildFinder
{
    public function __construct(
        private readonly ApplicationModel $applicationModel,
        private readonly FilesystemAdapter $fileSystem
    ) {
    }

    public function isBuildNeeded(): bool
    {
        $hasher = new DirectoryHasher(
            $this->applicationModel->getRoot(),
            $this->applicationModel->getDirectories()
        );
        $namer  = new ArtifactNamer($this->applicationModel, $hasher->getHash());

        return !$this->fileSystem->fileExists($namer->getArtifactName());
    }
}
