<?php

namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

/**
 * @DI\Service("report_drilldown_service")
 */
class ReportDrilldownService extends AbstractService
{

    const SERVICE_KEY = 'report_drilldown_service';


    // Scaffolding

    /**
     * @var Container
     */
    private $container;


    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;


    // Repositories

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;


    /**
     * @DI\InjectParams({
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
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);

        // Repositories
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
    }


    /**
     * Given an array of student ids, returns the ones which should be included in the drilldown:
     * they are individually accessible to the given user and are participants in the current academic year.
     *
     * Throws an AccessDeniedException if none of the students fit these criteria
     * or if the number of students not included is below a threshold that might allow the user to infer values for students which should be inaccessible.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param array $studentIds
     * @param string $userMessage
     * @return array
     * @throws AccessDeniedException
     */
    public function getIndividuallyAccessibleParticipants($loggedInUserId, $organizationId, $studentIds, $userMessage = 'You do not have individual access to the students')
    {
        // Determine whether the user has individual or aggregate permission to each of the students who have a value for this item.
        $accessLevels = $this->orgPermissionsetRepository->getAccessLevelForFacultyAndStudents($loggedInUserId, $studentIds);

        $individuallyAccessibleStudents = array_keys($accessLevels, 1);

        // Determine which of the individually accessible students are participants for the current academic year.
        $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);

        $individuallyAccessibleParticipants = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($individuallyAccessibleStudents, $organizationId, $currentOrgAcademicYearId);

        $individualParticipantCount = count($individuallyAccessibleParticipants);

        // Throw an exception if there are no students which should be included in the drilldown
        if ($individualParticipantCount == 0) {
            throw new AccessDeniedException('You do not have individual access to the students', $userMessage);
        }

        return $individuallyAccessibleParticipants;
    }

}