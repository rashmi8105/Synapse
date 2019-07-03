<?php
namespace Synapse\PersonBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\FacultyService;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\Service\Impl\PersonService as CoreBundlePersonService;
use Synapse\CoreBundle\Service\Impl\StudentService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Service\Utility\IDConversionService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\DTO\PersonDTO;
use Synapse\PersonBundle\DTO\PersonListDTO;
use Synapse\PersonBundle\DTO\PersonSearchResultDTO;
use Synapse\RiskBundle\Entity\RiskGroupPersonHistory;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Service\Impl\RiskGroupService;


/**
 * @TODO : This class should replace the current person service. When all methods have been moved out of the old person service class, change the class key here to "person_service".
 * @DI\Service("new_person_service")
 */
class PersonService extends AbstractService
{

    const SERVICE_KEY = 'new_person_service';

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    //Repositories

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgRiskGroupModelRepository
     */
    private $orgRiskGroupModelRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var RiskGroupRepository
     */
    private $riskGroupRepository;

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;

    // Services

    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    /**
     * @var CampusConnectionService
     */
    private $campusConnectionService;

    /**
     * @var CoreBundlePersonService
     */
    private $coreBundlePersonService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var FacultyService
     */
    private $facultyService;

    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var IDConversionService
     */
    private $idConversionService;

    /**
     * @var Manager
     */
    public $rbacManager;

    /**
     * @var RiskGroupService
     */
    private $riskGroupService;

    /**
     * @var StudentService
     */
    private $studentService;

    /**
     * @var URLUtilityService
     */
    private $urlUtilityService;

    /**
     * @var LegacyValidator
     */
    private $validatorService;

