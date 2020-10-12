<?php

namespace spec\SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use PhpSpec\ObjectBehavior;
use SlartyBartfast\Model\ApplicationModel;
use SlartyBartfast\Services\BuildDeployer;

class BuildDeployerSpec extends ObjectBehavior
{
    public function let(ApplicationModel $application, AdapterInterface $filesystem): void
    {
        $this->beConstructedWith($application, $filesystem);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BuildDeployer::class);
    }
}
