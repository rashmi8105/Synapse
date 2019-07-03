<?php
namespace Synapse\CampusResourceBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\CampusResourceBundle\Entity\OrgCampusResource;
use Synapse\CampusResourceBundle\EntityDto\CampusResourceDto;
use Synapse\CampusResourceBundle\Repository\OrgCampusResourceRepository;
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("campusresource_service")
 */
class CampusResourceService extends AbstractService
{

    const SERVICE_KEY = 'campusresource_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    //services

    /**
     * @var LegacyValidator
     */
    private $legacyValidator;

    /**
     * @var PersonService
     */

    private $personService;

    //repositories

    /**
     * @var OrganizationLangRepository
     */
    private $organizationLangRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCampusResourceRepository
     */
    private $orgCampusResourceRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @DI\InjectParams ({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        // Scaffolding
        $this->container = $container;

        //services
        $this->legacyValidator = $this->container->get(SynapseConstant::VALIDATOR);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);



        //repositories
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCampusResourceRepository = $this->repositoryResolver->getRepository(OrgCampusResourceRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Create Campus Resources
     *
     * @param CampusResourceDto $campusResourceDto
     * @param int $loggedInUserOrganizationId
     * @throws SynapseValidationException
     * @return CampusResourceDto
     */
    public function createCampusResource(CampusResourceDto $campusResourceDto, $loggedInUserOrganizationId)
    {
        $campusResourceDtoOrganizationId = $campusResourceDto->getOrganizationId();
        $organizationEntity = $this->validateOrganization($campusResourceDtoOrganizationId);

        // Make sure organization exists, staff is valid, and creator of request is in the correct organization
        $staffPersonEntity = $this->validateCampusResource($campusResourceDto->getStaffId(), $campusResourceDtoOrganizationId, $loggedInUserOrganizationId);

        // Validate resource name
        $campusResourceObject = $this->orgCampusResourceRepository->findOneBy(
            array(
                "orgId" => $organizationEntity,
                "name" => $campusResourceDto->getResourceName(),
            )
        );
        if ($campusResourceObject) {
            $this->logger->error(" Campus Resource Bundle - Campus Resource Service - createCampusResource - " . "Resource Name already exist" . " " . "resource_name_already_exist");
            throw new SynapseValidationException("Campus Resource name is already in use.");
        }

        $organizationCampusResource = new OrgCampusResource();
        $organizationCampusResource->setOrganization($organizationEntity);
        $organizationCampusResource->setName($campusResourceDto->getResourceName());
        $organizationCampusResource->setPersonId($staffPersonEntity);
        $organizationCampusResource->setPhone($campusResourceDto->getResourcePhoneNumber());
        $organizationCampusResource->setEmail($campusResourceDto->getResourceEmail());
        $organizationCampusResource->setLocation($campusResourceDto->getResourceLocation());
        $organizationCampusResource->setUrl($this->addURLScheme($campusResourceDto->getResourceUrl()));
        $organizationCampusResource->setDescription($campusResourceDto->getResourceDescription());
        $organizationCampusResource->setReceiveReferals($this->convertToBinary($campusResourceDto->getReceiveReferals()));
        $organizationCampusResource->setVisibleToStudent($this->convertToBinary($campusResourceDto->getVisibleToStudents()));

        $this->validateEntity($organizationCampusResource);
        $this->orgCampusResourceRepository->create($organizationCampusResource);

        $this->orgCampusResourceRepository->flush();

        $campusResourceDto->setResourceUrl($organizationCampusResource->getUrl());
        $campusResourceDto->setId($organizationCampusResource->getId());

        return $campusResourceDto;

    }

    /**
     * Performs all necessary validation for a campus resource action (create, update, delete).
     * This is done in conjunction with getting the required entity from the database.
     *
     * @param int $facultyId - ID of the person who is requesting the campus resource modification.
     * @param int $organizationIdFromCampusResourceDto - Organization ID of the destination organization.
     * @param int $organizationIdFromSecurityContext - ID of the organization to which the user belongs.
     * @throws SynapseValidationException
     * @return Person $personEntity - Person object after validating organization
     */
    private function validateCampusResource($facultyId, $organizationIdFromCampusResourceDto, $organizationIdFromSecurityContext)
    {
        $personEntity = $this->personRepository->find($facultyId);
        if (!$personEntity) {
            throw new SynapseValidationException('Requested Faculty Id for Campus Resource not found.');
        }
        // Validate Staff Person
        $staffInDestinationOrganization = $this->orgPersonFacultyRepository->findOneBy([
            'person' => $facultyId,
            'organization' => $organizationIdFromCampusResourceDto
        ]);

        if (is_null($staffInDestinationOrganization)) {
            throw new SynapseValidationException('Requested Faculty is not a member of this organization.');
        }

        // Make sure user is in correct organization to modify campus resource
        if ($organizationIdFromCampusResourceDto != $organizationIdFromSecurityContext) {
            throw new AccessDeniedException('You do not have permission to modify campus resources in this organization.');
        }

        return $personEntity;
    }

    /**
     * Changes the data of the campus resource specified by $campusResourceId
     * to that of the data specified in $campusResourceDto.
     *
     * @param CampusResourceDto $campusResourceDto
     * @param int $campusResourceId
     * @param int $loggedInUserOrganizationId
     * @throws SynapseValidationException
     * @return CampusResourceDto
     */
    public function updateCampusResource(CampusResourceDto $campusResourceDto, $campusResourceId, $loggedInUserOrganizationId)
    {
        $campusResourceObject = $this->orgCampusResourceRepository->find($campusResourceId);
        if (!$campusResourceObject) {
            throw new SynapseValidationException('Campus Resource is not found.');
        }

        $campusResourceDtoOrganizationId = $campusResourceDto->getOrganizationId();

        $organizationEntity = $this->validateOrganization($campusResourceDtoOrganizationId);

        // Make sure organization exists, staff is valid, and creator of request is in the correct organization
        $staffPersonEntity = $this->validateCampusResource(
            $campusResourceDto->getStaffId(),
            $campusResourceDtoOrganizationId,
            $loggedInUserOrganizationId
        );

        // Validate resource name
        $checkResourceName = $this->orgCampusResourceRepository->findOneBy(
            array(
                'orgId' => $organizationEntity,
                'name' => $campusResourceDto->getResourceName(),
            )
        );
        if (isset($checkResourceName) && $checkResourceName->getId() != $campusResourceId) {
            $this->logger->error(" Campus Resource Bundle - Campus Resource Service - createCampusResource - " . "Resource Name already exist" . " " . "resource_name_already_exist");
            throw new SynapseValidationException('Campus Resource name is already in use.');
        }

        $campusResourceObject->setOrganization($organizationEntity);
        $campusResourceObject->setName($campusResourceDto->getResourceName());
        $campusResourceObject->setPersonId($staffPersonEntity);
        $campusResourceObject->setPhone($campusResourceDto->getResourcePhoneNumber());
        $campusResourceObject->setEmail($campusResourceDto->getResourceEmail());
        $campusResourceObject->setLocation($campusResourceDto->getResourceLocation());
        $campusResourceObject->setUrl($this->addURLScheme($campusResourceDto->getResourceUrl()));
        $campusResourceObject->setDescription($campusResourceDto->getResourceDescription());
        $campusResourceObject->setReceiveReferals($this->convertToBinary($campusResourceDto->getReceiveReferals()));
        $campusResourceObject->setVisibleToStudent($this->convertToBinary($campusResourceDto->getVisibleToStudents()));
        $this->validateEntity($campusResourceObject);
        $this->orgCampusResourceRepository->flush();
        $campusResourceDto->setResourceUrl($campusResourceObject->getUrl());
        $campusResourceDto->setId($campusResourceId);

        return $campusResourceDto;
    }

    /**
     * Soft-deletes the campus resource with the id specified.
     * Will throw a SynapseValidationException
     * in validateCampusResource if the user is trying to
     * delete a campus resource in another organization.
     *
     * @param int $campusResourceId
     * @param int $loggedInUserOrganizationId
     * @param int $staffId
     * @return true
     */
    public function deleteCampusResource($campusResourceId, $loggedInUserOrganizationId, $staffId)
    {
        $campusResource = $this->orgCampusResourceRepository->find($campusResourceId);
        if(!$campusResource) {
            throw new SynapseValidationException('Campus Resource is not found.');
        }

        $campusResourceDtoOrganizationId = $campusResource->getOrganization()->getId();
        $this->validateCampusResource($staffId, $campusResourceDtoOrganizationId, $loggedInUserOrganizationId);

        $this->orgCampusResourceRepository->delete($campusResource);
        $this->orgCampusResourceRepository->flush();

        return true;
    }

    /**
     * Returns an array of the given organization's campus resource data.
     *
     * @param string $organizationId
     * @return array
     */
    public function getCampusResources($organizationId)
    {
        $this->logger->debug("Campus Resources -  Get a list of campus resources ");
        $this->validateOrganization($organizationId);
        $getAllCampusResources = $this->orgCampusResourceRepository->getCampusResources($organizationId, NULL);
        $getCampResDetailsResponse = array();
        foreach ($getAllCampusResources as $campusResourceDetails) {
            $campusResourceDto = new CampusResourceDto();
            $campusResourceDto->setId($campusResourceDetails['id']);
            $campusResourceDto->setOrganizationId($campusResourceDetails['organization_id']);
            $campusResourceDto->setResourceName($campusResourceDetails['resource_name']);
            $campusResourceDto->setStaffId($campusResourceDetails['staff_id']);
            $campusResourceDto->setStaffName($this->checkNullResponse($campusResourceDetails["lastname"] . ', ' . $campusResourceDetails["firstname"]));
            $campusResourceDto->setResourcePhoneNumber($this->checkNullResponse($campusResourceDetails["resource_phone_number"]));
            $campusResourceDto->setResourceEmail($this->checkNullResponse($campusResourceDetails["resource_email"]));
            $campusResourceDto->setResourceLocation($this->checkNullResponse($campusResourceDetails["resource_location"]));
            $campusResourceDto->setResourceUrl($this->checkNullResponse($campusResourceDetails["resource_url"]));
            $campusResourceDto->setResourceDescription($this->checkNullResponse($campusResourceDetails["resource_description"]));
            $campusResourceDto->setReceiveReferals($this->checkNullResponse($campusResourceDetails["receive_referals"]));
            $campusResourceDto->setVisibleToStudents($this->checkNullResponse($campusResourceDetails["visible_to_students"]));
            $getCampResDetailsResponse[] = $campusResourceDto;
        }
        $this->logger->debug("Campus Resources -  Get a list of campus resources  - Completed");
        return $getCampResDetailsResponse;
    }


    /**
     * Returns a CampusResourceDTO of the campus resource associated
     * with the provided $campResId
     *
     * @param int|string $campResId
     * @return CampusResourceDto
     */
    public function getCampusResourceDetails($campResId)
    {
        $this->logger->debug(CampusResourceConstants::CAMPUS_RES_ERR_2570_GET);
        $campusResourceDetails = $this->orgCampusResourceRepository->getCampusResources($campResId, 1);

        $this->isObjectExist($campusResourceDetails, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND_CODE, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND, $this->logger);

        foreach ($campusResourceDetails as $campusResourceDetail) {
            $campusResourceDto = new CampusResourceDto();
            $campusResourceDto->setId($campusResourceDetail['id']);
            $campusResourceDto->setOrganizationId($campusResourceDetail['organization_id']);
            $campusResourceDto->setResourceName($campusResourceDetail['resource_name']);
            $campusResourceDto->setStaffId($campusResourceDetail['staff_id']);
            $campusResourceDto->setStaffName($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_LASTNAME] . ', ' . $campusResourceDetail[CampusResourceConstants::FIELD_FIRSTNAME]));
            $campusResourceDto->setResourcePhoneNumber($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_PHONE_NUMBER]));
            $campusResourceDto->setResourceEmail($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_EMAIL]));
            $campusResourceDto->setResourceLocation($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_LOCATION]));
            $campusResourceDto->setResourceUrl($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_URL]));
            $campusResourceDto->setResourceDescription($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_DESCRIPTION]));
            $campusResourceDto->setReceiveReferals($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RECEIVE_REFERALS]));
            $campusResourceDto->setVisibleToStudents($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_VISIABLE_TO_STUDENETS]));
        }

        $this->logger->debug(CampusResourceConstants::CAMPUS_RES_ERR_2570_GET . CampusResourceConstants::MESSAGE_COMPLETED);

        return $campusResourceDto;
    }

    /**
     * Returns an associative array of the provided student's
     * campus resource data.
     *
     * @param int|string $studentId
     * @return array
     */
    public function getCampusResourceForStudent($studentId)
    {
        $this->logger->debug(CampusResourceConstants::STUDENT_VIEW_ESPRJ_2122);
        $person = $this->personService->findPerson($studentId);
        $organization = $this->organizationLangRepository->findOneByOrganization($person->getOrganization()->getId());
        $this->isObjectExist($organization, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND_CODE, CampusResourceConstants::CAMPUS_RESC_NOT_FOUND, $this->logger);

        $getCompleteResource = array();
        $campusResourceDetails = $this->orgCampusResourceRepository->getSingleCampusVisibleResource($person->getOrganization()->getId());
        $getCampResource = array();
        foreach ($campusResourceDetails as $campusResourceDetail) {
            $campusResourceDto = new CampusResourceDto();
            $campusResourceDto->setId($campusResourceDetail[CampusResourceConstants::FIELD_ID]);
            $campusResourceDto->setResourceName($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_NAME]);
            $campusResourceDto->setStaffName($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_LASTNAME] . ', ' . $campusResourceDetail[CampusResourceConstants::FIELD_FIRSTNAME]));
            $campusResourceDto->setResourcePhoneNumber($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_PHONE_NUMBER]));
            $campusResourceDto->setResourceEmail($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_EMAIL]));
            $campusResourceDto->setResourceLocation($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_LOCATION]));
            $campusResourceDto->setResourceUrl($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_URL]));
            $campusResourceDto->setResourceDescription($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RESOURCE_DESCRIPTION]));
            $campusResourceDto->setReceiveReferals($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_RECEIVE_REFERALS]));
            $campusResourceDto->setVisibleToStudents($this->checkNullResponse($campusResourceDetail[CampusResourceConstants::FIELD_VISIABLE_TO_STUDENETS]));
            $getCampResource[] = $campusResourceDto;
        }
        $getCompleteResource[] = [
            'campus_id' => $person->getOrganization()->getId(),
            'campus_name' => $organization->getOrganizationName(),
            'campus_resources' => $getCampResource
        ];

        $rs = [
            'campus_resource_list' => $getCompleteResource
        ];

        $this->logger->debug(CampusResourceConstants::STUDENT_VIEW_ESPRJ_2122 . CampusResourceConstants::MESSAGE_COMPLETED);
        return $rs;
    }

    /**
     * Gets organization from $orgId, and throws a ValidationException
     * if organization is not found.
     *
     * @param string|int $orgId
     * @throws ValidationException
     * @return Organization
     */
    private function validateOrganization($orgId)
    {
        $organization = $this->organizationRepository->find($orgId);
        if (!$organization) {
            $this->logger->error(" Campus Resource Bundle - Campus Announcement Service - validateOrganization -  " . CampusResourceConstants::ORGANIZATION_NOT_FOUND . " " . CampusResourceConstants::ORGANIZATION_NOT_FOUND_CODE);
            throw new ValidationException([
                CampusResourceConstants::ORGANIZATION_NOT_FOUND
            ], CampusResourceConstants::ORGANIZATION_NOT_FOUND, CampusResourceConstants::ORGANIZATION_NOT_FOUND_CODE);
        }
        return $organization;
    }

    /**
     * Uses validator service to make sure entity is valid.
     * If entity is not valid, throws a ValidationException.
     *
     * @param $entity
     * @throws ValidationException
     * @return void
     */
    private function validateEntity($entity)
    {
        /** @var ConstraintViolation[] $errors */
        $errors = $this->legacyValidator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();

            throw new ValidationException([
                $errorsString
            ], $errorsString, 'entity_validation');
        }
    }

    /**
     * Replaces $input with an empty string if it is null.
     *
     * @param string $input
     * @return string
     */
    private function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    /**
     * Checks if object exists (i.e. !$object == true).
     * Throws ValidationException if object evaluates to false.
     *
     * @param $object
     * @param string $message
     * @param $key
     * @param string $errorConst
     * @param Logger $logger
     * @throws ValidationException
     * @return void
     */
    private function isObjectExist($object, $message, $key, $errorConst = '', $logger)
    {
        if (!($object)) {
            $logger->error(" Campus Resource Bundle - Campus Announcement Service - isObjectExist -  " . $errorConst . $message . " " . $key);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Returns 1 if $input is evaluated as true, 0 otherwise.
     *
     * @param string $input
     * @return int
     */
    private function convertToBinary($input)
    {
        return $input ? 1 : 0;
    }

    /**
     * Returns a concatenation of $scheme and $url if the scheme is not present,
     * otherwise returns url.
     *
     * @param string $url
     * @param string $scheme
     * @return string
     */
    private function addURLScheme($url, $scheme = 'http://')
    {
        if (!empty($url)) {
            return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
        }
        return $url;
    }
} 
