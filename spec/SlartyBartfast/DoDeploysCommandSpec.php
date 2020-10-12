<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\DoDeploysCommand;

class DoDeploysCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DoDeploysCommand::class);
    }
}
