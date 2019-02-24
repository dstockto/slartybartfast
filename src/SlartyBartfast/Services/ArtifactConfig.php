<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use SlartyBartfast\Model\ApplicationModel;
use Tightenco\Collect\Support\Collection;

class ArtifactConfig
{
    /** @var array */
    private $configuration;
    /** @var string */
    private $configPath;
    /** @var Collection */
    private $applicationModels;

    /**
     * ArtifactConfig constructor.
     *
     * @param string $configPath
     */
    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
        $this->validate();
        $this->loadConfig();
        $this->buildApplicationModels();
    }

    private function validate(): void
    {
        // ensure it exists, throw if not
        if (!file_exists($this->configPath)) {
            throw new \RuntimeException('Configuration file does not exist');
        }
    }

    private function loadConfig(): void
    {
        $json = file_get_contents($this->configPath);
        $this->configuration = json_decode($json, true);
        if ($this->configuration['root_directory'] === '__DIR__') {
            $this->configuration['root_directory'] = dirname(realpath($this->configPath));
        }
    }

    public function getApplicationList(array $filters): Collection
    {
        $filterCollection = collect($filters)->map(
            function ($filter) {
                return strtolower($filter);
            }
        );

        if (empty($filters)) {
            return $this->applicationModels;
        }

        return $this->applicationModels->filter(
            function (ApplicationModel $application) use ($filterCollection) {
                return $filterCollection->contains(strtolower($application->getName()));
            }
        );
    }

    private function buildApplicationModels(): void
    {
        $root = $this->configuration['root_directory'];

        $artifacts = collect($this->configuration['artifacts']);

        $this->applicationModels = $artifacts->map(
            function ($applicationConfig) use ($root) {
                return new ApplicationModel(
                    $applicationConfig['root'] ?? $root,
                    $applicationConfig['name'],
                    $applicationConfig['directories'],
                    $applicationConfig['command'],
                    $applicationConfig['output_directory'],
                    $applicationConfig['deploy_location'],
                    $applicationConfig['artifact_prefix']
                );
            }
        );
    }
}
