<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use RuntimeException;
use SlartyBartfast\Model\ApplicationModel;
use Tightenco\Collect\Support\Collection;
use SlartyBartfast\Model\AssetModel;

class ArtifactConfig
{
    /** @var array */
    private $configuration;
    /** @var string */
    private $configPath;
    /** @var Collection */
    private $applicationModels;
    /** @var Collection */
    private $assetModels;
    /** @var bool */
    private $localOverride = false;

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
        $this->buildAssetModels();
    }

    private function validate(): void
    {
        // ensure it exists, throw if not
        if (!file_exists($this->configPath)) {
            throw new RuntimeException('Configuration file does not exist');
        }
    }

    private function loadConfig(): void
    {
        $json = file_get_contents($this->configPath);
        $configuration = json_decode($json, true);

        if ($configuration === null) {
            throw new RuntimeException('JSON Configuration was malformed');
        }

        $this->configuration = $configuration;
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

    public function getAssetList(array $filters): Collection
    {
        $filterCollection = collect($filters)->map(
            function ($filter) {
                return strtolower($filter);
            }
        );

        if (empty($filters)) {
            return $this->assetModels;
        }

        return $this->assetModels->filter(
            function (AssetModel $asset) use ($filterCollection) {
                return $filterCollection->contains(strtolower($asset->getName()));
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

    private function buildAssetModels(): void
    {
        $root = $this->configuration['root_directory'];

        if (!array_key_exists('assets', $this->configuration)) {
            $this->assetModels = collect();
            return;
        }
        $assets = collect($this->configuration['assets']);

        $this->assetModels = $assets->map(
            function ($assetConfig) use ($root) {
                return new AssetModel(
                    $assetConfig['name'],
                    $assetConfig['filename'],
                    $assetConfig['deploy_location'],
                    $root
                );
            }
        );
    }

    public function getRepositoryConfig(): array
    {
        if (!array_key_exists('repository', $this->configuration)) {
            throw new RuntimeException('Configuration is missing the repository section');
        }

        $repoConfig = $this->configuration['repository'];
        if ($this->localOverride) {
            $repoConfig['adapter'] = 'local';
        }

        return $repoConfig;
    }

    /**
     * Sets the configuration to return local for the adapter instead of whatever is configured. It does rely on the
     * options['root'] setting to be in place already
     */
    public function doLocalOverride(): void
    {
        $this->localOverride = true;
    }
}
