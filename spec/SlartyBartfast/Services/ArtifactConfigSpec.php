<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Services\ArtifactConfig;

class ArtifactConfigSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('/path/to/config');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ArtifactConfig::class);
    }
}
