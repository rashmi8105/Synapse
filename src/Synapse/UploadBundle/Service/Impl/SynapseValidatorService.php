<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Synapse Upload Validation
 *
 * @DI\Service("synapse_validator_service")
 */
class SynapseValidatorService extends AbstractService
{

    const SERVICE_KEY = 'synapse_validator_service';

    protected $profileService;

    protected $orgProfileService;

    protected $errors;

    protected $validator;

    protected $isUpdate;

    protected $organizationRepository;

    protected $orgGroupRepository;

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
    public function __construct($repositoryResolver, $logger, $profileService = null, $orgProfileService = null, $validator = null)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->profileService = $profileService;
        $this->orgProfileService = $orgProfileService;
        $this->errors = [];
        $this->validator = $validator;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->orgGroupRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroup");
        $this->childConstruct();
    }

    public function childConstruct()
    {}

    public function validateContents($item, $id, $repoInfoArr = null, $customMsg = null)
    {
        if (is_array($repoInfoArr)) {

            foreach ($repoInfoArr as $key => $value) {
                if (! $value) {
                    return false;
                }
            }

            $repoClass = $repoInfoArr['class'];
            $repoObj = $this->repositoryResolver->getRepository($repoClass);
            $repoObjVal = $repoObj->findOneBy($repoInfoArr['keys']);

            if (! isset($id)) {
                $this->errors[] = [
                    'name' => $item,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "{$item} should not be empty. "
                    ]
                ];
                return false;
            }

            if (! is_object($repoObjVal)) {
                $customMsg = ($customMsg) ? $customMsg : "{$id} is an invalid value. ";
                $this->errors[] = [
                    'name' => $item,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        $customMsg
                    ]
                ];
                return false;
            }

            return $repoObjVal;
        }
    }

    public function setErrors($errors)
    {
        $this->errors[] = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
    
    public function clearErrors(){
        $this->errors = [];
    }

    public function validateSU($row)
    {
        foreach ($row as $field => $value) {
            switch (strtolower($field)) {
                case 'redlow':
                case 'redhigh':
                case 'yellowlow':
                case 'yellowhigh':
                case 'greenlow':
                case 'greenhigh':
                    $this->checkNumeric($field, $value);
                    break;
            }
        }
    }

    protected function checkLimit($field, $value, $startLimit, $endLimit)
    {
        if ($value > $endLimit) {
            $this->errors[] = [
                'name' => $field,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                    'Value cannot exceed ' . $endLimit . '.'
                ]
            ];
            return false;
        }

        if ($value < $startLimit ) {
            $this->errors[] = [
                'name' => $field,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                    'Value cannot be less than ' . $startLimit . '.'
                ]
            ];
            return false;
        }
        return true; // changed from  return $value to return true as when value will be 0 it would mean its false.
    }

    protected function checkNumericWithLimit($value, $startLimit, $endLimit)
    {
        if (! is_numeric($value) || ($value < $startLimit || $value > $endLimit)) {
            $this->errors[] = [
                'name' => $value,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                    $value . UploadConstant::INVALID_VALUE
                ]
            ];
            return false;
        }
        return $value;
    }

    protected function checkNumeric($field, $value)
    {
        if (! empty($value) && ! preg_match('/\d/', $value)) {
            $this->errors[] = [
                'name' => $field,
                UploadConstant::VALUE => '',
                UploadConstant::ERRORS => [
                    $value . UploadConstant::INVALID_VALUE
                ]
            ];
        }
        return $value;
    }
}