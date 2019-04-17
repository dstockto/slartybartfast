<?php

namespace SlartyBartfast\Model;

class AssetModel
{
    /** @var string */
    private $name;
    /** @var string */
    private $filename;
    /** @var string */
    private $deployLocation;
    /** @var string */
    private $root;

    /**
     * AssetModel constructor.
     *
     * @param string $name
     * @param string $filename
     * @param string $deployLocation
     * @param string $root
     */
    public function __construct($name, $filename, $deployLocation, $root)
    {
        $this->name           = $name;
        $this->filename       = $filename;
        $this->deployLocation = $deployLocation;
        $this->root           = $root;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
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
    public function getRoot(): string
    {
        return $this->root;
    }
}
