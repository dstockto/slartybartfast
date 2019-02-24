<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\Adapter\Local;

class FlySystemLocalFactory
{
    public static function build(array $options): Local
    {
        if (!array_key_exists('root', $options)) {
            throw new \RuntimeException('Local filesystem requested without providing root');
        }

        return new Local($options['root']);
    }
}
