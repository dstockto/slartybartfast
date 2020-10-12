<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Services\FlySystemLocalFactory;

class FlySystemLocalFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FlySystemLocalFactory::class);
    }
}
