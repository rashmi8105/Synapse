<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Service\Impl\FacultyService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\FacultyPolicyDto;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\RestBundle\Entity\Response;

/**
 * Class FacultyController
 *
 * @package Synapse\RestBundle\Controller
 *      @Rest\Prefix("faculty")
 *
 */
class FacultyController extends AbstractAuthController
{

    const SECURITY_CONTEXT = 'security.context';


    /**
     * @var FacultyService
     *
     *      @DI\Inject(FacultyService::SERVICE_KEY)
     */
    private $facultyService;

    /**
     * @var PersonService
     *
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * @param int $loggedInOrgId
     * @param int $facultyId
     */
    protected function ensureAccessToFaculty($loggedInOrgId, $facultyId)
    {
        $facultyPerson = $this->personService->findPerson($facultyId);
        $facultyOrgId = $facultyPerson->getOrganization()->getId();

        if(!$loggedInOrgId || $loggedInOrgId !== $facultyOrgId){
           throw new AccessDeniedException("Logged in user doesn't have access to this faculty member: $loggedInOrgId !== $facultyOrgId");
        }
    }

    /**
     * Get Api to allow a coordinator to view a faculty's information.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Faculty Information",
     * section = "Faculty",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{facultyId}", defaults={"facultyId" = -1},requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $facultyId
     * @return Response
     */
    public function getPersonalInfoAction($facultyId)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $this->ensureAccessToFaculty($organizationId, $facultyId);

