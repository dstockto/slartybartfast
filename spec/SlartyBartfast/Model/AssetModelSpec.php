<?php

namespace spec\SlartyBartfast\Model;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\AssetModel;

class AssetModelSpec extends ObjectBehavior
{
    public function let(): void
    {
        $name           = 'Asset';
        $filename       = 'filename';
        $deployLocation = 'public/dir';
        $root           = '/path/to/code';

        $this->beConstructedWith($name, $filename, $deployLocation, $root);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AssetModel::class);
    }
}
