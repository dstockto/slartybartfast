<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\FilesystemAdapter;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\AssetModel;
use SlartyBartfast\Services\AssetFinder;

class AssetFinderSpec extends ObjectBehavior
{
    public function let(AssetModel $assetModel, FilesystemAdapter $fileSystem): void
    {
        $this->beConstructedWith($assetModel, $fileSystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AssetFinder::class);
    }
}
