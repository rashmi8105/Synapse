<?php

namespace Synapse\UploadBundle\Service;

use Synapse\CoreBundle\Util\S3Helper;

interface S3PolicyServiceInterface {

    public function getSecureUrl($file);
    public function getSecureUploadUrl($file);
}
