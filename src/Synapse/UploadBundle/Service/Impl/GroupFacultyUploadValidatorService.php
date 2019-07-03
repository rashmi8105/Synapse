<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Person;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
* Handle faculty upload validation
*
* @DI\Service("group_faculty_upload_validator_service")
*/
class GroupFacultyUploadValidatorService extends AbstractService implements UploadValidatorServiceInterface
{

    const SERVICE_KEY = 'group_faculty_upload_validator_service';

    private $errors;
    private $isUpdate;
    /**
     * @param $repositoryResolver
     * @param $logger
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     * })
     */
    public function __construct($repositoryResolver, $logger, $profileService, $validator)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->errors = [];
    }

    public function validate($name, $data, $orgId, $isUpdate = false)
    {

        $this->isUpdate = $isUpdate;

        if (strtolower($name) != strtolower(UploadConstant::EXTERNALID)) {
            $this->errors[] = 'is not a valid column';
        }

        if (count($this->errors)) {
            return false;
        }

        return true;

    }

    public function getErrors()
    {
        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }

}
