<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use SlartyBartfast\Model\ApplicationModel;

class ArtifactNamer
{
    private $hash;
    private $application;

    /**
     * ArtifactNamer constructor.
     *
     * @param ApplicationModel $applicationModel
     * @param string           $hash
     */
    public function __construct(ApplicationModel $applicationModel, string $hash)
    {
        $this->application = $applicationModel;
        $this->hash        = $hash;
    }

    /**
     * @return string
     */
    public function getApplicationName(): string
    {
        return $this->application->getName();
    }

    /**
     * @return string
     */
    public function getArtifactName(): string
    {
        return strtolower(
            implode('',
                [
                    $this->application->getArtifactPrefix(),
                    '-',
                    $this->hash,
                    '.tar'
                ]
            )
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getArtifactName();
    }
}
