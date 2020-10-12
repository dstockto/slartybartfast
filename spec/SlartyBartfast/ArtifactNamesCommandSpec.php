<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\ArtifactNamesCommand;

class ArtifactNamesCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ArtifactNamesCommand::class);
    }
}
