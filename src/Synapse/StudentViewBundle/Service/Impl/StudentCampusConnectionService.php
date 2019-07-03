<?php
namespace Synapse\StudentViewBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\StudentViewBundle\Service\StudentCampusConnectionServiceInterface;
use Synapse\CampusConnectionBundle\Util\Constants\CampusConnectionErrorConstants;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CampusConnectionBundle\EntityDto\StudentCampusConnectionsDto;
use Synapse\CampusConnectionBundle\EntityDto\CampusConnectionsArrayDto;
use Synapse\StudentViewBundle\Util\Constants\StudentViewConstants;

/**
 * @DI\Service("studentcampusconnection_service")
 */
class StudentCampusConnectionService extends AbstractService implements StudentCampusConnectionServiceInterface
{

    const SERVICE_KEY = 'studentcampusconnection_service';

    /**
     * @var Container
     */
    private $container;

    // Services
    /**
    * @var UserManagementService
    */
    private $userManagementService;

    // Repositories
    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * StudentCampusConnectionService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->container = $container;

        // Services
        $this->userManagementService = $this->container->get('user_management_service');

        // Repositories
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonFaculty");
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudentYear");
    }

    /**
     * Fetching campus connection for student. This gets used only on the student site.
     *
     * @param int $studentId
     * @return array
     * @throws AccessDeniedException
     */
    public function getCampusConnectionsForStudent($studentId)
    {
        $this->logger->debug("Student Campus Connections List with student id - " . $studentId);
        $this->logger->debug("validating the student id" . $studentId);
        $student = $this->orgPersonStudentRepository->findOneBy(['person' => $studentId]);
        $this->isObjectExist($student, CampusConnectionErrorConstants::CAMPUS_CON_ERR_102, CampusConnectionErrorConstants::CAMPUS_CON_ERR_102, CampusConnectionErrorConstants::CAMPUS_CON_ERR_102, $this->logger);

        $orgId = $student->getOrganization()->getId();
        $studentIsActive = $this->userManagementService->isStudentActive($studentId, $orgId);
        if (!$studentIsActive) {
            throw new AccessDeniedException('Inactive/Non-Participant Student Cannot View Campus Connections');
        }
        $currentDate = new \DateTime('now');
        $date = $currentDate->format('Y-m-d');
        $campusConnections = $this->orgPersonFacultyRepository->getAllCampusConnectioDetailsForStudent($studentId, $date);
        $facultyCampusConn = $this->getFacultyDetailsArray($campusConnections);
        $facultyConnByOrg = $this->getFacultyConnectionsByOrg($facultyCampusConn);
        $studentCampusConnections = array();
        foreach ($facultyConnByOrg as $orgFaculty) {
            $campusConn = $this->getPrimaryConnectionFirstArray($orgFaculty);
            $studentConn = new StudentCampusConnectionsDto();
            $studentConn->setOrganizationId($orgFaculty[0][StudentViewConstants::ORG_ID]);
            $campusId = (!empty($orgFaculty[0][StudentViewConstants::CAMPUS_ID])) ? $orgFaculty[0][StudentViewConstants::CAMPUS_ID] : '';
            $studentConn->setCampusId($campusId);
            $studentConn->setCampusName($orgFaculty[0]['org_name']);
            $facultyDetails = $this->getFacultyDetailsResponse($campusConn);
            $studentConn->setCampusConnections($facultyDetails);
            $studentCampusConnections['campus_connection_list'][] = $studentConn;
        }
        $this->logger->info("Student Campus Connections List is Completed");
        return $studentCampusConnections;
    }


    private function isObjectExist($object, $message, $key, $errorConst = '', $logger)
    {
        if (! ($object)) {
            $logger->error("Student View Campus connection - Is object exist - " . $errorConst . $message . " " . $key);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function getFacultyDetailsArray($campusConnections)
    {
        $faculty = array();
        foreach ($campusConnections as $conn) {
            $faculty[$conn[StudentViewConstants::PERSON_ID]]['id'] = $conn[StudentViewConstants::PERSON_ID];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::FNAME] = $conn[StudentViewConstants::FNAME];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::LNAME] = $conn[StudentViewConstants::LNAME];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::TITLE] = $conn[StudentViewConstants::TITLE];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::EMAIL] = $conn[StudentViewConstants::EMAIL];
            $faculty[$conn[StudentViewConstants::PERSON_ID]]['externalId'] = $conn['externalId'];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::PHONE] = $conn[StudentViewConstants::PHONE];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::MOBILE_NO] = $conn[StudentViewConstants::MOBILE_NO];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::ORG_ID] = $conn['organization_id'];
            $faculty[$conn[StudentViewConstants::PERSON_ID]]['org_name'] = $conn['organization_name'];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::CAMPUS_ID] = $conn[StudentViewConstants::CAMPUS_ID];
            $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::IS_PRIMARY] = $conn['primary_conn'];
            if($conn[StudentViewConstants::IS_INVISIBLE]){
                $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::IS_INVISIBLE] = $conn[StudentViewConstants::IS_INVISIBLE];
            }else{
                $faculty[$conn[StudentViewConstants::PERSON_ID]][StudentViewConstants::IS_INVISIBLE] = false;
            }
            $associatedWith = array();
            $associatedWith['flag'] = $conn['flag'];
            $associatedWith[StudentViewConstants::ORIGIN_ID] = $conn[StudentViewConstants::ORIGIN_ID];
            $associatedWith[StudentViewConstants::ORIGIN_NAME] = $conn[StudentViewConstants::ORIGIN_NAME];
            $faculty[$conn[StudentViewConstants::PERSON_ID]]['details'][] = $associatedWith;
        }
        return $faculty;
    }

    private function getFacultyConnectionsByOrg($facultyCampusConn)
    {
        $facultyArr = array();
        foreach ($facultyCampusConn as $faculty) {
            if (is_numeric($faculty[StudentViewConstants::ORG_ID])) {
                $facultyArr[$faculty[StudentViewConstants::ORG_ID]][] = $faculty;
            }
        }
        return $facultyArr;
    }

    private function getPrimaryConnectionFirstArray($facultyCampusConn)
    {
        $primaryConn = array();
        $otherConn = array();
        foreach ($facultyCampusConn as $faculty) {
            if ($faculty['id'] == $faculty[StudentViewConstants::IS_PRIMARY]) {
                $primaryConn[$faculty['id']] = $faculty;
            } else {
                $otherConn[$faculty['id']] = $faculty;
            }
        }
        $campusConn = array_merge($primaryConn, $otherConn);
        return $campusConn;
    }

    private function getFacultyDetailsResponse($campusConn)
    {
        $connectionsArr = array();
        foreach ($campusConn as $facultyDetail) {
            $campusConn = new CampusConnectionsArrayDto();
            $campusConn->setPersonId($facultyDetail['id']);
            $campusConn->setPersonFirstname($facultyDetail['fname']);
            $campusConn->setPersonLastname($facultyDetail['lname']);
            $title = (! empty($facultyDetail['title'])) ? $facultyDetail['title'] : '';
            $campusConn->setPersonTitle($title);
            $phone = (! empty($facultyDetail['mobile_no'])) ? $facultyDetail['mobile_no'] : $facultyDetail['phone'];
            $campusConn->setPhone($phone);
            $email = (! empty($facultyDetail['email'])) ? $facultyDetail['email'] : '';
            $campusConn->setEmail($email);
            $isPrimary = ($facultyDetail['id'] == $facultyDetail['is_primary']) ? true : false;
            $campusConn->setPrimaryConnection($isPrimary);
            $isInvisible = ($facultyDetail['is_invisible']) ? true : false;
            $campusConn->setIsInvisible($isInvisible);
            $groups = array();
            $courses = array();
            foreach ($facultyDetail['details'] as $associatedWith) {
                if ($associatedWith['flag'] == 'group') {
                    $group = array();
                    $group['group_id'] = $associatedWith['origin_id'];
                    $group['group_name'] = $associatedWith['origin_name'];
                    $groups[] = $group;
                } else {
                    $course = array();
                    $course['course_id'] = $associatedWith['origin_id'];
                    $course['course_name'] = $associatedWith['origin_name'];
                    $courses[] = $course;
                }
            }
            $campusConn->setGroups($groups);
            $campusConn->setCourses($courses);
            $connectionsArr[] = $campusConn;
        }
        return $connectionsArr;
    }
}