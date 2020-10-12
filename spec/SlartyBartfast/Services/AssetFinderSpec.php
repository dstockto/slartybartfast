<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\AssetModel;
use SlartyBartfast\Services\AssetFinder;

class AssetFinderSpec extends ObjectBehavior
{
    public function let(AssetModel $assetModel, AdapterInterface $fileSystem): void
    {
        $this->beConstructedWith($assetModel, $fileSystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AssetFinder::class);
    }
}
