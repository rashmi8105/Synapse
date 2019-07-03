<?php

namespace Synapse\SurveyBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\ISQPermissionsetService;


/**
 * @DI\Service("survey_permission_service")
 */
class SurveyPermissionService extends AbstractService
{

    const SERVICE_KEY = 'survey_permission_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    // Services
    /**
     * @var ISQPermissionsetService
     */
    private $isqPermissionsetService;

    // Repositories
    /**
     * @var DatablockMasterLangRepository
     */
    private $datablockMasterLangRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPermissionsetDatablockRepository
     */
    private $orgPermissionsetDatablockRepository;


    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->isqPermissionsetService = $this->container->get(ISQPermissionsetService::SERVICE_KEY);

        // Repositories
        $this->datablockMasterLangRepository = $this->repositoryResolver->getRepository(DatablockMasterLangRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetDatablockRepository::REPOSITORY_KEY);
    }


    /**
     * Returns high-level data about the given faculty member's survey-related permissions,
     * including whether he/she can access any survey blocks, the free-response survey block in particular, and any ISQs.
     *
     * @param int $facultyId -- the faculty member's person_id
     * @param int $organizationId
     * @return array
     */
    public function getSurveyPermissionsForFaculty($facultyId, $organizationId)
    {
        $dataToReturn = [];

        // Get survey block permissions.
        $permissionSets = $this->orgPermissionsetRepository->getAllPermissionsetIdsByPerson($facultyId, $organizationId);
        $permissionSets = array_column($permissionSets, 'org_permissionset_id');

        $surveyBlocks = $this->orgPermissionsetDatablockRepository->getAllSurveyblockIdByPermissions($permissionSets);
        $surveyBlocks = array_column($surveyBlocks, 'block_id');

        if (empty($surveyBlocks)) {
            $dataToReturn['has_survey_block_access'] = false;
            $dataToReturn['has_free_response_access'] = false;
        } else {
            $dataToReturn['has_survey_block_access'] = true;
            $openEndedSurveyBlockId = $this->datablockMasterLangRepository->findOneBy(['datablockDesc' => 'Open Ended'])->getDatablock()->getId();
            if (in_array($openEndedSurveyBlockId, $surveyBlocks)) {
                $dataToReturn['has_free_response_access'] = true;
            } else {
                $dataToReturn['has_free_response_access'] = false;
            }
        }

        // Get ISQ permissions.
        $allowedISQs = $this->isqPermissionsetService->getFilteredISQIds($facultyId, $organizationId);

        if (empty($allowedISQs)) {
            $dataToReturn['has_isq_access'] = false;
        } else {
            $dataToReturn['has_isq_access'] = true;
        }

        return $dataToReturn;
    }


    /**
     * Returns high-level data about the given faculty member's survey-related permissions with respect to the given student,
     * including whether he/she can access any survey blocks, the free-response survey block in particular, and any ISQs.
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $organizationId
     * @return array $surveyPermissionDetails
     * @throws AccessDeniedException
     */
    public function getSurveyPermissionsForFacultyAndStudent($facultyId, $studentId, $organizationId)
    {
        //check for non-participant student permissions
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);
        $surveyPermissionDetails = [];

        // Get survey block permissions.
        $surveyBlocks = $this->getSurveyBlocksByFacultyStudent($facultyId, $studentId);

        if (empty($surveyBlocks)) {
            $surveyPermissionDetails['has_survey_block_access'] = false;
            $surveyPermissionDetails['has_free_response_access'] = false;
        } else {
            $surveyPermissionDetails['has_survey_block_access'] = true;
            $openEndedSurveyBlockId = $this->datablockMasterLangRepository->findOneBy(['datablockDesc' => 'Open Ended'])->getDatablock()->getId();
            if (in_array($openEndedSurveyBlockId, $surveyBlocks)) {
                $surveyPermissionDetails['has_free_response_access'] = true;
            } else {
                $surveyPermissionDetails['has_free_response_access'] = false;
            }
        }

        // Get ISQ permissions.
        $permissionSets = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($facultyId, $studentId);
        $permissionSets = array_column($permissionSets, 'org_permissionset_id');

        $allowedISQs = $this->isqPermissionsetService->getFilteredISQIds($facultyId, $organizationId, $permissionSets);

        if (empty($allowedISQs)) {
            $surveyPermissionDetails['has_isq_access'] = false;
        } else {
            $surveyPermissionDetails['has_isq_access'] = true;
        }

        return $surveyPermissionDetails;
    }


    /**
     * Returns an array of the survey blocks the given faculty member should be allowed to see for the given student.
     *
     * @param int $facultyId
     * @param int $studentId
     * @return array
     */
    public function getSurveyBlocksByFacultyStudent($facultyId, $studentId)
    {
        $permissionSets = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($facultyId, $studentId);
        $permissionSets = array_column($permissionSets, 'org_permissionset_id');
        $surveyBlocks = $this->orgPermissionsetDatablockRepository->getAllSurveyblockIdByPermissions($permissionSets);
        $surveyBlocks = array_column($surveyBlocks, 'block_id');
        return $surveyBlocks;
    }

}