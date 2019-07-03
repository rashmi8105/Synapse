<?php
namespace Synapse\StudentBulkActionsBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\DTO\BulkActionsDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\StudentBulkActionsBundle\Service\Impl\StudentBulkActionsService;

/**
 * Class StudentBulkActionsController
 *
 * @package Synapse\StudentBulkActionsBundle\Controller
 *         
 *          @Rest\Prefix("/bulkactions")
 */
class StudentBulkActionsController extends AbstractAuthController
{

    /**
     * @var StudentBulkActionsService
     *     
     *      @DI\Inject(StudentBulkActionsService::SERVICE_KEY)
     */
    private $studentBulkActionsService;

    /**
     * Get permissions based on activity type for passed in student list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Permissions",
     * input = "Synapse\StudentBulkActionsBundle\EntityDto\BulkActionsDto",
     * output = "Synapse\StudentBulkActionsBundle\EntityDto\BulkActionsPermissionDto",
     * section = "Student Bulk Actions",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/permissions", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param BulkActionsDto $bulkActionsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function getStudentPermissionsAction(BulkActionsDto $bulkActionsDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($bulkActionsDto, [$validationErrors[0]->getMessage()]), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $organizationId = $this->getLoggedInUserOrganizationId();

            $studentPermissions = $this->studentBulkActionsService->getBulkActionableStudents($bulkActionsDto, $organizationId, $loggedInUserId);
            return new Response($studentPermissions);
        }
    }
}
