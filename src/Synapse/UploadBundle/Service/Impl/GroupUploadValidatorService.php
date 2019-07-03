<?php
namespace Synapse\UploadBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
/**
 * Synapse Upload Validation
 *
 * @DI\Service("groupupload_validator_service")
 */
class GroupUploadValidatorService extends SynapseValidatorService
{

    const SERVICE_KEY = 'groupupload_validator_service';

    const RISK_M_001 = "ERR-RISK_M_001";

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "profileService" = @DI\Inject("profile_service"),
     *            "orgProfileService" = @DI\Inject("orgprofile_service"),
     *            "validator" = @DI\Inject("validator")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $profileService, $orgProfileService, $validator)
    {
        parent::__construct($repositoryResolver, $logger, $profileService, $orgProfileService, $validator);
    }

    public function validateStudentCommandCol($data)
    {
        if (! empty($data[strtolower(GroupUploadConstants::REMOVE)]) && strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) != 'remove') {
            $this->errors[] = [
                'name' => '',
                'value' => '',
                'errors' => [
                    $data[strtoloweR(GroupUploadConstants::REMOVE)]. " Not a valid command"
                ]
            ];
        }
    }
}