<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class FlySystemS3Factory
{
    public static function build(array $options): AwsS3Adapter
    {
        if (!isset(
            $options['key'],
            $options['secret'],
            $options['region'],
            $options['bucket-name']
        )) {
            throw new \RuntimeException(
                'Missing required S3 configuration keys: key, secret, region and/or bucket-name'
            );
        }

        $client = new S3Client([
            'credentials' => [
                'key'    => $options['key'],
                'secret' => $options['secret'],
            ],
            'region' => $options['region'] ?? null,
            'version' => 'latest',
        ]);

        return new AwsS3Adapter($client, $options['bucket-name'], $options['path-prefix'] ?? '');
     }
}
