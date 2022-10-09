<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use SlartyBartfast\Model\ApplicationModel;

class ArtifactNamer
{
    public function __construct(private ApplicationModel $application, private string $hash)
    {
    }

    public function getApplicationName(): string
    {
        return $this->application->getName();
    }

    public function getArtifactName(): string
    {
        return strtolower(
            implode(
                '',
                [
                    $this->application->getArtifactPrefix(),
                    '-',
                    $this->hash,
                    '.tar.gz',
                ]
            )
        );
    }

    public function __toString(): string
    {
        return $this->getArtifactName();
    }
}
