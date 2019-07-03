<?php

namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;

/**
 * Class for functions converting external ID values to internal ID values, or vice versa.
 *
 * @package Synapse\CoreBundle\Service\Utility
 *
 * @DI\Service("id_conversion_service")
 *
 */
class IDConversionService extends AbstractService
{

    const SERVICE_KEY = "id_conversion_service";

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    // Repositories

    /**
     * @var OrgCoursesRepository
     */
    private $organizationCourseRepository;

    /**
     * @var OrgGroupRepository
     */
    private $organizationGroupRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    // Services
    /**
     * @var APIValidationService
     */
    private $apiValidationService;

    // Private variables

    /**
     * @var array
     */
    public $validationErrors = [];


    /**
     * IDConversionService constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;

        // Repositories
        $this->organizationCourseRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->organizationGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        //Services
        $this->apiValidationService = $this->container->get(APIValidationService::SERVICE_KEY);
    }

    /**
     * Convert internal or external person IDs to the opposite, based on the flag ($isInternalIds)
     *
     * @param string $personIds - Comma separated value of Person IDs
     * @param int $organizationId - Organization ID that each person should be validated against
     * @param bool $isInternalIds - The passed in personIds values are internal, and should be converted to external ($isInternalIds = true)
     *                              or external and should be converted to internal ($isInternalIds = false).
     * @return array
     */
    public function convertPersonIds($personIds, $organizationId, $isInternalIds = true)
    {
        if ($personIds) {
            $personIdsArray = explode(',', $personIds);
        } else {
            $personIdsArray = [];
        }
        $convertedPersonIds = [];

        foreach ($personIdsArray as $personId) {
            if ($isInternalIds) {
                $personObject = $this->personRepository->find($personId);
                if ($personObject && $personObject->getOrganization()->getId() == $organizationId) {
                    $convertedPersonIds[] = $personObject->getExternalId();
                } else {
                    $this->validationErrors[] = [$personId => "Person ID {$personId} is not valid at the organization."];
                }
            } else {
                $personObject = $this->personRepository->findOneBy([
                    'externalId' => $personId,
                    'organization' => $organizationId
                ]);
                if ($personObject && $personObject->getOrganization()->getId() == $organizationId) {
                    $convertedPersonIds[] = $personObject->getId();
                } else {
                    $this->validationErrors[] = [$personId => "Person ID {$personId} is not valid at the organization."];
                }
            }
            unset($personObject);
        }

        return $convertedPersonIds;
    }

    /**
     * Convert internal or external course IDs to the opposite, based on the flag ($isInternalIds)
     *
     * @param string $courseIds - Comma separated value of course IDs
     * @param int $organizationId - Organization ID of the API coordinator
     * @param bool $isInternalIds - The passed in courseId values are internal, and should be converted to external ($isInternalIds = true)
     *                              or external and should be converted to internal ($isInternalIds = false).
     * @return array
     */
    public function convertCourseIDs($courseIds, $organizationId, $isInternalIds = true)
    {
        $courseIdArray = explode(',', $courseIds);
        $convertedCourseIds = [];
        foreach ($courseIdArray as $courseId) {
            if ($isInternalIds) {
                $courseObject = $this->organizationCourseRepository->find($courseId);
                if ($courseObject && $courseObject->getOrganization()->getId() == $organizationId) {
                    $convertedCourseIds[] = $courseObject->getExternalId();
                } else {
                    $this->validationErrors[] = [$courseId => "Course ID {$courseId} is not valid at the organization."];
                }
            } else {
                $courseObject = $this->organizationCourseRepository->findOneBy([
                    'externalId' => $courseId,
                    'organization' => $organizationId
                ]);
                if ($courseObject && $courseObject->getOrganization()->getId() == $organizationId) {
                    $convertedCourseIds[] = $courseObject->getId();
                } else {
                    $this->validationErrors[] = [$courseId => "Course ID {$courseId} is not valid at the organization."];
                }
            }
            unset($courseObject);
        }
        return $convertedCourseIds;
    }

