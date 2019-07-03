<?php
namespace Synapse\StaticListBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PHPExcel_IOFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager as TinyRbac_Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\CoreBundle\Util\CSVFile;
use Synapse\CoreBundle\Util\UploadHelper;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StaticListBundle\EntityDto\StaticListDto;
use Synapse\StaticListBundle\Service\Impl\StaticListService;
use Synapse\StaticListBundle\Service\Impl\StaticListStudentsService;
use Synapse\UploadBundle\Job\ProcessStaticListUpload;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * StaticList Controller
 *
 * @package Synapse\StaticListBundle\Controller
 *          @Rest\Prefix("/staticlists")
 *
 */
class StaticListController extends AbstractAuthController
{

    /**
     * @var \BCC\ResqueBundle\Resque
     *
     *      @DI\Inject(SynapseConstant::RESQUE_CLASS_KEY)
     */
    private $resque;

    /**
     * @var StaticListService
     *
     *      @DI\Inject(StaticListService::SERVICE_KEY)
     */
    private $staticListService;

    /**
     * @var StaticListStudentsService
     *
     *      @DI\Inject(StaticListStudentsService::SERVICE_KEY)
     */
    private $staticListStudentsService;

    /**
     * @var UploadFileLogService
     *
     *      @DI\Inject(UploadFileLogService::SERVICE_KEY)
     */
    private $uploadFileLogService;

    /**
     * @var UploadHelper
     */
    private $uploadHelper;

    /**
     * Get GroupStudent Upload
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Static List Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{staticlistId}/upload/{id}")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getStaticListUploadAction($staticlistId, $id)
    {
        $this->authorize();
        $upload = $this->uploadFileLogService->findAllUploadLogs($id, 'SL');
        $response = new Response($upload, array());
        return $response;
    }


    /**
     * Gets the list of pending static list uploads.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Pending Static List Uploads",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/pending/{orgId}")
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     * @return Response
     */
    public function getPendingStaticListUploadAction($orgId)
    {
        $upload = $this->uploadFileLogService->hasPendingView($orgId, 'SL');
        return new Response([
            'upload' => $upload
        ], []);
    }

    /**
     * Gets the total count of students within a static list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Static List Students Count",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/count/{staticListId}")
     * @Rest\View(statusCode=200)
     * @QueryParam(name="org_id", strict=true, description="Organization")
     *
     * @param int $staticListId
     * @return Response
     */
    public function getStaticListStudentsCountAction($staticListId)
    {
        $this->authorize();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->staticListStudentsService->getStaticListStudentsCount($staticListId, $organizationId);
        return new Response($result);
    }

