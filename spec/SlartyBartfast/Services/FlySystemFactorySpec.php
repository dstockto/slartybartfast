<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Services\FlySystemFactory;

class FlySystemFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FlySystemFactory::class);
    }
}
