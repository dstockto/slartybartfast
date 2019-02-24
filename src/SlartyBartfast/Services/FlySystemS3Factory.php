<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AwsS3v3\AwsS3Adapter;

class FlySystemS3Factory
{
    public static function build(array $options): AwsS3Adapter
    {
//        $client = S3Client::factory([
//            'credentials' => [
//                'key'    => 'your-key',
//                'secret' => 'your-secret',
//            ],
//            'region' => 'your-region',
//            'version' => 'latest|version',
//        ]);
//
//        $adapter = new AwsS3Adapter($client, 'your-bucket-name', 'optional/path/prefix');
//
//        $filesystem = new Filesystem($adapter);

        // Default this?
        //    'version' => 'latest|version',

        throw new \RuntimeException('I have not yet created the S3 adapter factory');
    }
}
