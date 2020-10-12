<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\DeployCleanup;

class DeployCleanupSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DeployCleanup::class);
    }
}
