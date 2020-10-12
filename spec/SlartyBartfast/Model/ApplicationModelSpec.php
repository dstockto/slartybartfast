<?php

namespace spec\SlartyBartfast\Model;

use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;

class ApplicationModelSpec extends ObjectBehavior
{
    public function let(): void
    {
        $root              = '/path/to/code';
        $name              = 'appname';
        $directories       = [];
        $buildCommand      = 'make build';
        $outputDirectory   = 'app/build';
        $deployLocation    = 'public/deploy';
        $artifactPrefix    = 'app_prefix';
        $artifactTreatment = 'tar';
        $this->beConstructedWith(
            $root,
            $name,
            $directories,
            $buildCommand,
            $outputDirectory,
            $deployLocation,
            $artifactPrefix,
            $artifactTreatment
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ApplicationModel::class);
    }
}
