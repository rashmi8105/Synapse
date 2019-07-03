<?php

namespace Synapse\UploadBundle\Service;

interface UploadValidatorServiceInterface
{

    public function validate($name, $data, $orgId, $isUpdate);
}
