<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;

class FlySystemFactory
{
    public static function getAdapter(array $configuration): AdapterInterface
    {
        switch (strtolower($configuration['adapter'])) {
            case 'local':
                return FlySystemLocalFactory::build($configuration['options']);
                break;
            case 's3':
                return FlySystemS3Factory::build($configuration['options']);
                break;
        }
    }
}
