<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\ArtifactNamer;

class ArtifactNamerSpec extends ObjectBehavior
{
    public function let(ApplicationModel $applicationModel): void
    {
        $this->beConstructedWith($applicationModel, 'abcdefg');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ArtifactNamer::class);
    }
}