    /**
     * Person Service Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *            })
     *
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;

        //Repositories
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgRiskGroupModelRepository = $this->repositoryResolver->getRepository(OrgRiskGroupModelRepository::REPOSITORY_KEY);
        $this->riskGroupRepository = $this->repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
        $this->riskGroupPersonHistoryRepository = $this->repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);

        //Service
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->campusConnectionService = $this->container->get(CampusConnectionService::SERVICE_KEY);
        $this->coreBundlePersonService = $this->container->get(CoreBundlePersonService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->facultyService = $this->container->get(FacultyService::SERVICE_KEY);
        $this->groupService = $this->container->get(GroupService::SERVICE_KEY);
        $this->idConversionService = $this->container->get(IDConversionService::SERVICE_KEY);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->riskGroupService = $this->container->get(RiskGroupService::SERVICE_KEY);
        $this->studentService = $this->container->get(StudentService::SERVICE_KEY);
        $this->urlUtilityService = $this->container->get(URLUtilityService::SERVICE_KEY);
        $this->validatorService = $this->container->get(SynapseConstant::VALIDATOR);
    }

    /**
     * Creates persons
     *
     * @param PersonListDTO $personListDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return array
     */
    public function createPersons($personListDTO, $organization, $loggedInUser)
    {
        $responseArray = [];
        $responseErrorArray = [];
        $errorArray = [];
        $errorPerson = [];
        $successfullyCreatedPerson = [];
        $currentDate = new \DateTime('now');

        //Get persons from personDTO
        $personList = $personListDTO->getPersonList();
        foreach ($personList as $personDTO) {

            //creating new person
            $personObject = new Person();
            $personObject->setExternalId($personDTO->getExternalId());
            $personObject->setUsername($personDTO->getPrimaryEmail());
            $personObject->setFirstname($personDTO->getFirstname());
            $personObject->setLastname($personDTO->getLastname());
            $personObject->setOrganization($organization);
            $personObject->setAuthUsername($personDTO->getAuthUsername());
            $personObject->setTitle($personDTO->getTitle());
            $personObject->setIsLocked("y");
            $personObject->setCreatedBy($loggedInUser);
            $personObject->setCreatedAt($currentDate);
            $personObject->setModifiedBy($loggedInUser);
            $personObject->setModifiedAt($currentDate);

            // Validates required attributes of PersonEntity having empty and invalid values
            $requiredErrors = $this->validatorService->validate($personObject, null, ['required']);
            $validateRequiredError = $this->buildEntityValidationErrorArray($requiredErrors);
            if (!empty($validateRequiredError)) {
                // enqueuing required field errors in errorPerson[] and skipping further processing
                $errorRequiredFields = $this->buildPersonResponseArray($personDTO, $validateRequiredError);
                $errorPerson[] = $errorRequiredFields;
                $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organization->getId(), [$errorRequiredFields]);
                unset($personObject);
                continue;
            }

            // Validates optional attributes of PersonEntity having invalid values
            $optionalErrors = $this->validatorService->validate($personObject);
            $validateOptionalError = $this->buildEntityValidationErrorArray($optionalErrors);
            if(!empty($validateOptionalError)) {
                // setting the optional fields (title or auth_username) to null in case there is an invalid value.
                foreach ($validateOptionalError as $propertyPath => $propertyValue) {
                    $errorArray[$propertyPath] = $propertyValue;
                    $setFunctionName = "set" . $propertyPath;
                    $personObject->$setFunctionName(null);
                }
            }

            //creating person and fetching its Internal Id
            $personObject = $this->personRepository->persist($personObject);
            $personInternalId = $personObject->getId();
            $personDTO->setMapworksInternalId($personInternalId);
            $successfullyCreatedPerson[] = $this->buildPersonResponseArray($personDTO);

            // create Student if isStudent is set
            if ($personDTO->getIsStudent()) {

                //create OrgPersonStudent Object
                $studentObject = new OrgPersonStudent();

                // Validating PhotoURL
                if (!empty($personDTO->getPhotoLink())) {
                    $isValidPhoto = $this->urlUtilityService->validatePhotoURL($personDTO->getPhotoLink());
                    if ($isValidPhoto) {
                        $studentObject->setPhotoUrl($personDTO->getPhotoLink());
                    } else {
                        $errorArray['photo_link'] = "Invalid Photo URL. Please try another URL.";
                    }
                }

                // Checking the faculty user have the permission to access this student
                if (!empty($personDTO->getPrimaryCampusConnectionId())) {
                    $responsePrimaryCampus = $this->campusConnectionService->validatePrimaryCampusConnectionId($personDTO->getPrimaryCampusConnectionId(), $organization, $personInternalId);
                    if (is_bool($responsePrimaryCampus)) {
                        $validPerson = $this->personRepository->findOneBy(['externalId' => $personDTO->getPrimaryCampusConnectionId(), 'organization' => $organization]);
                        $studentObject->setPersonIdPrimaryConnect($validPerson);
                    } else {
                        $errorArray['primary_campus_connection_id'] = $responsePrimaryCampus;
                    }
                }

                // validating Risk Group id
                if (!empty($personDTO->getRiskGroupId())) {
                    $responseRiskGroup = $this->riskGroupService->validateRiskGroupBelongsToOrganization($organization->getId(), $personDTO->getRiskGroupId());
                    if (is_bool($responseRiskGroup)) {
                        $riskGroup = $this->riskGroupRepository->find($personDTO->getRiskGroupId());
                        $riskGroupPersonHistoryObject = new RiskGroupPersonHistory();
                        $riskGroupPersonHistoryObject->setPerson($personObject);
                        $riskGroupPersonHistoryObject->setRiskGroup($riskGroup);
                        $riskGroupPersonHistoryObject->setAssignmentDate($currentDate);
                        //persisting and unset RiskGroupPersonHistory object
                        $this->riskGroupPersonHistoryRepository->persist($riskGroupPersonHistoryObject);
                        unset($riskGroupPersonHistoryObject);
                    } else {
                        $errorArray['risk_group_id'] = $responseRiskGroup;
                    }
                }

                //create student auth key using external Id
                $studentAuthKey = $this->coreBundlePersonService->generateAuthKey($personDTO->getExternalId(), 'Student');

                //setting valid values to the orgPersonStudent Object
                $studentObject->setPerson($personObject);
                $studentObject->setOrganization($organization);
                $studentObject->setCreatedAt($currentDate);
                $studentObject->setCreatedBy($loggedInUser);
                $studentObject->setModifiedAt($currentDate);
                $studentObject->setModifiedBy($loggedInUser);
                $studentObject->setAuthKey($studentAuthKey);
                $studentObject->setStatus(1);

                //persisting and unset student object
                $studentObject = $this->orgPersonStudentRepository->persist($studentObject);
                unset($studentObject);

                //adding the student to the AllStudents group
                $this->groupService->addStudentSystemGroup($organization, $personObject);
            }

            // create Faculty if isFaculty is set
            if ($personDTO->getIsFaculty()) {

                //create OrgPersonFaculty Object here as following method is used for both create and update Faculty
                $facultyObject = new OrgPersonFaculty();
                $facultyAuthKey = $this->coreBundlePersonService->generateAuthKey($personDTO->getExternalId(), 'Faculty');

                //setting valid values to the orgPersonFaculty Object
                $facultyObject->setPerson($personObject);
                $facultyObject->setOrganization($organization);
                $facultyObject->setAuthKey($facultyAuthKey);
                $facultyObject->setCreatedAt($currentDate);
                $facultyObject->setCreatedBy($loggedInUser);
                $facultyObject->setModifiedAt($currentDate);
                $facultyObject->setModifiedBy($loggedInUser);
                $facultyObject->setStatus(1);

                //persisting and unset faculty object
                $facultyObject = $this->orgPersonFacultyRepository->persist($facultyObject);
                unset($facultyObject);
            }

            if (!empty($errorArray)) {
                $errorPerson[] = $this->buildPersonResponseArray($personDTO, $errorArray);
            }
            unset($personObject);
        }

