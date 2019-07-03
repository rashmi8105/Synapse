<?php

namespace Synapse\CoreBundle\Util;

use Aws\S3\S3Client;

/**
* Provides helper methods for S3 URL's
*/
class S3Helper
{

    private $key;
    private $secret;
    private $region;
    private $bucket;
    private $client;

    public function __construct($key, $secret, $region, $bucket)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->region = $region;
        $this->bucket = $bucket;

        $this->client = S3Client::factory([
            'key' => $this->key,
            'secret' => $this->secret,
            'region' => $this->region
        ]);
    }

    public function getSecureUrl($file, $time = 600)
    {
        $signedUrl = $this->client->getObjectUrl($this->bucket, $file, "+$time seconds");

        return $signedUrl;
    }

    public function getSecureUploadUrl($file, $time = 600)
    {
        $command = $this->client->getCommand('PutObject', [
            'Bucket' => $this->bucket,
            'Key' => $file,
            'ACL' => 'private',
            'Body' => ''
        ]);

        $signedUrl = $command->createPresignedUrl("+$time seconds");

        return $signedUrl;
    }
}