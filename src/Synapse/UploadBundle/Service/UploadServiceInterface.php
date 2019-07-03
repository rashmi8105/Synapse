<?php

namespace Synapse\UploadBundle\Service;

interface UploadServiceInterface
{



    /**
     * Processes the currently loaded file
     * @return array|bool Returns array containg information about the upload, or false on failure
     */
    public function process($uploadId);
}
