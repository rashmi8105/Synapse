<?php
namespace Synapse\StudentBulkActionsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\DTO\StudentDto;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\FeatureService;
use Synapse\RestBundle\DTO\BulkActionsDto;
use Synapse\StudentBulkActionsBundle\EntityDto\BulkActionsPermissionDto;

/**
 * @DI\Service("student_bulk_actions_service")
 */
class StudentBulkActionsService extends AbstractService
{

    const SERVICE_KEY = "student_bulk_actions_service";

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    //Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var FeatureService
     */
    private $featureService;

    //Repositories

    /**
     * @var OrgFeaturesRepository
     */
    private $orgFeaturesRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;

    /**
     * StudentBulkActionsService constructor.
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
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);


        //Repositories
        $this->orgFeaturesRepository = $this->repositoryResolver->getRepository(OrgFeaturesRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);

    }


    /**
     * Get students for which the logged in user can take bulk actions.
     *
     * @param BulkActionsDto $bulkActionsDto
     * @param int $organizationId
     * @param int $loggedInUserId
     * @return BulkActionsPermissionDto
     * @throws AccessDeniedException
     */
    public function getBulkActionableStudents($bulkActionsDto, $organizationId, $loggedInUserId)
    {
        //Get the activity type and array of student Ids
        $type = $bulkActionsDto->getType();
        $requestedStudentIds = $bulkActionsDto->getStudentIds();

        //Get the feature_master_lang name of the desired feature
        $featureName = $this->featureService->mapFeatureNames($type);

        //Check to see if the feature is enabled for the organization
        $isFeatureEnabled = $this->orgFeaturesRepository->isFeatureEnabledForOrganization($organizationId, $featureName);

        //If the feature is not enabled for the organization, throw an exception
        if (!$isFeatureEnabled) {
            throw new SynapseValidationException("$featureName is/are not enabled for this organization");
        }

        //Create a new BulkActionsPermissionsDto, and set attributes
        $bulkActionPermissionsDto = new BulkActionsPermissionDto();
        $bulkActionPermissionsDto->setType($type);

        $totalStudentCount = count($requestedStudentIds);
        $bulkActionPermissionsDto->setTotalStudentCount($totalStudentCount);
        // getting the current academic year   for the organization
        $currentOrgAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        //If the current academic year is not active, throw an exception
        if (isset($currentOrgAcademicYear['org_academic_year_id'])) {
            $currentOrgAcademicYearId = $currentOrgAcademicYear['org_academic_year_id'];
        } else {
            throw new AccessDeniedException("Academic year is not active");
        }
        //Get a list of the student ids for which the faculty has public_create permissions for that feature ID
        $featureSpecificStudents = $this->orgPermissionsetFeaturesRepository->getStudentsForFeature($organizationId, $loggedInUserId, $featureName, $requestedStudentIds, $currentOrgAcademicYearId);

        //For each of the students that the logged in user has access to, create a StudentDto object, and set the first and last name
        $accessibleStudents = [];
        foreach ($featureSpecificStudents as $featureSpecificStudent) {

            $bulkActionStudentDto = new StudentDto();
            $bulkActionStudentDto->setStudentId($featureSpecificStudent['student_id']);
            $bulkActionStudentDto->setFirstname($featureSpecificStudent['firstname']);
            $bulkActionStudentDto->setLastname($featureSpecificStudent['lastname']);

            $accessibleStudents[] = $bulkActionStudentDto;
        }

        //Set the total number of accessible students, and the array of students if there is 1 or more accessible students
        $accessibleStudentCount = count($accessibleStudents);
        $bulkActionPermissionsDto->setStudentCountForFeaturePermission($accessibleStudentCount);

        if ($accessibleStudentCount > 0) {
            $bulkActionPermissionsDto->setStudents($accessibleStudents);
        }

        return $bulkActionPermissionsDto;
    }
} 