<?php

declare(strict_types=1);

namespace SlartyBartfast\Model;

class ApplicationModel
{
    /** @var string */
    private $root;
    /** @var string */
    private $name;
    /** @var array */
    private $directories;
    /** @var string */
    private $buildCommand;
    /** @var string */
    private $outputDirectory;
    /** @var string */
    private $deployLocation;
    /** @var string */
    private $artifactPrefix;
    /** @var string */
    private $artifactTreatment;

    /**
     * ApplicationModel constructor.
     *
     * @param string $root
     * @param string $name
     * @param array  $directories
     * @param string $buildCommand
     * @param string $outputDirectory
     * @param string $deployLocation
     * @param string $artifactPrefix
     * @param string $artifactTreatment
     */
    public function __construct(
        string $root,
        string $name,
        array $directories,
        string $buildCommand,
        string $outputDirectory,
        string $deployLocation,
        string $artifactPrefix,
        string $artifactTreatment = 'tar' // Not used currently
    ) {
        $this->root              = $root;
        $this->name              = $name;
        $this->directories       = $directories;
        $this->buildCommand      = $buildCommand;
        $this->outputDirectory   = $outputDirectory;
        $this->deployLocation    = $deployLocation;
        $this->artifactPrefix    = $artifactPrefix;
        $this->artifactTreatment = $artifactTreatment;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @return string
     */
    public function getBuildCommand(): string
    {
        return $this->buildCommand;
    }

    /**
     * @return string
     */
    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    /**
     * @return string
     */
    public function getDeployLocation(): string
    {
        return $this->deployLocation;
    }

    /**
     * @return string
     */
    public function getArtifactPrefix(): string
    {
        return $this->artifactPrefix;
    }

    /**
     * @return string
     */
    public function getArtifactTreatment(): string
    {
        return $this->artifactTreatment;
    }
}
