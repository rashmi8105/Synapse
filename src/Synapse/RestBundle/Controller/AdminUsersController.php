<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\AdminUsersService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\UserDTO;

/**
 * Class UsersController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/adminusers")
 */
class AdminUsersController extends AbstractAuthController
{

	const SECURITY_CONTEXT = 'security.context';
    
    /**
     * @var AdminUsersService admin user service
     *     
     *      @DI\Inject(AdminUsersService::SERVICE_KEY)
     */
    private $adminUserService;


	/**
     * Creates an admin user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Admin User",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * output = "Synapse\RestBundle\Entity\UserDTO",
     * section = "Admin User",
     * statusCodes = {
     *                  201 = "Admin user created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function createAdminUserAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $usersResponse = $this->adminUserService->createAdminUser($userDTO,$loggedInUserId);
            return new Response($usersResponse);
        }
    }
    
    /**
     * Edits an admin user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit an Admin User",
     * input = "Synapse\RestBundle\Entity\UserDTO",
     * output = "Synapse\RestBundle\Entity\UserDTO",
     * section = "Admin User",
     * statusCodes = {
     *                  201 = "Admin user was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("/{userId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param UserDTO $userDTO
     * @param integer $userId
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function editAdminUserAction(UserDTO $userDTO, $userId, ConstraintViolationListInterface $validationErrors)
    {
      $this->ensureAdminAccess();
      if (count($validationErrors) > 0) {
            return View::create(new Response($userDTO, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
          $usersResponse = $this->adminUserService->editAdminUser($userDTO,$userId);
          return new Response($usersResponse);
    	}
    }

    /**
     * Get all Admin Users.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Gets all Admin Users",
     * section = "Admin User",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAllAdminUsersAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAdminAccess();
    	$pageNo = $paramFetcher->get('page_no');
    	$offset = $paramFetcher->get('offset');
        $usersResponse = $this->adminUserService->getAllAdminUsers($pageNo, $offset);
    	return new Response($usersResponse);
    }
    
    /**
     * Deletes an Admin User.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Admin User",
     * section = "Admin User",
     * statusCodes = {
     *                  204 = "Admin user was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{userId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $userId
     * @return Response
     */
    public function deleteAdminUserAction($userId)
    {
        $this->ensureAdminAccess();
        $loggedInUserId = $this->getLoggedInUserId();
        $deleteUser = $this->adminUserService->deleteAdminUser($userId,$loggedInUserId);
    	return new Response($deleteUser);
    }
    
    /**
     * Send invitation link to the user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Sent Invitation Email",
     * section = "Admin User",
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
     * @Rest\Get("/sendinvite/{userId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $userId
     * @return Response
     */
    public function getSendInvitationEmailAction($userId)
    {
        $this->ensureAdminAccess();
        $sendResponse = $this->adminUserService->getSendInvitationEmail($userId);
        return new Response($sendResponse);
    }
}