        $responseArray['created_count'] = count($successfullyCreatedPerson);
        $responseArray['created_records'] = $successfullyCreatedPerson;

        $responseErrorArray['error_count'] = count($errorPerson);
        $responseErrorArray['error_records'] = $errorPerson;

        $returnArray['data'] = $responseArray;
        $returnArray['errors'] = $responseErrorArray;
        return $returnArray;
    }

    /**
     * Formats the return data when creating a person
     *
     * @param PersonDTO $personDTO
     * @param array $errorMessage
     * @return array
     */
    private function buildPersonResponseArray($personDTO, $errorMessage = [])
    {
        $personArray = $responseArray = [];
        if (array_key_exists('externalId', $errorMessage)) {
            $errorMessage['external_id'] = $errorMessage['externalId'];
        }
        if (array_key_exists('organization', $errorMessage)) {
            $errorMessage['external_id'] = $errorMessage['organization'];
        }
        $personArray['external_id'] = $personDTO->getExternalId();
        $personArray['mapworks_internal_id'] = $personDTO->getMapworksInternalId();
        if (array_key_exists('authUsername', $errorMessage)) {
            $errorMessage['auth_username'] = $errorMessage['authUsername'];
        }
        $personArray['auth_username'] = $personDTO->getAuthUsername();
        $personArray['firstname'] = $personDTO->getFirstname();
        $personArray['lastname'] = $personDTO->getLastname();
        $personArray['title'] = $personDTO->getTitle();
        if (array_key_exists('username', $errorMessage)) {
            $errorMessage['primary_email'] = $errorMessage['username'];
        }
        $personArray['primary_email'] = $personDTO->getPrimaryEmail();
        if (!empty($personDTO->getPhotoLink())) {
            $personArray['photo_link'] = $personDTO->getPhotoLink();
        }
        if (!empty($personDTO->getPrimaryCampusConnectionId())) {
            $personArray['primary_campus_connection_id'] = $personDTO->getPrimaryCampusConnectionId();
        }
        if (!empty($personDTO->getRiskGroupId())) {
            $personArray['risk_group_id'] = $personDTO->getRiskGroupId();
        }
        $personArray['is_student'] = $personDTO->getIsStudent();
        $personArray['is_faculty'] = $personDTO->getIsFaculty();

        $responseArray = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($personArray, $errorMessage);
        return $responseArray;
    }

    /**
     * Gets the user list based on type and search text, without considering permissions.
     *
     * @param int $organizationId
     * @param string|null $searchText
     * @param string|null $userType - faculty|student|dual_role|orphan
     * @param int|null $pageNumber
     * @param int|null $recordsPerPage
     * @return PersonSearchResultDTO
     */
    public function getMapworksPersons($organizationId, $searchText = null, $userType = null, $pageNumber = null, $recordsPerPage = null)
    {

        switch ($userType) {
            case "student" :
                $personIdArray = $this->personRepository->getMapworksStudents($organizationId, $searchText);
                break;
            case "faculty":
                $personIdArray = $this->personRepository->getMapworksFaculty($organizationId, $searchText);
                break;
            case "orphan":
                $personIdArray = $this->personRepository->getMapworksOrphanUsers($organizationId, $searchText);
                break;
            case "dual_role" :
                $personIdStudents = $this->personRepository->getMapworksStudents($organizationId, $searchText);
                $personIdFaculties = $this->personRepository->getMapworksFaculty($organizationId, $searchText);
                $personIdArray = array_intersect($personIdStudents, $personIdFaculties);
                break;
            default:
                $personIdArray = $this->personRepository->getMapworksPersons($organizationId, $searchText);
        }

        $personCount = count(array_unique($personIdArray));

        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        if (empty($recordsPerPage)) {
            $recordsPerPage = $personCount;
        } else {
            $recordsPerPage = (int)$recordsPerPage;
        }

        $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;
        $totalPages = ceil($personCount / $recordsPerPage);

        $personListWithMetaData = $this->personRepository->getMapworksPersonData($organizationId, $personIdArray, $offset, $recordsPerPage);

        $personSearchResultDto = new PersonSearchResultDTO();
        $personSearchResultDto->setCurrentPage($pageNumber);
        $personSearchResultDto->setPersonList($personListWithMetaData);
        $personSearchResultDto->setTotalRecords($personCount);
        $personSearchResultDto->setTotalPages($totalPages);
        $personSearchResultDto->setRecordsPerPage($recordsPerPage);
        return $personSearchResultDto;

    }


    /**
     * Update persons
     *
     * @param PersonListDTO $personListDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return array
     */
    public function updatePersons($personListDTO, $organization, $loggedInUser)
    {
        $updatedRowsArray = [];
        $errorRowsArray = [];
        $skippedRowsArray = [];

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();

        $personList = $personListDTO->getPersonList();
        foreach ($personList as $personDTO) {
            try {
                $externalId = $personDTO->getExternalId();
                $personObject = $this->personRepository->findOneBy(['externalId' => $externalId, 'organization' => $organization]);
                if (empty($personObject)) {
                    $dataProcessingExceptionHandler->addErrors("Person ID {$externalId} is not valid at the organization.", "external_id", "required");
                    $this->entityValidationService->throwErrorIfContains($dataProcessingExceptionHandler, "required");
                } else {
                    // Check $personDTO duplicate with $personObject, add it to $skippedRowsArray if its true
                    $isDuplicateRecord  = $this->verifyPersonDtoToPersonObject($personDTO, $organization);
                    if($isDuplicateRecord){
                        $skippedRows = $this->buildPersonResponseArray($personDTO);
                        $skippedRowsArray[] = $skippedRows;
                        continue;
                    }

                    /** Purpose of having this cloned, is to retain the old values of the record, Say we have a person who has a title "Mr",
                     *  Now we are trying to update the person with a title more than 45 characters (which is an error), so we need to revert back to the older value,
                     *  this is where this clone object will come handy. Used in line no. 546
                     */
                    $clonePersonObject = clone($personObject);

                    // Set the person entity with all attribute of PersonDTO
                    $personObject = $this->modifyPersonObject($personObject, $personDTO, $organization, $loggedInUser);

                    // Nullify fields of Person object
                    $fieldsToClearArray = $personDTO->getFieldsToClear();
                    if (!empty($fieldsToClearArray)) {
                        $personObject = $this->entityValidationService->nullifyFieldsToBeCleared($personObject, $fieldsToClearArray);
                    }

                    // Validate person object with required and optional group
                    $dataProcessingExceptionHandler = $this->entityValidationService->validateAllDoctrineEntityValidationGroups($personObject, $dataProcessingExceptionHandler, ['required' => true, null => false]);

                    // Restore the optional parameter
                    $personObject = $this->entityValidationService->restoreErroredProperties($personObject, $clonePersonObject, $dataProcessingExceptionHandler);

                    // Update person entity
                    $this->personRepository->persist($personObject);

                    // Get the person internal id and setting it to personDTO for further uses
                    $personDTO->setMapworksInternalId($personObject->getId());
                    $updatedRows = $this->buildPersonResponseArray($personDTO);
                    $updatedRowsArray[] = $updatedRows;

                    // Update student
                    $this->studentService->determineStudentUpdateType($personObject, $personDTO, $organization, $loggedInUser, $dataProcessingExceptionHandler);

                    // Update faculty
                    $this->facultyService->determineFacultyUpdateType($personObject, $personDTO, $organization, $loggedInUser);

                    // Unset person object and clone object
                    unset($personObject);
                    unset($clonePersonObject);

                    // Throw error object if error object having any optional error
                    if (!empty($dataProcessingExceptionHandler->getAllErrors())) {
                        $this->entityValidationService->throwErrorIfContains($dataProcessingExceptionHandler);
                    }

                }

            } catch (DataProcessingExceptionHandler $dpeh) {
                $personErrorArray = [];
                $allErrorsArray = $dpeh->getAllErrors();
                foreach ($allErrorsArray as $errorArray) {
                    foreach ($errorArray as $field => $value) {
                        $personErrorArray[$field] = $value;
                        break;
                    }
                }
                $errorMessageArray = $this->buildPersonResponseArray($personDTO, $personErrorArray);
                $errorRowsArray[] = $errorMessageArray;
            }
            // Reset all errors for each PersonDTO
            $dataProcessingExceptionHandler->resetAllErrors();
        }

        $responseArray = [];
        $responseArray['errors']['error_count'] = count($errorRowsArray);
        $responseArray['errors']['error_records'] = $errorRowsArray;
        $responseArray['data']['updated_count'] = count($updatedRowsArray);
        $responseArray['data']['updated_records'] = $updatedRowsArray;
        $responseArray['data']['skipped_count'] = count($skippedRowsArray);
        $responseArray['data']['skipped_records'] = $skippedRowsArray;

        return $responseArray;
    }

    /**
     * Modify person object with the applicable attributes of PersonDTO
     *
     * @param Person $person
     * @param PersonDTO $personDTO
     * @param Organization $organization
     * @param Person $loggedInUser
     * @return Person
     */
    private function modifyPersonObject($person, $personDTO, $organization, $loggedInUser)
    {
        $currentDate = new \DateTime('now');
        $person->setFirstname($personDTO->getFirstname());
        $person->setLastname($personDTO->getLastname());
        $person->setUsername($personDTO->getPrimaryEmail());
        $person->setTitle($personDTO->getTitle());
        $person->setAuthUsername($personDTO->getAuthUsername());
        $person->setOrganization($organization);
        $person->setModifiedBy($loggedInUser);
        $person->setModifiedAt($currentDate);

        return $person;
    }

    /**
     * Gets student list with current risk and intent to leave information based, optionally, on search text, risk group, and cohort
     * Optional Pagination
     *
     * @param int $organizationId
     * @param string|null $searchText
     * @param int|null $cohort
     * @param int|null $riskGroupId
     * @param int|null $pageNumber
     * @param int|null $recordsPerPage
     * @return array
     */
    public function getCurrentRiskAndIntentToLeaveForOrganization($organizationId, $searchText = null, $cohort = null, $riskGroupId = null, $pageNumber = null, $recordsPerPage = null)
    {

        $currentDate = new \DateTime('now');
        $currentDateTimeString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $orgAcademicYearData = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateTimeString, $organizationId);
        if ($orgAcademicYearData) {
            $currentOrgAcademicYearId = $orgAcademicYearData[0]['org_academic_year_id'];
        } else {
            throw new SynapseValidationException("There are no cohorts available due to there being no current or past academic years.");
        }

        if (!is_numeric($pageNumber)) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        $offset = 0;
        if (is_numeric($recordsPerPage)) {
            $recordsPerPage = (int)$recordsPerPage;
            $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;
        }

        $riskAndIntentToLeaveData = $this->personRepository->getPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria($organizationId, $currentOrgAcademicYearId, $searchText, $cohort, $riskGroupId, $recordsPerPage, $offset);

        if (is_numeric($recordsPerPage)) {
            $riskAndIntentToLeaveDataForCount = $this->personRepository->getMapworksStudents($organizationId, $searchText, $riskGroupId, $cohort, $currentOrgAcademicYearId);

            $riskAndIntentToLeaveDataCount = count($riskAndIntentToLeaveDataForCount);
            $totalPages = ceil($riskAndIntentToLeaveDataCount / $recordsPerPage);
        } else {
            $totalPages = 1;
            $recordsPerPage = count($riskAndIntentToLeaveData);
            $riskAndIntentToLeaveDataCount = count($riskAndIntentToLeaveData);
        }
        $personSearchResultDto = new PersonSearchResultDTO();
        $personSearchResultDto->setCurrentPage($pageNumber);
        $personSearchResultDto->setPersonList($riskAndIntentToLeaveData);
        $personSearchResultDto->setTotalRecords($riskAndIntentToLeaveDataCount);
        $personSearchResultDto->setTotalPages($totalPages);
        $personSearchResultDto->setRecordsPerPage($recordsPerPage);
        return $personSearchResultDto;
    }

    /**
     * Checking if the PersonDto is an duplicate, The method returns false on the first occurrence where it finds out  the person dto is not a duplicate
     *
     * @param PersonDTO $personDto
     * @param Organization $organization
     * @return bool
     */
    private function verifyPersonDtoToPersonObject($personDto, $organization)
    {
        $personObject = $this->personRepository->findOneBy([
            'externalId' => $personDto->getExternalId(),
            'firstname' => $personDto->getFirstname(),
            'lastname' => $personDto->getLastname(),
            'username' => $personDto->getPrimaryEmail(),
            'title' => $personDto->getTitle(),
            'authUsername' => $personDto->getAuthUsername(),
            'organization' => $organization
        ]);

        if (!$personObject) {
            return false;
        }

        $primaryConnectionId = $personDto->getPrimaryCampusConnectionId();
        if (!is_null($primaryConnectionId)) {
            $primaryConnectionObject = $this->personRepository->findOneBy(['externalId' => $primaryConnectionId]);
        } else {
            $primaryConnectionObject = null;
        }
        $orgPersonStudent = $this->orgPersonStudentRepository->findOneBy([
            'person' => $personObject,
            'organization' => $personObject->getOrganization(),
            'photoUrl' => $personDto->getPhotoLink(),
            'personIdPrimaryConnect' => $primaryConnectionObject
        ]);

        //Person Student
        if (($personDto->getIsStudent() == 1 && is_null($orgPersonStudent))) {
            return false;
        }

        if (($personDto->getIsStudent() == 0 && !is_null($orgPersonStudent))) {
            return false;
        }

        // getting the Last risk group id from riskGroup History
        $riskGroup = $this->riskGroupPersonHistoryRepository->findOneBy([
            'person' => $personObject
        ], ['assignmentDate' => 'DESC']);

        if (!is_null($personDto->getRiskGroupId()) && is_null($riskGroup)) {
            return false;
        }

        if ($riskGroup && $riskGroup->getRiskGroup()->getId() != $personDto->getRiskGroupId()) {
            return false;
        }

        $orgPersonFaculty = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $personObject,
            'organization' => $organization
        ]);

        if (($personDto->getIsFaculty() == 1 && is_null($orgPersonFaculty)) || ($personDto->getIsFaculty() == 0 && !is_null($orgPersonFaculty))) {
            return false;
        }

        return true;
    }
}