<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use SlartyBartfast\Model\AssetModel;

class AssetFinder
{
    public function __construct(private readonly AssetModel $assetModel, private readonly FilesystemAdapter $fileSystem)
    {
    }

    public function assetExists(): bool
    {
        return $this->fileSystem->fileExists($this->assetModel->getFilename());
    }
}
