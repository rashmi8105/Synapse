<?php
namespace Synapse\UploadBundle\Service\Impl;

use Doctrine\Common\Cache\RedisCache;
use Gedmo\ReferenceIntegrity\Mapping\Validator;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyRepository;
use Synapse\UploadBundle\Service\UploadValidatorServiceInterface;
use Synapse\UploadBundle\Util\Constants\UploadConstant;


/**
 * Handle student upload validation
 *
 * @DI\Service("student_upload_validator_service")
 */
class StudentUploadValidatorService extends AbstractService implements UploadValidatorServiceInterface
{

    const SERVICE_KEY = 'student_upload_validator_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var Validator
     */
    private $validator;

    // Services

    /**
     * @var OrgProfileService
     */
    private $orgProfileService;

    /**
     * @var ProfileService
     */
    private $profileService;

    // Repository

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgPersonStudentSurveyRepository
     */
    private $orgPersonStudentSurveyRepository;

    // Member Variables

    /**
     * @var array
     */
    private $errors;

    /**
     * @var boolean
     */
    private $isUpdate;

    /**
     * @var boolean
     */
    private $profileCache = false;

    /**
     * @var boolean
     */
    private $archivedProfileCache = false;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        // Services
        $this->orgProfileService = $this->container->get(OrgProfileService::SERVICE_KEY);
        $this->profileService = $this->container->get(ProfileService::SERVICE_KEY);

        // Repository
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgPersonStudentSurveyRepository = $repositoryResolver->getRepository(OrgPersonStudentSurveyRepository::REPOSITORY_KEY);

