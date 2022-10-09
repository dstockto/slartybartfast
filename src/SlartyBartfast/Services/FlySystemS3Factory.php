<?php

declare(strict_types=1);

namespace SlartyBartfast\Services;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class FlySystemS3Factory
{
    public static function build(array $options): AwsS3V3Adapter
    {
        if (!isset($options['bucket-name'], $options['region'])) {
            throw new \RuntimeException(
                'Missing required S3 configuration keys: bucket-name or region'
            );
        }

        $clientOptions = [
            'version' => 'latest',
            'region'  => $options['region'],
        ];

        if (isset($options['profile'])) {
            $clientOptions['profile'] = $options['profile'];
        }

        $client = new S3Client($clientOptions);

        return new AwsS3V3Adapter($client, $options['bucket-name'], $options['path-prefix'] ?? "");
    }
}