        $faculty = $this->personService->findPerson($facultyId);
        return new Response($faculty);
    }
    
    /**
     * Allows a Coordinator to soft delete a faculty member.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Faculty Member",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{personId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @return Response
     */
    public function deleteAction($personId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->softDeleteById($personId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't delete faculty member ($personId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Gets a list of all faculty groups.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Faculty Group List",
     * section = "Faculty",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{personId}/groups/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @param int $orgId
     * @return Response
     */
    public function getGroupsListAction($personId, $orgId)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->facultyService->getGroupsList($organizationId, $personId);
        return new Response($result);
    }

    /**
     * Allows a Coordinator to add a faculty to a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Group",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member was added to group. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/{personId}/group/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param int $personId
     * @param int $groupId
     * @return Response
     */
    public function addGroupAction($personId, $groupId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->addGroup($personId, $groupId);

        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't add group ($groupId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to remove a faculty member from a group.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Faculty Member from Group",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member was removed from group. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{personId}/group/{groupId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param int $groupId
     * @return Response
     */
    public function removeGroupAction($personId, $groupId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->removeGroup($personId, $groupId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't remove group ($groupId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }


        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Adds a Faculty member to multiple groups.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Groups",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member added to groups. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/addGroups", requirements={"_format"="json"})
     * @RequestParam(name="grouplist", default ="", strict=false, description="Group List JSON")
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function addGroupsAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $groupList = $paramFetcher->get('grouplist');
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $status =  $this->facultyService->manageGroupMembership($groupList,$personId);
         return new Response([
            'success' => $status
        ], []);
        
    }

    /**
     * Removes a faculty member from multiple groups.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Faculty From Groups",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member removed from groups. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/removeGroups", requirements={"_format"="json"})
     * @RequestParam(name="grouplist", default ="", strict=false, description="Group Ids")
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function removeGroupsAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $groupIds = $paramFetcher->get('grouplist');
        $status = null;
        $errors = [];
        foreach ($groupIds as $groupId) {
            try {
                $this->facultyService->removeGroup($personId, $groupId);
            } catch (\RuntimeException $e) {
                $errors[] = "Couldn't remove person-$personId from group-$groupId during a bulk operation.\n";
            }
        }

        if ($errors) {
            return new Response([
                'errors' => $errors
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to add a course for a faculty member.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Course to Faculty",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty's course was added. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/{personId}/course/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param int $courseId
     * @return Response
     */
    public function addCourseAction($personId, $courseId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->addCourse($personId, $courseId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't add course ($$courseId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }
        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to remove a course from a faculty member.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Course from Faculty",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty's course was removed. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{personId}/course/{courseId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param int $courseId
     * @return Response
     */
    public function removeCourseAction($personId, $courseId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->removeCourse($personId, $courseId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't remove course ($$courseId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }
        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Add a faculty member to multiple courses.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Courses",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty was added to courses. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/addCourses", requirements={"_format"="json"})
     * @RequestParam(name="courselist", default ="", strict=false, description="Course Ids")
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function addCoursesAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $courseIds = $paramFetcher->get('courselist');
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $status = $this->facultyService->manageFacultyCourseMembership($courseIds,$personId);
        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Removes multiple courses from a faculty member.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Courses from Faculty",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty was removed from courses. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/removeCourses", requirements={"_format"="json"})
     * @RequestParam(name="courselist", default ="", strict=false, description="Course Ids")
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function removeCoursesAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $courseIds = $paramFetcher->get('courselist');
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $status = null;
        $errors = '';
        foreach ($courseIds as $courseId) {
            try {
                $this->facultyService->removeCourse($personId, $courseId);
            } catch (\RuntimeException $e) {
                $errors .= "Couldn't remove person-$personId from course-$courseId during a bulk operation.\n";
            }
        }

        if ($errors) {
            throw new \RuntimeException($errors);
        }

        return new Response([
            'success' => $status
        ], []);
    }
    
    /**
     * Allows a Coordinator to add a faculty member to a team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Team",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty added to team. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("/{personId}/team/{teamId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param int $teamId
     * @return Response
     */
    public function addTeamAction($personId, $teamId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->addTeam($personId, $teamId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't add team ($teamId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Allows a Coordinator to remove a faculty member from a team.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove Faculty from Team",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty removed from team. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{personId}/team/{teamId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param int $teamId
     * @return Response
     */
    public function removeTeamAction($personId, $teamId)
    {
        try {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();
            $this->ensureCoordinatorOrgAccess($loggedInUserId);
            $this->ensureAccessToFaculty($organizationId, $personId);
            $status = $this->facultyService->removeTeam($personId, $teamId);
        } catch (\Exception $e) {
            return new Response([
                'errors' => [ "Couldn't remove team ($teamId) for faculty member ($personId). ", $e->getMessage() ]
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Adds multiple teams to a faculty member.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Teams",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Teams added to faculty member. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/addTeams", requirements={"_format"="json"})
     * @RequestParam(name="teamlist", default ="", strict=false, description="Team List JSON")
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function addTeamsAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
		$teamList = $paramFetcher->get('teamlist');
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        $status = $this->facultyService->manageFacultyTeamMembership($personId,$teamList);
        return new Response([
            'success' => $status
        ], []);
    }

    /**
     * Removes a faculty member from multiple teams.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Teams",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Faculty member removed from teams. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{personId}/removeTeams", requirements={"_format"="json"})
     * @RequestParam(name="teamlist", default ="", strict=false, description="Team Ids")
     * @Rest\View(statusCode=204)
     *
     * @param int $personId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function removeTeamsAction($personId, ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $this->ensureCoordinatorOrgAccess($loggedInUserId);
        
        $teamIds = $paramFetcher->get('teamlist');
        $status = null;
        $errors = [];
        foreach ($teamIds as $teamId) {
            try {
                $this->facultyService->removeTeam($personId, $teamId);
            } catch (\RuntimeException $e) {
                $errors[] = "Couldn't remove person-$personId from team-$teamId during a bulk operation.\n";
            }
        }
        
        if ($errors) {
            return new Response([
                'errors' => $errors
            ]);
        }

        return new Response([
            'success' => $status
        ], []);
    }
    
    /**
     * Gets a list of courses that a faculty member is a part of.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets Faculty Group List",
     * section = "Faculty",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{personId}/courses", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $personId
     * @return Response
     */
    public function listFacultyCourseAction($personId)
    {
        $organization = $this->getLoggedInUserOrganization();
        $response = $this->facultyService->listFacultyCourses($personId, $organization);
        return new Response(["courselist"=>$response], []);
    }

    /**
     * Updates Faculty privacy policy accept data
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add Faculty to Teams",
     * input = "Synapse\RestBundle\Entity\FacultyPolicyDto",
     * output = "Synapse\RestBundle\Entity\FacultyPolicyDto",
     * section = "Faculty",
     * statusCodes = {
     *                  204 = "Privacy policy accept data updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/policy", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param FacultyPolicyDto $facultyPolicyDto
     * @return Response
     */
    public function updateFacultyPolicyAction(FacultyPolicyDto $facultyPolicyDto)
    {
        $result = $this->facultyService->updatePolicy($facultyPolicyDto);
        return $result;
    }
}
