<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\BuildFinder;

class BuildFinderSpec extends ObjectBehavior
{
    public function let(ApplicationModel $applicationModel, FilesystemAdapter $fileSystem): void
    {
        $this->beConstructedWith($applicationModel, $fileSystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BuildFinder::class);
    }
}
