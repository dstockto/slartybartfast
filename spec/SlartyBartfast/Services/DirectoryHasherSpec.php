<?php

namespace spec\SlartyBartfast\Services;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Services\DirectoryHasher;

class DirectoryHasherSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('__DIR__', []);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DirectoryHasher::class);
    }
}
