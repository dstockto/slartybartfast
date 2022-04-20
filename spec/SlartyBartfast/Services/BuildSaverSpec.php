<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemAdapter;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\BuildSaver;

class BuildSaverSpec extends ObjectBehavior
{
    public function let(ApplicationModel $application, FilesystemAdapter $filesystem): void
    {
        $this->beConstructedWith($application, $filesystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BuildSaver::class);
    }
}
