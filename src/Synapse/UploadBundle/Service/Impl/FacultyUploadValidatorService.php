<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
* Handle faculty upload validation
*
* @DI\Service("faculty_upload_validator_service")
*/
class FacultyUploadValidatorService extends AbstractService implements UploadValidatorServiceInterface
{

    const SERVICE_KEY = 'faculty_upload_validator_service';

    private $profileService;
    private $errors;
    private $validator;
    private $isUpdate;
    private $organizationRepository;
    private $orgGroupRepository;
    /**
     * @param $repositoryResolver
     * @param $logger
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "profileService" = @DI\Inject("profile_service"),
     *      "validator" = @DI\Inject("validator")
     * })
     */
    public function __construct($repositoryResolver, $logger, $profileService, $validator)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->profileService = $profileService;
        $this->errors = [];
        $this->validator = $validator;
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->orgGroupRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroup");
    }

    public function validate($name, $data, $orgId, $isUpdate = false)
    {

        $this->isUpdate = $isUpdate;

        $personItems = [
            strtolower(UploadConstant::EXTERNALID),
            strtolower(UploadConstant::FIRSTNAME),
            strtolower(UploadConstant::LASTNAME),
            strtolower('Title'),
            strtolower(UploadConstant::DATEOFBIRTH),
            strtolower('AuthUsername')
        ];

        $contactItems = [
            strtolower('Address1'),
            strtolower('Address2'),
            strtolower('City'),
            strtolower('Zip'),
            strtolower('State'),
            strtolower('Country'),
            strtolower('PrimaryMobile'),
            strtolower('AlternateMobile'),
            strtolower('HomePhone'),
            strtolower('OfficePhone'),
            strtolower(UploadConstant::PRIMARY_EMAIL),
            strtolower('AlternateEmail'),
            strtolower('PrimaryMobileProvider'),
            strtolower('AlternateMobileProvider')
        ];

        $otherItems = [
            strtolower('FacultyAuthKey'),
            strtolower('IsActive')
        ];

        if (in_array(strtolower($name), $personItems)) {
            $status = $this->validatePersonItem($name, $data);
        } elseif (in_array(strtolower($name), $contactItems)) {
            $status = $this->validateContactItem(strtolower($name), $data);
        } elseif (preg_match('/^GroupID(.+)$/', $name, $matches)) {
            $status = $this->validateGroupItem($matches[1], $data, $orgId);
        } elseif (in_array(strtolower($name), $otherItems)) {
            $status = $this->validateAdditionalItem();
        } else {
            // renamed function to be easier to read
            $status = $this->notAValidColumn();
        }

        return $status;

    }

    private function validatePersonItem($name, $data)
    {
        $testPerson = new Person;
        call_user_func([$testPerson, 'set' . $name], $data);
        $validationErrors = $this->validator->validate($testPerson);
        foreach ($validationErrors as $error) {
            if (strtolower($error->getPropertyPath()) == strtolower($name) && !$this->isUpdate && $error->getMessage() != $data . ' has already been used.') {
                $this->errors[] = $error->getMessage();
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    private function validateContactItem($name, $data)
    {
        $testContact = new ContactInfo;
        call_user_func([$testContact, 'set' . $name], $data);
        $validationErrors = $this->validator->validate($testContact);

        foreach ($validationErrors as $error) {
            if (strtolower($error->getPropertyPath()) == strtolower($name)) {
                if ($this->isUpdate && $error->getMessage() == 'This value is already used.') {
                    continue;
                }

                if ($this->isUpdate && $error->getMessage() == 'Primary Email  already exists with another ExternalId.') {
                    continue;
                }

                $this->errors[] = $error->getMessage();
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    private function validateGroupItem($externalId, $data, $orgId)
    {
        $organization = $this->organizationRepository->findOneById($orgId);

        if ($data == 1 && !$this->orgGroupRepository->findOneBy(
                ['organization' => $organization, 'externalId' => $externalId]
            )
        )
        {
            $this->errors[] = "Group ID does not exist";
            return false;
        }

        return true;
    }

    private function notAValidColumn()
    {
        $this->errors[] = 'is not a valid column';
        return false;
    }

    private function validateAdditionalItem()
    {
        return true;
    }

    public function getErrors()
    {
        $errors = $this->errors;
        $this->errors = [];

        return $errors;
    }

    /*
     * @codeCoverageIgnore
    */
    private function recursiveInArray($array, $value, $key)
    {
        //loop through the array
        foreach ($array as $val) {
            //if $val is an array cal myInArray again with $val as array input
            if (is_array($val)) {
                if ($this->recursiveInArray($val, $value, $key)) {
                    return $val;
                }
            } else {
                if ($array[$key]==$value) {
                    return true;
                }
            }
        }
        return false;
    }
}