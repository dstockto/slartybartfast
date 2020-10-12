<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use SlartyBartfast\Model\AssetModel;

class AssetFinder
{
    /**
     * @var AssetModel
     */
    private $assetModel;
    /**
     * @var AdapterInterface
     */
    private $fileSystem;

    public function __construct(AssetModel $assetModel, AdapterInterface $fileSystem)
    {
        $this->assetModel = $assetModel;
        $this->fileSystem = $fileSystem;
    }

    public function assetExists(): bool
    {
        return $this->fileSystem->has($this->assetModel->getFilename());
    }
}
