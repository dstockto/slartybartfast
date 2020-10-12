<?php

namespace spec\SlartyBartfast;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\MyCommand;

class MyCommandSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(MyCommand::class);
    }
}
