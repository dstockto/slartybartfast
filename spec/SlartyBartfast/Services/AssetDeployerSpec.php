<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\FilesystemAdapter;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\AssetModel;
use SlartyBartfast\Services\AssetDeployer;

class AssetDeployerSpec extends ObjectBehavior
{
    public function let(AssetModel $asset, FilesystemAdapter $filesystem): void
    {
        $this->beConstructedWith($asset, $filesystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AssetDeployer::class);
    }
}
