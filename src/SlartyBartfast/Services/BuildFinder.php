<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use SlartyBartfast\Model\ApplicationModel;

class BuildFinder
{
    /**
     * @var ApplicationModel
     */
    private $applicationModel;
    /**
     * @var AdapterInterface
     */
    private $fileSystem;

    public function __construct(ApplicationModel $applicationModel, AdapterInterface $fileSystem)
    {
        $this->applicationModel = $applicationModel;
        $this->fileSystem       = $fileSystem;
    }

    public function isBuildNeeded(): bool
    {
        $hasher = new DirectoryHasher(
            $this->applicationModel->getRoot(),
            $this->applicationModel->getDirectories()
        );
        $namer  = new ArtifactNamer($this->applicationModel, $hasher->getHash());

        return !$this->fileSystem->has($namer->getArtifactName());
    }
}
