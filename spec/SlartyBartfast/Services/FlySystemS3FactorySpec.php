<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Services\FlySystemS3Factory;

class FlySystemS3FactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FlySystemS3Factory::class);
    }
}