    /**
     * Get array of group objects by passing array of group internal or external group Ids
     *
     * @param array $groupIdArray
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return array
     */
    public function getConvertedGroupObjects($groupIdArray, $organizationId, $isInternalIds)
    {
        $convertedGroupIds = $this->convertGroupIds($groupIdArray, $organizationId, $isInternalIds);
        $validationErrors = $this->createErrorsFromConvertedGroupIds($convertedGroupIds);
        $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds);
        $convertedGroupIds = array_values($convertedGroupIds);
        return $convertedGroupIds;
    }

    /**
     * Get group objects by passing internal or external group Id
     *
     * @param int|string $groupId
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return mixed
     */
    public function getConvertedGroupObject($groupId, $organizationId, $isInternalIds)
    {
        $convertedGroupObject = $this->convertGroupObject($groupId, $organizationId, $isInternalIds);
        $validationErrors = $this->createErrorsFromConvertedGroupIds([$groupId => $convertedGroupObject]);
        $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds);
        return $convertedGroupObject;
    }

    /**
     * Convert internal or external group IDs to the opposite, based on the flag ($isInternalIds)
     *
     * @param array $groupIdArray
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return array
     */
    private function convertGroupIds($groupIdArray, $organizationId, $isInternalIds)
    {
        $internalGroupIds = [];
        foreach ($groupIdArray as $groupId) {
            $groupObject = $this->convertGroupObject($groupId, $organizationId, $isInternalIds);
            if (empty($groupObject)) {
                $internalGroupIds[$groupId] = $groupObject;
            } else {
                $internalGroupIds[$groupId] = $groupObject->getId();
            }
        }
        return $internalGroupIds;
    }

    /**
     * Gets group object based on isInternal true or false
     *
     * @param int $groupId
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return mixed
     */
    private function convertGroupObject($groupId, $organizationId, $isInternalIds = true)
    {
        if ($isInternalIds) {
            $returnGroupObject = $this->convertGroupIdToExternalId($groupId, $organizationId);
        } else {
            $returnGroupObject = $this->convertExternalIdToGroupId($groupId, $organizationId);
        }
        return $returnGroupObject;
    }

    /**
     * Get group object based on groups Internal Id
     *
     * @param int $groupId
     * @param int $organizationId
     * @return OrgGroup|null
     */
    private function convertGroupIdToExternalId($groupId, $organizationId)
    {
        $groupObject = $this->organizationGroupRepository->find($groupId);

        if ($groupObject && $groupObject->getOrganization()->getId() == $organizationId) {
            return $groupObject;
        }
        return null;
    }

    /**
     * Get group object based on groups External Id
     *
     * @param int $groupId
     * @param int $organizationId
     * @return OrgGroup|null
     */
    private function convertExternalIdToGroupId($groupId, $organizationId)
    {
        $groupObject = $this->organizationGroupRepository->findOneBy([
            'externalId' => $groupId,
            'organization' => $organizationId
        ]);

        if ($groupObject && $groupObject->getOrganization()->getId() == $organizationId) {
            return $groupObject;
        }
        return null;
    }

    /**
     * Create validation error array from converted group ids
     *
     * @param array $convertedGroupIds
     * @return array
     */
    public function createErrorsFromConvertedGroupIds($convertedGroupIds)
    {
        $validationErrors = [];
        foreach ($convertedGroupIds as $convertedGroupIdKey => $convertedGroupIdValue) {
            if (!$convertedGroupIdValue) {
                $validationErrors[] = [$convertedGroupIdKey => "Group ID {$convertedGroupIdKey} is not valid at the organization."];
            }
        }
        return $validationErrors;
    }


    /**
     * Get array of course objects using course ids
     *
     * TODO: Combine this chain functions across the repeated functionality with groups, based on the BaseEntity class.
     *
     * @param array $courseIdArray
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return array
     */
    public function getConvertedCourseObjects($courseIdArray, $organizationId, $isInternalIds = true)
    {
        $convertedCourseIds = $this->convertAllCourseIds($courseIdArray, $organizationId, $isInternalIds);
        $validationErrors = $this->createErrorsFromConvertedCourseIds($convertedCourseIds);
        $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds);
        $convertedCourseIds = array_values($convertedCourseIds);
        return $convertedCourseIds;
    }


    /**
     * Get course object using course id
     *
     * TODO: Combine this chain functions across the repeated functionality with groups, based on the BaseEntity class.
     *
     * @param string|int $courseId
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return array
     */
    public function getConvertedCourseObject($courseId, $organizationId, $isInternalIds = true)
    {
        $convertedCourseObject = $this->convertCourseObject($courseId, $organizationId, $isInternalIds);
        if (empty($convertedCourseObject)) {
            $validationErrors = $this->createErrorsFromConvertedCourseIds([$courseId => $convertedCourseObject]);
            $this->apiValidationService->addErrorsToOrganizationAPIErrorCount($organizationId, $validationErrors, $isInternalIds);
        }
        return $convertedCourseObject;
    }


    /**
     * Convert internal or external group IDs to the opposite, based on the flag ($isInternalIds)
     *
     * @param array $courseIdArray
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return array
     */
    public function convertAllCourseIds($courseIdArray, $organizationId, $isInternalIds)
    {
        $internalCourseIds = [];
        foreach ($courseIdArray as $courseId) {
            $courseObject = $this->convertCourseObject($courseId, $organizationId, $isInternalIds);
            if (empty($courseObject)) {
                $internalCourseIds[$courseId] = $courseObject;
            } else {
                $internalCourseIds[$courseId] = $courseObject->getId();
            }
        }
        return $internalCourseIds;
    }


    /**
     * Convert course objects
     *
     * @param string|int $courseId
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return null|OrgCourses
     */
    public function convertCourseObject($courseId, $organizationId, $isInternalIds)
    {
        if ($isInternalIds) {
            $returnCourseObject = $this->convertCourseIdToExternalId($courseId, $organizationId);
        } else {
            $returnCourseObject = $this->convertExternalIdToCourseId($courseId, $organizationId);
        }
        return $returnCourseObject;
    }


    /**
     * Convert course internal id to external id
     *
     * @param int $courseId
     * @param int $organizationId
     * @return null|OrgCourses
     */
    private function convertCourseIdToExternalId($courseId, $organizationId)
    {
        $courseObject = $this->organizationCourseRepository->find($courseId);

        if ($courseObject && $courseObject->getOrganization()->getId() == $organizationId) {
            return $courseObject;
        }
        return null;
    }


    /**
     * Convert course section id to internal id
     *
     * @param string $courseId
     * @param int $organizationId
     * @return null|OrgCourses
     */
    private function convertExternalIdToCourseId($courseId, $organizationId)
    {
        $courseObject = $this->organizationCourseRepository->findOneBy([
            'courseSectionId' => $courseId,
            'organization' => $organizationId
        ]);

        if ($courseObject && $courseObject->getOrganization()->getId() == $organizationId) {
            return $courseObject;
        }
        return null;
    }


    /**
     * Create errors from converted course ids
     *
     * @param $convertedCourseIds
     * @return array
     */
    public function createErrorsFromConvertedCourseIds($convertedCourseIds)
    {
        $validationErrors = [];
        foreach ($convertedCourseIds as $convertedIdKey => $convertedIdValue) {
            if (!$convertedIdValue) {
                $validationErrors[] = [$convertedIdKey => "Course ID {$convertedIdKey} is not valid at the organization."];
            }
        }
        return $validationErrors;
    }

    /**
     * Convert to person objects
     *
     * @param string|int $personId
     * @param int $organizationId
     * @param bool $isInternalIds
     * @return null|Person
     */
    public function convertPersonObject($personId, $organizationId, $isInternalIds)
    {
        if ($isInternalIds) {
            $personObject = $this->convertPersonIdToExternalId($personId, $organizationId);
        } else {
            $personObject = $this->convertExternalIdToPersonId($personId, $organizationId);
        }
        return $personObject;
    }

    /**
     * Convert person internal id to external id
     *
     * @param int $personId
     * @param int $organizationId
     * @return null|Person
     */
    private function convertPersonIdToExternalId($personId, $organizationId)
    {
        $personObject = $this->personRepository->find($personId);

        if ($personObject && $personObject->getOrganization()->getId() == $organizationId) {
            return $personObject;
        }
        return null;
    }


    /**
     * Convert person external id to internal id
     *
     * @param string $personId
     * @param int $organizationId
     * @return null|Person
     */
    private function convertExternalIdToPersonId($personId, $organizationId)
    {
        $personObject = $this->personRepository->findOneBy([
            'externalId' => $personId,
            'organization' => $organizationId
        ]);

        if ($personObject && $personObject->getOrganization()->getId() == $organizationId) {
            return $personObject;
        }
        return null;
    }
}