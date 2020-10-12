<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\ShouldBuildApplicationsCommand;

class ShouldBuildApplicationsCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShouldBuildApplicationsCommand::class);
    }
}