    /**
     * Gets items on a static list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Static List Items",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("")
     * @Rest\View(statusCode=200)
     * @QueryParam(name="org_id", strict=true, description="Organization")
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStaticListItemsAction(ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);

        $orgId = $paramFetcher->get(CourseConstant::ORG_ID);
        $pageNo = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
        $sortBy = $paramFetcher->get('sortBy');
        $loggedInUser = $this->getLoggedInUser();
        $result = $this->staticListService->listAllStaticLists($orgId, $loggedInUser, null, $pageNo, $offset, $sortBy);
        return new Response($result);
    }


    /**
     * Gets a static list and its details.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Static List",
     * output = "Synapse\RestBundle\Entity\PersonDTO",
     * section = "Static List",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{staticListId}")
     * @Rest\View(statusCode=200)
     * @QueryParam(name="page_no", strict=false, description="page_no")
     * @QueryParam(name="offset", strict=false, description="offset")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @QueryParam(name="output-format", strict=false, description="output-format: csv")
     *
     * @param int $staticListId
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStaticListAction($staticListId, ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);

        $pageNo = $paramFetcher->get('page_no');
        $offset = $paramFetcher->get('offset');
        $sortBy = $paramFetcher->get('sortBy');
        $outputFormat = trim($paramFetcher->get('output-format'));
        $isCSV =  (strtolower($outputFormat) == 'csv') ? true : false;
        $loggedInUser = $this->getLoggedInUser();

        $result = $this->staticListStudentsService->viewStaticListDetails($staticListId, $loggedInUser, $pageNo, $offset, $sortBy, $isCSV);
        return new Response($result);
    }

    /**
     * Create API to share a static list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Share Static List",
     * output = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{listid}/shares",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="person_id")
     * @RequestParam(name="org_id")
     *
     * @param int $listid
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createShareStaticListAction($listid, ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorizeManager() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $facultyId = $paramFetcher->get('person_id');
        $orgId = $paramFetcher->get(CourseConstant::ORG_ID);
        $loggedInUser = $this->getLoggedInUser();

        $response = $this->staticListService->shareStaticList($orgId, $facultyId, $listid, $loggedInUser);
        $response = new Response($response, []);
        return $response;
    }

    /**
     * Get Student Upload Template
     *
     *
     * @Rest\Get("/upload/template")
     *
     * @codeCoverageIgnore
     */
    public function getStaticListUploadTemplateAction()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="staticlist-upload-template.csv"');
        echo "StudentId";
        echo "\n";
        exit();
    }

    /**
     * Create API to upload static lists.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Static List Upload",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/{id}/upload",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="organization")
     * @RequestParam(name="key")
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createStaticListUploadAction($id, ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $loggedInUserId = $this->getLoggedInUserId();

        $orgId = $paramFetcher->get('organization');
        $key = $paramFetcher->get('key');

        $pathParts = pathinfo($key);

        $this->uploadHelper = new UploadHelper();

        if ($pathParts['extension'] == 'xls' || $pathParts['extension'] == 'xlsx') {

            $inputFile = $this->container->getParameter(UploadConstant::KERNEL) . "/$key";
            $outputFileNoExtension = "data://staticlist_uploads/{$pathParts['filename']}.csv";
            $outputFileWithExtension = "data://staticlist_uploads/$key";

            file_put_contents($inputFile, fopen($outputFileWithExtension, 'r'));
            $this->uploadHelper->convertXLStoCSV($inputFile, $outputFileNoExtension);
            unlink($this->container->getParameter(UploadConstant::KERNEL) . "/$key");
            $key = "{$pathParts['filename']}.csv";
        }

        $file = new CSVFile("data://staticlist_uploads/$key", true, ',', '"', '\\', true);
        $file->seek(PHP_INT_MAX);
        $rowsTotal = $file->key();
        $file->seek(0);
        foreach ($file as $idx => $row) {
            $columns = array_keys($row);
            break;
        }

        $jobNumber = uniqid();

        $uploadType = 'SL';

        $uploadFile = $this->uploadFileLogService->createUploadService($orgId, $key, $columns, $rowsTotal, $jobNumber, $loggedInUserId, $uploadType);

        if (!in_array(strtolower(CourseConstant::STUDENTID), $columns) || $rowsTotal == 0) {
            $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
            $response = new Response($uploadFile, []);
            return $response;
        }

        $this->uploadFileLogService->updateJobErrorPath($uploadFile);

        $job = new ProcessStaticListUpload();

        $job->args = array(
            UploadConstant::ORGID => $orgId,
            'key' => $key,
            'userId' => $loggedInUserId,
            'staticListId' => $id,
            'jobNumber' => $jobNumber,
            'uploadId' => $uploadFile->getId()
        );

        $this->resque->enqueue($job);

        $response = new Response($uploadFile, []);
        return $response;
    }

    /**
     * Creates a new static list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Static List Upload",
     * output = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="staticlist_name")
     * @RequestParam(name="staticlist_description")
     * @RequestParam(name="org_id")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createStaticListAction(ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $staticlist_name = $paramFetcher->get('staticlist_name');
        $staticlist_desc = $paramFetcher->get('staticlist_description');
        $orgId = $paramFetcher->get(CourseConstant::ORG_ID);

        $request = Request::createFromGlobals();
        $switchUser = $request->headers->get('switch-user');
        if ($switchUser != null) {
            $loggedInUser = $this->container->get('proxy_user');
        } else {
        $loggedInUser = $this->getLoggedInUser();
        }

        $response = $this->staticListService->createStaticList($orgId, $loggedInUser, $staticlist_name, $staticlist_desc);
        $response = new Response($response, []);
        return $response;
    }

    /**
     * Updates a Static List by adding or removing students.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Add/Remove Students In Static List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{staticListId}/students",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="student_ids_to_add",strict=false,nullable=true)
     * @RequestParam(name="student_ids_to_remove",strict=false,nullable=true)
     *
     * @param int $staticListId
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @throws ValidationException
     */
    public function addOrRemoveStudentsInStaticListAction($staticListId, ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([]);
        $studentIdsToAdd = $paramFetcher->get('student_ids_to_add') ? $paramFetcher->get('student_ids_to_add'): [];
        $studentIdsToRemove = $paramFetcher->get('student_ids_to_remove') ? $paramFetcher->get('student_ids_to_remove'): [];
        $loggedInUser = $this->getLoggedInUser();
        $organizationId =  $this->getLoggedInUserOrganizationId();

        $studentIdsInAddAndRemoveList = array_intersect($studentIdsToAdd, $studentIdsToRemove);
        // Check same student ID/IDs present in add and remove static list
        if ( isset($studentIdsInAddAndRemoveList) && !empty($studentIdsInAddAndRemoveList) ) {
            throw new SynapseValidationException("Not able to perform this action, Student Ids: " . implode(",", $studentIdsInAddAndRemoveList) . " present in remove list");
        } else {

            if (!empty($studentIdsToAdd)) {
                if (count($studentIdsToAdd) > 1) {
                    // Add/Share bulk students with a static list
                    $response = $this->staticListStudentsService->createBulkJobToAddStudentsToStaticList($organizationId, $loggedInUser, $staticListId,$studentIdsToAdd);
                } else {
                    // Add/Share single student with a static list
                    $response = $this->staticListStudentsService->addStudentToStaticList($organizationId, $loggedInUser, $staticListId,$studentIdsToAdd[0]);
                }
            }

            if (!empty($studentIdsToRemove)) {
                if (count($studentIdsToRemove) > 1) {
                    // Remove bulk students from a static list
                    $response = $this->staticListStudentsService->createBulkJobToRemoveStudentsFromStaticList($organizationId, $loggedInUser, $staticListId,$studentIdsToRemove);
                } else {
                    // Remove single student from a static list
                    $response = $this->staticListStudentsService->removeStudentFromStaticList($organizationId, $loggedInUser, $staticListId,$studentIdsToRemove[0]);
                }
            }

        }

        return new Response($response);
    }

    /**
     * Updates the students in a static list.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Manage Static List",
     * input = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * output = "Synapse\StaticListBundle\EntityDto\StaticListDetailsDto",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/manage",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param StaticListDto $staticListDto
     * @return Response
     */
    public function createStaticListManageAction(StaticListDto $staticListDto)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $students = $staticListDto->getStudentsDetails();
        $staticListId = $staticListDto->getStaticlistId();
        $organizationId = $staticListDto->getOrganizationId();
        $actionType = $staticListDto->getStudentEditType();
        $loggedInUser = $this->getLoggedInUser();

        $response = $this->staticListStudentsService->updateStudentsInStaticList($organizationId, $loggedInUser, $students, $staticListId, $actionType);
        $response = new Response($response, []);
        return $response;
    }

    /**
     * Edit API to update a static list information.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Manage Static List",
     * output = "Synapse\StaticListBundle\EntityDto\StaticListDto",
     * section = "Static List",
     * statusCodes = {
     *                  201 = "Resource(s) updated. Representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{staticlist_id}",requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="staticlist_name",strict=false,nullable=true)
     * @RequestParam(name="staticlist_description",strict=false, nullable=true)
     * @RequestParam(name="org_id")
     *
     * @param int $staticlist_id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function createStaticListEditAction($staticlist_id, ParamFetcher $paramFetcher)
    {
        /**
         * Removed $this->authorize() and added $this->ensureAccess([]) since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $staticlist_name = $paramFetcher->get('staticlist_name');
        $staticlist_desc = $paramFetcher->get('staticlist_description');
        $orgId = $paramFetcher->get(CourseConstant::ORG_ID);

        $loggedInUser = $this->getLoggedInUser();

        $response = $this->staticListService->updateStaticList($orgId, $loggedInUser, $staticlist_id, $staticlist_name, $staticlist_desc);
        $response = new Response($response, []);
        return $response;
    }

    /**
     * Delete Static List from course.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Static List",
     * section = "Static List",
     * statusCodes = {
     *                  204 = "Resource(s) deleted. No representation of resource(s) returned",
     *                  400 = "Validation errors have occurred",
     *                  403 = "Access denied",
     *                  404 = "Not found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{staticListId}/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     * @RequestParam(name="org_id")
     *
     * @param int $staticListId
     * @param int $orgId
     */
    public function deleteStaticListAction($staticListId, $orgId)
    {
        /**
         * Added $this->ensureAccess([]) method since this method will
         * take care of both proxy and non proxy user
         */
        $this->ensureAccess([]);
        $loggedInUser = $this->getLoggedInUser();
        $this->staticListService->deleteStaticList($orgId, $loggedInUser, $staticListId);
    }

    private function authorize()
    {
        if (!($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @deprecated
     */
    private function authorizeManager()
    {
        if (!($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))) {
            throw new AccessDeniedException();
        }
        /**
         *
         * @var TinyRbac_Manager $rbacMan
         */
        $rbacMan = $this->get('tinyrbac.manager');
        $rbacMan->initializeForUser();
    }
}