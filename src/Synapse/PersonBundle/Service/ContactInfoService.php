<?php
namespace Synapse\PersonBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\DTO\ContactInfoDTO;
use Synapse\PersonBundle\DTO\ContactInfoListDTO;
use Synapse\PersonBundle\DTO\PersonSearchResultDTO;
use Synapse\PersonBundle\Repository\ContactInfoRepository;


/**
 * @DI\Service("contact_info_service")
 */
class ContactInfoService extends AbstractService
{

    const SERVICE_KEY = 'contact_info_service';

    /**
     * This variable is used for the entity field and the json attribute mapping.This is needed when the doctrine entity is validated.
     *
     * @var array
     */
    private $jsonEntityFieldMap = [

        'externalId' => 'external_id',
        'address1' => 'address_one',
        'address2' => 'address_two',
        'primaryMobile' => 'primary_mobile',
        'alternateMobile' => 'alternate_mobile',
        'homePhone' => 'home_phone',
        'alternateEmail' => 'alternate_email',
        'primaryMobileProvider' => 'primary_mobile_provider',
        'alternateMobileProvider' => 'alternate_mobile_provider'
    ];

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    //Repositories

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    // Services

    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * ContactInfo Service Constructor
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
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        //Service
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
    }


    /**
     * Creates the contact information for the specified users.
     *
     * @param int $organizationId
     * @param ContactInfoListDTO $contactInfoListDTO
     * @return array
     * @throws null|DataProcessingExceptionHandler
     */
    public function createUsersContactInfo($organizationId, $contactInfoListDTO)
    {
        $contactInformationArray = $contactInfoListDTO->getContactInformation();
        $errorArray = [];
        $createdRecords = [];

        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($contactInformationArray as $contactInformation) {
            try {
                $personExternalId = $contactInformation->getExternalId();
                $dataProcessingExceptionHandler->addErrors('External Id ' . $personExternalId . ' is not valid at the organization.', 'external_id', 'required');
                $personEntity = $this->personRepository->findOneBy(['externalId' => $personExternalId, 'organization' => $organizationId], $dataProcessingExceptionHandler, null);
                $dataProcessingExceptionHandler->resetAllErrors();
                $personContact = $personEntity->getContacts();
                if (count($personContact->getValues()) > 0) {
                    $dataProcessingExceptionHandler->addErrors("Contact Information already exists for this user.", "external_id");
                    $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $dataProcessingExceptionHandler->getAllErrors());
                    throw $dataProcessingExceptionHandler;
                }

                $optionalError = $this->setContactInfoForCreate($contactInformation, $personEntity, $dataProcessingExceptionHandler);
                $createdRecords[] = $contactInformation;
                if ($optionalError) {
                    $dataProcessingExceptionHandler = $optionalError;
                    if (count($dataProcessingExceptionHandler->getAllErrors()) > 0) {
                        throw $dataProcessingExceptionHandler;
                    }
                }
            } catch (DataProcessingExceptionHandler $dpeh) {
                $allErrorsArray = $dpeh->getAllErrors();
                $contactInfoArray = $this->convertContactInfoDtoToArray($contactInformation);
                $errorData = [];
                foreach ($allErrorsArray as $error) {
                    $dataProcessingExceptionHandler->resetAllErrors();
                    $errorKeys = array_keys($error);
                    $errorData[$errorKeys[0]] = $error[$errorKeys[0]];
                }
                $errorArray[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($contactInfoArray, $errorData, $this->jsonEntityFieldMap);
            }
        }
        return [
            'data' => [
                'created_count' => count($createdRecords),
                'created_records' => $createdRecords
            ],
            'error' => [
                'error_count' => count($errorArray),
                'error_data' => $errorArray
            ]
        ];
    }

    /**
     * convert contact information from DTO to Array to set error message
     *
     * @param ContactInfoDTO $contactInformation
     * @return array $contactInfoArray
     */
    private function convertContactInfoDtoToArray($contactInformation)
    {
        $contactInfoArray = [];
        $contactInfoArray['external_id'] = $contactInformation->getExternalId();
        $contactInfoArray['address_one'] = $contactInformation->getAddressOne();
        $contactInfoArray['address_two'] = $contactInformation->getAddressTwo();
        $contactInfoArray['city'] = $contactInformation->getCity();
        $contactInfoArray['state'] = $contactInformation->getState();
        $contactInfoArray['zip'] = $contactInformation->getZip();
        $contactInfoArray['country'] = $contactInformation->getCountry();
        $contactInfoArray['primary_mobile'] = $contactInformation->getPrimaryMobile();
        $contactInfoArray['alternate_mobile'] = $contactInformation->getAlternateMobile();
        $contactInfoArray['home_phone'] = $contactInformation->getHomePhone();
        $contactInfoArray['alternate_email'] = $contactInformation->getAlternateEmail();
        $contactInfoArray['primary_mobile_provider'] = $contactInformation->getPrimaryMobileProvider();
        $contactInfoArray['alternate_mobile_provider'] = $contactInformation->getAlternateMobileProvider();
        return $contactInfoArray;
    }

    /**
     * set contact information to the contact info object.
     *
     * @param ContactInfo $contactInfoObject
     * @param Person $personEntity
     * @param ContactInfoDTO $contactInformation
     * @return ContactInfo
     */
    private function setContactInfoObject($contactInfoObject, $personEntity, $contactInformation)
    {
        $contactInfoObject->setAddress1($contactInformation->getAddressOne());
        $contactInfoObject->setAddress2($contactInformation->getAddressTwo());
        $contactInfoObject->setCity($contactInformation->getCity());
        $contactInfoObject->setState($contactInformation->getState());
        $contactInfoObject->setZip($contactInformation->getZip());
        $contactInfoObject->setCountry($contactInformation->getCountry());
        $contactInfoObject->setPrimaryMobile($contactInformation->getPrimaryMobile());
        $contactInfoObject->setAlternateMobile($contactInformation->getAlternateMobile());
        $contactInfoObject->setHomePhone($contactInformation->getHomePhone());
        $contactInfoObject->setAlternateEmail($contactInformation->getAlternateEmail());
        $contactInfoObject->setPrimaryMobileProvider($contactInformation->getPrimaryMobileProvider());
        $contactInfoObject->setAlternateMobileProvider($contactInformation->getAlternateMobileProvider());
        $contactInfoObject->setPrimaryEmail($personEntity->getUsername());
        return $contactInfoObject;
    }

    /**
     * Create and persist contact info entity
     *
     * @param ContactInfoDTO $contactInformation
     * @param Person $personEntity
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     * @return null|DataProcessingExceptionHandler
     */
    private function setContactInfoForCreate($contactInformation, $personEntity, $dataProcessingExceptionHandler)
    {
        $contactInfoObject = new ContactInfo();
        $contactInfoObject = $this->setContactInfoObject($contactInfoObject, $personEntity, $contactInformation);
        $optionalExceptionObject = $this->entityValidationService->validateDoctrineEntity($contactInfoObject, $dataProcessingExceptionHandler, null, false);
        // If optional fields have invalid values, set null for those fields and create record with other valid value.
        if ($optionalExceptionObject) {
            foreach ($optionalExceptionObject->getAllErrors() as $error) {
                $keys = array_keys($error);
                $fieldName = $keys[0];
                $convertedAttribute = $this->entityValidationService->replaceUnderScoreToCamelCase($fieldName);
                $setFunction = 'set' . ucfirst($convertedAttribute);
                $contactInfoObject->$setFunction(null);
            }
        }
        $personEntity->addContact($contactInfoObject);
        $this->personRepository->persist($personEntity);
        return $optionalExceptionObject;
    }

    /**
     * Gets the user's contact information
     *
     * @param integer $organizationId
     * @param string $personFilter
     * @param string $contactFilter - search text which would for filtering the results
     * @param string $contactFilterType -  valid values would be address/phone and by default the filter text would be used to search in all contact fields or the result would be filtered based on the contactFilterType text
     * @param integer $pageNumber
     * @param integer $recordsPerPage
     * @return PersonSearchResultDTO
     */
    public function getUsersContactInfo($organizationId, $personFilter, $contactFilter, $contactFilterType, $pageNumber, $recordsPerPage)
    {

        $contactInfoPersonIds = $this->contactInfoRepository->getPersonIdsBasedOnContactInfoFilters($organizationId, $personFilter, $contactFilter, $contactFilterType);
        $personCount = count($contactInfoPersonIds);

        if (empty($pageNumber)) {
            $pageNumber = SynapseConstant::DEFAULT_PAGE_NUMBER;
        }
        if (empty($recordsPerPage)) {
            $recordsPerPage = $personCount;
        } else {
            $recordsPerPage = (int)$recordsPerPage;
        }
        $totalPages = ceil($personCount / $recordsPerPage);
        $offset = ($pageNumber * $recordsPerPage) - $recordsPerPage;

        if ($personCount) {
            $contactInfoData = $this->contactInfoRepository->getUsersContactInfo($organizationId, $contactInfoPersonIds, $offset, $recordsPerPage);
        } else {
            $contactInfoData = [];
        }

        $personSearchResultDto = new PersonSearchResultDTO();
        $personSearchResultDto->setCurrentPage($pageNumber);
        $personSearchResultDto->setTotalPages($totalPages);
        $personSearchResultDto->setTotalRecords($personCount);
        $personSearchResultDto->setPersonList($contactInfoData);
        $personSearchResultDto->setRecordsPerPage($recordsPerPage);

        return $personSearchResultDto;
    }

    /**
     * Updates the contact information for the specified users.
     *
     * @param int $organizationId
     * @param ContactInfoListDTO $contactInfoListDTO
     * @return array
     * @throws bool|DataProcessingExceptionHandler|null
     */
    public function updateUsersContactInfo($organizationId, $contactInfoListDTO)
    {
        $updatedContactInfoArray = [];
        $skippedContactInfoArray = [];
        $error = [];
        $contactInfoList = $contactInfoListDTO->getContactInformation();
        $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
        foreach ($contactInfoList as $contactInfoDTO) {
            try {
                $dataProcessingExceptionHandler->resetAllErrors();
                $personExternalId = $contactInfoDTO->getExternalId();

                //Check for valid person
                $dataProcessingExceptionHandler->addErrors('Not a Valid Person', "external_id", "required");
                $personObject = $this->personRepository->findOneBy([
                    'externalId' => $personExternalId,
                    'organization' => $organizationId
                ], $dataProcessingExceptionHandler, null);

                $dataProcessingExceptionHandler->resetAllErrors();

                //Check for Contact exists or not for the person
                $personContacts = $personObject->getContacts()->getValues();
                if (!$personContacts) {
                    $dataProcessingExceptionHandler->addErrors('Contact Information does not exist for this user', "external_id", "required");
                    throw $dataProcessingExceptionHandler;

                } else {
                    //This just pulls the top one off the stack.  There should only be one
                    $contactInfoObject = current($personContacts);


                    $optionalError = null;

                    //Keeping clone of the ContactInfoObject so that it can be used later to restore value
                    $cloneContactInfoObject = clone($contactInfoObject);

                    //Setting ContactInfo Object with ContactInfoDTO
                    $contactInfoObject = $this->setContactInfoObject($contactInfoObject, $personObject, $contactInfoDTO);
                    
                    // Nullify fields of ContactInfo object
                    $fieldsToClearArray = $contactInfoDTO->getFieldsToClear();
                    if (!empty($fieldsToClearArray)) {
                        $contactInfoObject = $this->entityValidationService->nullifyFieldsToBeCleared($contactInfoObject, $fieldsToClearArray);
                    }

                    $optionalError = $this->entityValidationService->validateDoctrineEntity($contactInfoObject, $dataProcessingExceptionHandler, null, false);

                    // Restore the optional parameter
                    $contactInfoObject = $this->entityValidationService->restoreErroredProperties($contactInfoObject, $cloneContactInfoObject, $dataProcessingExceptionHandler);

                    $this->contactInfoRepository->persist($contactInfoObject);

                    if ($cloneContactInfoObject == $contactInfoObject) {
                        $skippedContactInfoArray[] = $contactInfoDTO;
                    } else {
                        $updatedContactInfoArray[] = $contactInfoDTO;
                    }

                    // Throw error
                    if ($optionalError) {
                        $dataProcessingExceptionHandler = $optionalError;
                        if (count($dataProcessingExceptionHandler->getAllErrors()) > 0) {
                            throw $dataProcessingExceptionHandler;
                        }
                    }
                }
            } catch (DataProcessingExceptionHandler $dpeh) {

                $contactInfoArray = $this->convertContactInfoDtoToArray($contactInfoDTO);

                $errorArray = $dpeh->getAllErrors();

                $errorDataArray = [];
                foreach ($errorArray as $errorData) {
                    if ($errorData['type'] == 'required') {
                        $this->apiValidationService->updateOrganizationAPIValidationErrorCount($organizationId, $dpeh->getAllErrors());
                    }
                    $keys = array_keys($errorData);
                    $errorDataArray[$keys[0]] = $errorData[$keys[0]];
                }
                if (count($errorArray) > 0) {
                    $error[] = $this->dataProcessingUtilityService->setErrorMessageOrValueInArray($contactInfoArray, $errorDataArray, $this->jsonEntityFieldMap);
                }
            }
        }
        return [
            'data' => [
                'updated_count' => count($updatedContactInfoArray),
                'updated_records' => $updatedContactInfoArray,
                'skipped_count' => count($skippedContactInfoArray),
                'skipped_records' => $skippedContactInfoArray
            ],
            'errors' => [
                'error_count' => count($error),
                'error_records' => $error
            ]
        ];
    }


}