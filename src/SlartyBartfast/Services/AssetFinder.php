<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use SlartyBartfast\Model\AssetModel;

class AssetFinder
{
    public function __construct(private AssetModel $assetModel, private FilesystemAdapter $fileSystem)
    {
    }

    public function assetExists(): bool
    {
        return $this->fileSystem->has($this->assetModel->getFilename());
    }
}
