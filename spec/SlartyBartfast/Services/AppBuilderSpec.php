<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\FilesystemAdapter;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\AppBuilder;

class AppBuilderSpec extends ObjectBehavior
{
    public function let(ApplicationModel $application, FilesystemAdapter $filesystem): void
    {
        $this->beConstructedWith($application, $filesystem, false);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AppBuilder::class);
    }
}
