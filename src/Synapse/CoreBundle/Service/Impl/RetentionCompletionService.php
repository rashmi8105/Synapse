<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;


/**
 * @DI\Service("retention_completion_service")
 */
class RetentionCompletionService extends AbstractService
{

    const SERVICE_KEY = 'retention_completion_service';

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
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;


    /**
     * RetentionCompletionService Construct
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
        $this->container = $container;

        //Repositories
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
    }

    /**
     * Gets retention completion variables for an organization with given student id(s)
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param int|null $yearId
     * @param array|null $studentIds
     * @return array
     */
    public function getOrganizationRetentionCompletionVariables($facultyId, $organizationId, $yearId = null, $studentIds = [])
    {
        $hasPermissionForRetentionCompletion = false;

        if (empty($studentIds)) {
            $hasPermissionForRetentionCompletion = $this->validateRetentionCompletionPermission($facultyId, $organizationId);
            $retentionStudents = [];
        } else {
            $retentionStudents = $this->orgPermissionsetRepository->hasRetentionAccessToStudents($facultyId, $studentIds);
        }

        if ($hasPermissionForRetentionCompletion || !empty($retentionStudents)) {
            $retentionCompletionVariables = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionCompletionVariablesByOrganization($organizationId, $yearId, $retentionStudents);
            $response = [];
            $response['total_students'] = count($retentionCompletionVariables);
            $response['retention_tracking_variables'] = $retentionCompletionVariables;
            return $response;
        } else {
            throw new AccessDeniedException("You do not have access to retention / completion information.");
        }
    }

    /**
     * Gets the list of Retention tracking groups for an organization
     *
     * @param integer $facultyId
     * @param integer $organizationId
     * @return array
     * @throws AccessDeniedException
     */
    public function getOrganizationRetentionTrackingGroups($facultyId, $organizationId)
    {
        $hasPermissionForRetentionCompletion = $this->validateRetentionCompletionPermission($facultyId, $organizationId);
        if ($hasPermissionForRetentionCompletion) {
            $retentionTrackingGroups = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupsForOrganization($organizationId);
            $responseArray = [];
            $responseArray['organization_id'] = $organizationId;
            $responseArray['retention_tracking_groups'] = $retentionTrackingGroups;
            return $responseArray;
        } else {
            throw new AccessDeniedException("Access Denied");
        }
    }


    /**
     * Gets the retention and completion variables based on the retention tracking group
     *
     * @param integer $facultyId
     * @param integer $organizationId
     * @param string $retentionTrackGroup
     * @return array
     * @throws AccessDeniedException
     */
    public function getRetentionTrackGroupVariables($facultyId, $organizationId, $retentionTrackGroup)
    {
        $hasPermissionForRetentionCompletion = $this->validateRetentionCompletionPermission($facultyId, $organizationId);
        if ($hasPermissionForRetentionCompletion) {

            $currentDateTime = new \DateTime('now');
            $currentDateTimeString = $currentDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $currentOrPreviousAcademicYear = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateTimeString, $organizationId);

            if (empty($currentOrPreviousAcademicYear)) {
                throw new SynapseValidationException('No Academic Year has been set for the organization');
            }else{
                $currentOrPreviousYearId = $currentOrPreviousAcademicYear[0]['year_id'];
            }

            $retentionTrackGroupVariables = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionAndCompletionVariables($organizationId, $retentionTrackGroup);
            $retentionTrackGroupVariablesGroupedByYearId = [];

            foreach ($retentionTrackGroupVariables as $retentionTrackGroupVariable) {
                $retentionTrackGroupVariablesGroupedByYearId[$retentionTrackGroupVariable['year_id']]['variables'][] = $retentionTrackGroupVariable['retention_completion_name_text'];
                $retentionTrackGroupVariablesGroupedByYearId[$retentionTrackGroupVariable['year_id']]['year_name'] = $retentionTrackGroupVariable['year_name'];
            }

            $retentionTrackVariablesArray = [];
            foreach ($retentionTrackGroupVariablesGroupedByYearId as $retentionCompletionYearId => $retentionCompletionYearAndVariableNames) {
                $retentionTrackVariableArray = [];
                if($currentOrPreviousYearId >= $retentionCompletionYearId){
                    $retentionTrackVariableArray['year_id'] = $retentionCompletionYearId;
                    $retentionTrackVariableArray['year_name'] = $retentionCompletionYearAndVariableNames['year_name'];
                    $retentionTrackVariableArray['variables'] = $retentionCompletionYearAndVariableNames['variables'];
                    $retentionTrackVariablesArray[] = $retentionTrackVariableArray;
                }
            }

            $responseArray = [];
            $responseArray['organization_id'] = $organizationId;
            $responseArray['retention_tracking_year'] = $retentionTrackGroup;
            $responseArray['retention_track_variables'] = $retentionTrackVariablesArray;
            return $responseArray;
        } else {
            throw new AccessDeniedException("Access Denied");
        }

    }

    /**
     *
     * Method to check if the user has permissions for retentionCompletion enabled
     *
     * @param integer $facultyId
     * @param integer $organizationId
     * @return bool
     *
     */
    public function validateRetentionCompletionPermission($facultyId, $organizationId)
    {
        $permissionSetIds = $this->orgPermissionsetRepository->getAllPermissionsetIdsByPerson($facultyId, $organizationId);
        $permissionSetIdArray = array_column($permissionSetIds, 'org_permissionset_id');
        $hasRetentionCompletionPermission =  $this->orgPermissionsetRepository->hasRetentionAndCompletionAccess($permissionSetIdArray);
        return $hasRetentionCompletionPermission;
    }

}