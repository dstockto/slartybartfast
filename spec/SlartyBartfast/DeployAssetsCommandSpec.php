<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\DeployAssetsCommand;

class DeployAssetsCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DeployAssetsCommand::class);
    }
}
