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
            $options['bucket-name'],
            $options['region']
        )) {
            throw new \RuntimeException(
                'Missing required S3 configuration keys: bucket-name or region'
            );
        }

        $client = new S3Client([
            'version' => 'latest',
            'profile' => $options['profile'] ?? 'default',
            'region'  => $options['region']
        ]);

        // TODO: Not 5.5 compatible
        return new AwsS3Adapter($client, $options['bucket-name'], $options['path-prefix'] ?? '');
     }
}