        // Member Variables
        $this->errors = [];
    }


    /**
     * Checks if the academicyearId is Valid
     * @param int $organizationId
     * @param string $yearId
     * @return bool|object
     */
    public function validateAcademicYear($organizationId, $yearId)
    {

        $academicYear = $this->orgAcademicYearRepository->findOneBy([
            UploadConstant::ORGN => $organizationId,
            'yearId' => $yearId
        ]);

        if ($academicYear) {
            return $academicYear;
        } else {
            return false;
        }
    }

    /**
     * validates upload fields for student
     *
     * @param string $name
     * @param array $data
     * @param int $orgId
     * @param bool $isUpdate
     * @return bool
     */
    public function validate($name, $data, $orgId, $isUpdate = false)
    {
        $this->isUpdate = $isUpdate;
        $this->errors = [];

        $personItems = [
            'externalid',
            'firstname',
            'lastname',
            'title',
            'dateofbirth',
            'authusername'
        ];

        $contactItems = [
            'address1',
            'address2',
            'city',
            'zip',
            'state',
            'country',
            'primarymobile',
            'alternatemobile',
            'homephone',
            'officephone',
            'primaryemail',
            'alternateemail',
            'primarymobileprovider',
            'alternatemobileprovider'
        ];

        $otherItems = [
            'studentphoto',
            'isactive',
            'termid',
            'yearid',
            'riskgroupid',
            'surveycohort',
            'transitiononereceivesurvey',
            'checkuponereceivesurvey',
            'transitiontworeceivesurvey',
            'checkuptworeceivesurvey',
            'primaryconnect',
            'studentauthkey',
            'recordtype',
            'participating',
            'retentiontrack',
            'enrolledatmidpointofacademicyear',
            'enrolledatbeginningofacademicyear',
            'completedadegree'
        ];

        if (in_array(strtolower($name), $personItems)) {
            $status = $this->validatePersonItem($name, $data);
        } elseif (in_array(strtolower($name), $contactItems)) {
            $status = $this->validateContactItem($name, $data);
        } elseif (preg_match('/^GroupID(.+)$/', $name, $matches)) {
            $status = $this->validateGroupItem($matches[1], $data, $orgId);
        } elseif (in_array(strtolower($name), $otherItems)) {
            $status = $this->validateAdditionalItem();
        } else {
            $status = $this->validateProfileItem($name, $data, $orgId);
        }

        return $status;
    }

    private function validatePersonItem($name, $data)
    {
        $testPerson = new Person();
        call_user_func([
            $testPerson,
            'set' . $name
        ], $data);
        $validationErrors = $this->validator->validate($testPerson);
        foreach ($validationErrors as $error) {
            if (strtolower($error->getPropertyPath()) == strtolower($name) && !$this->isUpdate && $error->getMessage() != 'ExternalId already exists with another Primary Email' && $error->getMessage() != 'Primary Email  already exists with another ExternalId') {
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
        $testContact = new ContactInfo();
        call_user_func([
            $testContact,
            'set' . $name
        ], $data);

        $validationErrors = $this->validator->validate($testContact);

        foreach ($validationErrors as $error) {
            if (strtolower($error->getPropertyPath()) == lcfirst($name)) {
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

        if ($data == 1 && !$this->orgGroupRepository->findOneBy([
                'organization' => $organization,
                'externalId' => $externalId
            ])
        ) {
            $this->errors[] = "Group ID does not exist";
            return false;
        }

        return true;
    }

    /**
     * Validate profile items
     *
     * @param string $name
     * @param array $data
     * @param int $orgId
     * @return bool
     */
    private function validateProfileItem($name, $data, $orgId)
    {

        if (!$this->profileCache || !$this->archivedProfileCache) {
            $this->profileCache = $this->cache->fetch("profileItems:all:$orgId");
            $this->archivedProfileCache = $this->cache->fetch("profileItems:archived:$orgId");
        }

        $profileItems = $this->profileCache;
        $archivedProfileItems = $this->archivedProfileCache;

        $profileItem = $this->recursiveInArray($profileItems, $name, 'item_label');
        if ($profileItem) {

            if (empty(trim($data))) {
                return true;
            }

            switch ($profileItem['item_data_type']) {
                case 'T':
                    $this->validateTextProfile($profileItem, $data);
                    break;
                case 'N':
                    $this->validateNumberProfile($profileItem, $data);
                    break;
                case 'S':
                    $this->validateSelectProfile($profileItem, $data);
                    break;
                case 'D':
                    $this->validateDateProfile($data);
                    break;
                default:
                    $this->errors[] = 'Contains invalid data type';
                    break;
            }
        } else {
            if ($this->recursiveInArray($archivedProfileItems, $name, 'item_label')) {
                $this->errors[] = 'This item is currently archived and not available for updating';
            } else {
                $this->errors[] = 'is not a valid column';
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    private function validateTextProfile($profileItem, $data)
    {
        switch ($profileItem['definition_type']) {
            case 'E':
                if (strlen($data) > 255) {
                    $this->errors[] = 'Field cannot exceed more than 255 characters.';
                }
                break;

            case 'O':
                if (strlen($data) > 1024) {
                    $this->errors[] = 'Field cannot exceed more than 1024 characters.';
                }
                break;

            default:
                if (count($data) > 2000) {
                    $this->errors[] = 'Field cannot exceed more than 2000 characters.';
                }
                break;
        }

    }

    private function validateNumberProfile($profileItem, $data)
    {
        if (isset($profileItem[UploadConstant::DECIMAL_POINTS])) {
            $profileItem[UploadConstant::NUM_TYPE] = [];

            if (isset($profileItem[UploadConstant::MIN_DIGITS])) {
                $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MIN_DIGITS] = $profileItem[UploadConstant::MIN_DIGITS];
            } else if (isset($profileItem['min_range'])) {
                $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MIN_DIGITS] = $profileItem['min_range'];
            }

            if (isset($profileItem[UploadConstant::MAX_DIGITS])) {
                $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MAX_DIGITS] = $profileItem[UploadConstant::MAX_DIGITS];
            } else if (isset($profileItem['max_range'])) {
                $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MAX_DIGITS] = $profileItem['max_range'];
            }
            $profileItem[UploadConstant::NUM_TYPE][UploadConstant::DECIMAL_POINTS] = $profileItem[UploadConstant::DECIMAL_POINTS];

        }
        if (!is_numeric($data)) {
            $this->errors[] = 'Field has the wrong data type: This field only accepts numbers';
            return false;
        }
        if ($profileItem[UploadConstant::NUM_TYPE][UploadConstant::MIN_DIGITS] && $data < $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MIN_DIGITS]) {
            $this->errors[] = 'Value cannot be less than ' . number_format($profileItem[UploadConstant::NUM_TYPE][UploadConstant::MIN_DIGITS], 2) . '.';
        }
        if ($profileItem[UploadConstant::NUM_TYPE][UploadConstant::MAX_DIGITS] && $data > $profileItem[UploadConstant::NUM_TYPE][UploadConstant::MAX_DIGITS]) {
            $this->errors[] = 'Value cannot exceed more than ' . number_format($profileItem[UploadConstant::NUM_TYPE][UploadConstant::MAX_DIGITS], 2) . '.';
        }
        if (strlen(substr(strrchr($data, "."), 1)) > $profileItem[UploadConstant::NUM_TYPE][UploadConstant::DECIMAL_POINTS]) {
            $this->errors[] = 'Field has too many decimals places: Field may only contain upto ' . $profileItem[UploadConstant::NUM_TYPE][UploadConstant::DECIMAL_POINTS] . ' decimal places.';
        }
    }

    /**
     * Validate profile data
     *
     * @param array $profileItem
     * @param array $data
     */
    private function validateSelectProfile($profileItem, $data)
    {
        if (!isset($profileItem['category_type'])) {
            $category = $this->profileService->getProfile($profileItem['id']);
        } else {
            $category = $profileItem;
        }

        if (is_array($category)) {
            $items = $category['category_type'];
        } else {
            $items = $category->getCategoryType();
        }

        $validOption = false;
        foreach ($items as $item) {
            if ($data == $item['value']) {
                $validOption = true;
            }
        }

        if (!$validOption) {
            $this->errors[] = 'Invalid values for this field. To see valid values, please consult the data definitions file.';
        }
    }

    /**
     * Validate Date
     *
     * @param string $data
     * @return bool
     */
    private function validateDateProfile($data)
    {
        if (!$data) {
            return false;
        }

        $data = date(SynapseConstant::DATE_FORMAT, $data);

        if (is_object(\DateTime::createFromFormat(SynapseConstant::DATE_FORMAT, $data))) {
            return true;
        } else {
            $this->errors[] = 'Invalid Date. Dates should be numeric in month/date/year format.';
            return false;
        }

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

    public function validatePrimaryConnect($studentId, $facultyId, $orgId, $cuurentDateTime)
    {
        $this->logger->info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++");
        $orgPersonFacultyRepository = $this->repositoryResolver->getRepository(AcademicUpdateConstant::ORG_PERSON_FACULTY_REPO);
        $campusConnections = $orgPersonFacultyRepository->getCourseCampusConnection($studentId, $orgId, $cuurentDateTime);
        $campusGroupConnections = $orgPersonFacultyRepository->getGroupCampusConnection($studentId, $orgId, $cuurentDateTime);
        $connections = array_merge($campusConnections, $campusGroupConnections);

        if (count($connections) > 0) {
            $faculties = array_column($connections, 'faculty_id');

            if (in_array($facultyId, $faculties)) {
                $this->logger->info("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++" . json_encode($faculties));
                return true;
            }
        }
        return false;
    }

    /*
     * @codeCoverageIgnore
     */
    private function recursiveInArray($array, $value, $key)
    {
        // loop through the array
        foreach ($array as $val) {
            // if $val is an array cal myInArray again with $val as array input
            if (is_array($val)) {
                if ($this->recursiveInArray($val, $value, $key)) {
                    return $val;
                }
            } else {
                if (isset($array[$key]) && strtolower($array[$key]) == strtolower($value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Validates the value for retention Variable  and if the academic year is valid  for the retention values, Valid values would be 0 and 1
     *
     * @param string $retentionVariable - RetentionTrack,EnrolledAtBeginningOfAcademicYear,EnrolledAtMidpointOfAcademicYear,CompletedADegree
     * @param string $retentionVariableValue -  Value of the variables uploaded in csv
     * @param OrgAcademicYear $orgAcademicYear -  academic year
     * @return bool
     */
    public function validateRetentionVariable($retentionVariable, $retentionVariableValue, $orgAcademicYear)
    {
        if ($retentionVariableValue !== 1 && $retentionVariableValue !== "1" && $retentionVariableValue !== 0 && $retentionVariableValue !== "0") {
            $this->errors[$retentionVariable] = "Invalid value for $retentionVariable column: should be either 0 or 1";
        }
        if (!$orgAcademicYear) {
            $this->errors[$retentionVariable] = "$retentionVariable field needs to have a valid year";
        }
        if (count($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates the value for receive survey  and if the academic year is valid
     *
     * @param Organization $organization
     * @param array $rowData
     * @return null|array
     */
    public function validateReceiveSurveys($organization, $rowData)
    {
        $mappedSurveyColumnNames = [
            "transitiononereceivesurvey" => "Transition One",
            "checkuponereceivesurvey" => "Check-Up One",
            "transitiontworeceivesurvey" => "Transition Two",
            "checkuptworeceivesurvey" => "Check-Up Two"
        ];

        $atLeastOneSurveyVariableIsValid = false;

        //looping through each of the receive survey column.
        // Loops the array keys of the array above ex: checkuponereceivesurvey
        foreach ($mappedSurveyColumnNames as $receiveSurveyColumn => $value) {
            // check if the column is present and if it has valid values
            if (isset($rowData[$receiveSurveyColumn]) && $rowData[$receiveSurveyColumn] != "") {
                $isReceiveSurveyVariableValid = $this->validateRetentionVariable($receiveSurveyColumn, $rowData[$receiveSurveyColumn], $rowData['yearid']);
                $orgAcademicYear = $this->validateAcademicYear($organization->getId(), $rowData['yearid']);

                if (!$isReceiveSurveyVariableValid) {
                    unset($mappedSurveyColumnNames[$receiveSurveyColumn]);

                } else if (!$orgAcademicYear) {
                    $this->errors[] = "Invalid YearId for the organization";
                    unset($mappedSurveyColumnNames[$receiveSurveyColumn]);

                } else {
                    $atLeastOneSurveyVariableIsValid = true;
                }
            } else {
                //unset the column, we don't need to process this, as the user here does not intend to upload this column
                unset($mappedSurveyColumnNames[$receiveSurveyColumn]);
            }

        }

        if (!$atLeastOneSurveyVariableIsValid) {
            return;
        }

        $surveysBasedOnYearId = $this->orgPersonStudentSurveyRepository->getSurveyExternalIds($rowData['yearid']);
        $surveyArray = [];

        foreach ($mappedSurveyColumnNames as $mappedSurveyColumn => $surveyName) {
            $surveyArray[$mappedSurveyColumn] = (isset($surveysBasedOnYearId[$surveyName]) ? $surveysBasedOnYearId[$surveyName] : "");
        }
        $receiveSurveyArray = [];
        foreach ($surveyArray as $receiveSurveyColumnName => $surveyExternalId) {

            // check if survey is present for the academic year , else report error
            if ($surveyExternalId == "") {
                $this->errors[] = "No Survey Available for the academic year";
            } else {
                $receiveSurveyArray[$surveyExternalId] = $rowData[strtolower($receiveSurveyColumnName)];
            }
        }

        return $receiveSurveyArray;

    }
}
