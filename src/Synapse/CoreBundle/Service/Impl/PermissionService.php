<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\RestBundle\Entity\FeaturePermissionResponseDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\SearchBundle\DAO\QuickSearchDAO;

/**
 * @DI\Service("permission_service")
 */
class PermissionService extends AbstractService
{

    const SERVICE_KEY = 'permission_service';

    const FIELD_FEATUREID = 'feature_id';


    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var Manager
     */
    private $rbacManager;

    // Services
    /**
     * @var OrganizationService
     */
    private $organizationService;


    // DAO
    /**
     * @var QuickSearchDAO
     */
    private $quickSearchDao;


    /**
     * PermissionService constructor.
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $this->container->get('tinyrbac.manager');

        // Services
        $this->personService = $this->container->get('person_service');
        $this->organizationService = $this->container->get('org_service');
        $this->academicYearService = $this->container->get('academicyear_service');

        // DAO
        $this->quickSearchDao = $this->container->get('quick_search_dao');
    }
    /**
     * Using search text, looks up 100 students, applying permissions where appropriate
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param boolean $requiresAppointmentPermission
     * @param boolean $isCoordinator
     * @param string $searchText
     * @return array
     */
    public function searchForStudents($organizationId, $facultyId, $requiresAppointmentPermission, $isCoordinator, $searchText)
    {
        $searchText = trim($searchText);

        // get the current academicYear

        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        } else {
            $orgAcademicYearId = null; //  Academic year id would be null if there is no current academic year id
        }

        if ($isCoordinator) {
            $students = $this->quickSearchDao->searchFor100StudentsAsCoordinator($organizationId, $searchText, $orgAcademicYearId);
        } else {
            if ($requiresAppointmentPermission) {
                $students = $this->quickSearchDao->searchFor100StudentsAsFaculty($organizationId, $facultyId, $searchText, $orgAcademicYearId, 'appointment');
            } else {
                $students = $this->quickSearchDao->searchFor100StudentsAsFaculty($organizationId, $facultyId, $searchText, $orgAcademicYearId);
            }
        }

        $studentsToReturn = [];

        foreach ($students as $student) {
            $studentToReturn = [];
            $studentToReturn['user_id'] = (int)$student['person_id'];
            $studentToReturn['user_firstname'] = $student['firstname'];
            $studentToReturn['user_lastname'] = $student['lastname'];
            $studentToReturn['student_id'] = $student['external_id'];
            $studentToReturn['user_email'] = $student['primary_email'];
            $studentToReturn['student_status'] = $student['status'];

            $studentsToReturn[] = $studentToReturn;
        }

        $responseArray = [];
        $responseArray['organization_id'] = $organizationId;
        $responseArray['users'] = $studentsToReturn;

        return $responseArray;
    }

    private function getHighestpermission($oldvalue, $newvalue)
    {
        $oldvalue = (bool) $oldvalue;
        $newvalue = (bool) $newvalue;
        
        $returnValue = false;
        
        if ($oldvalue) {
            $returnValue = true;
        } else {
            $returnValue = $newvalue;
        }
        
        return $returnValue;
    }
}