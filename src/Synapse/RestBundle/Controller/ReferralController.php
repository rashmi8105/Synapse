<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\DashboardReferralService;
use Synapse\CoreBundle\Service\Impl\ReferralService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\RecentReferralDashboardDto;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Entity\Response;


/**
 * Class ReferralController
 *
 * @Rest\Prefix("/referrals")
 */
class ReferralController extends AbstractAuthController
{

    /**
     * @var DashboardReferralService
     *
     *      @DI\Inject(DashboardReferralService::SERVICE_KEY)
     */
    private $referralDashboardService;

    /**
     * @var ReferralService
     *
     *      @DI\Inject(ReferralService::SERVICE_KEY)
     */
    private $referralService;

    /**
     * Gets a single referral.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Activity",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Referrals",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}", requirements={"id" = "\d+","_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @return Response
     */
    public function getReferralAction($id)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_VIEW, self::PERM_REFERRALS_PRIVATE_VIEW, self::PERM_REFERRALS_TEAMS_VIEW, self::PERM_REASON_REFERRALS_PUBLIC_VIEW, self::PERM_REASON_REFERRALS_PRIVATE_VIEW,self::PERM_REASON_REFERRALS_TEAMS_VIEW]);

        $referral = $this->referralService->getReferral($id);
        return new Response($referral, []);
    }

    /**
     * Creates a new referral.
     *
     * @ApiDoc(
     * resource = true,
     * input = "Synapse\RestBundle\Entity\ReferralsDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * description = "Create Referral",
     * section = "Referrals",
     * statusCodes = {
     *                  201 = "Referral was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ReferralsDTO $referralsDto
     * @return Response
     */
    public function createReferralAction(ReferralsDTO $referralsDto)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_CREATE, self::PERM_REFERRALS_PRIVATE_CREATE, self::PERM_REFERRALS_TEAMS_CREATE, self::PERM_REASON_REFERRALS_PRIVATE_CREATE, self::PERM_REASON_REFERRALS_PUBLIC_CREATE, self::PERM_REASON_REFERRALS_TEAMS_CREATE]);

        $studentIds = $referralsDto->getPersonStudentId();
        $studentsIds = explode(",", $studentIds);
        if (count($studentsIds) > 1) {
            $referral = $this->referralService->createBulkReferral($referralsDto);
        } else {
            $referral = $this->referralService->createReferral($referralsDto);
        }
        return new Response($referral);
    }

    /**
     * Edits a referral.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Referral",
     * input = "Synapse\RestBundle\Entity\ReferralsDTO",
     * output = "Synapse\RestBundle\Entity\ReferralsDTO",
     * section = "Referrals",
     * statusCodes = {
     *                  204 = "Referral was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param ReferralsDto $referralsDto
     * @return Response
     */
    public function editReferralAction(ReferralsDto $referralsDto)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_CREATE, self::PERM_REFERRALS_PRIVATE_CREATE, self::PERM_REFERRALS_TEAMS_CREATE, self::PERM_REASON_REFERRALS_PRIVATE_CREATE,self::PERM_REASON_REFERRALS_PUBLIC_CREATE, self::PERM_REASON_REFERRALS_TEAMS_CREATE]);
        $loggedInUserId = $this->getLoggedInUserId();

        $referral = $this->referralService->editReferral($referralsDto, $loggedInUserId);
        return new Response($referral);
    }

    /**
     * Deletes a single referral.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Referral",
     * section = "Referrals",
     * statusCodes = {
     *                  204 = "Referral was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"id" = "\d+","_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     * @return Response
     */
    public function deleteReferralAction($id)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_CREATE, self::PERM_REFERRALS_PRIVATE_CREATE, self::PERM_REFERRALS_TEAMS_CREATE, self::PERM_REASON_REFERRALS_PRIVATE_CREATE,self::PERM_REASON_REFERRALS_PUBLIC_CREATE, self::PERM_REASON_REFERRALS_TEAMS_CREATE]);

        $this->referralService->deleteReferral($id);
    }

    /**
     * Gets all referrals for the loggedInUser.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Referrals",
     * output = "Synapse\RestBundle\Entity\RecentReferralDashboardDto",
     * section = "Referrals",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type", requirements="[a-z]+", description="Referral type -sent received")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReferralsAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_VIEW,self::PERM_REFERRALS_PRIVATE_VIEW,self::PERM_REFERRALS_TEAMS_VIEW, self::PERM_REASON_REFERRALS_PUBLIC_VIEW, self::PERM_REASON_REFERRALS_PRIVATE_VIEW,self::PERM_REASON_REFERRALS_TEAMS_VIEW]);

        $type = $paramFetcher->get('type');
        if ($type == 'sent') {
            $referral = $this->referralService->getSentReferralByPerson($this->getUser());
        } elseif ($type == 'received') {            
            $referral = $this->referralService->getReceivedReferralByPerson($this->getUser());
        } else {
            $referralSummary = $this->referralService->getReferralSummaryForPerson( $this->getUser() );
            $referralDetails = $this->referralService->getRecentReferralDetails($this->getUser(), $numberOfRecords = 3, $offset = 0 );

            $recentReferralDashboardDto = new RecentReferralDashboardDto();
            $recentReferralDashboardDto->setPersonId($this->getUser()->getId());
            $recentReferralDashboardDto->setTotalOpenReferralsReceived($referralSummary['totalOpenReferralsReceived']);
            $recentReferralDashboardDto->setTotalReferralsReceived($referralSummary['totalReferralsReceived']);
            $recentReferralDashboardDto->setTotalOpenReferralsSent($referralSummary['totalOpenReferralsSent']);
            $recentReferralDashboardDto->setTotalReferralsSent($referralSummary['totalReferralsSent']);
            $recentReferralDashboardDto->setReferrals($referralDetails);
            $referral = $recentReferralDashboardDto;

        }
        return new Response($referral, []);
    }
    
    /**
     * Gets the details about a referral.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Referral Details",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Referrals",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/dashboard", requirements={"_format"="json"})
     * @QueryParam(name="filter", requirements="[a-z]+", description="Referral type -sent received")
     * @QueryParam(name="status", requirements="[A-Za-z]+", description="Referral status -open closed")
     * @QueryParam(name="offset", requirements="\d+", strict = false, description="No of records to be fetched")
     * @QueryParam(name="page_no", requirements="\d+", strict = false,  description="current page number")
     * @QueryParam(name="data", requirements="student-list", strict = false,  description="total Student list")
     * @QueryParam(name="sortBy", strict=false, description="sorting field")
     * @QueryParam(name="output-format", strict=false, description="download csv")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReferralDetailsAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_VIEW,self::PERM_REFERRALS_PRIVATE_VIEW,self::PERM_REFERRALS_TEAMS_VIEW, self::PERM_REASON_REFERRALS_PUBLIC_VIEW, self::PERM_REASON_REFERRALS_PRIVATE_VIEW,self::PERM_REASON_REFERRALS_TEAMS_VIEW]);

        $filter = $paramFetcher->get('filter');
        $status = $paramFetcher->get('status');
        $offset = $paramFetcher->get('offset');
        $pageNo = $paramFetcher->get('page_no');
        $data = $paramFetcher->get('data');
        $sortBy = $paramFetcher->get('sortBy');
        $outputFormat = trim($paramFetcher->get('output-format'));
        $isCSV =  ($outputFormat == 'csv') ? true : false;

        $referral = $this->referralDashboardService->getReferralDetailsBasedFilters($this->getUser(), $status, $filter, $offset, $pageNo, $data, $sortBy, $isCSV);
        return new Response($referral, []);
    }

    /**
     * Change referrals status to Open or Closed.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Change Referral Status",
     * input = "Synapse\RestBundle\Entity\ReferralsDto",
     * output = "Synapse\RestBundle\Entity\ReferralsDto",
     * section = "Referrals",
     * statusCodes = {
     *                  201 = "Referral status was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/{id}", requirements={"id" = "\d+","_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ReferralsDTO $referralsDto
     * @param int $id
     * @return Response
     */
    public function changeReferralStatusAction(ReferralsDto $referralsDto, $id)
    {
        $this->ensureAccess([self::PERM_REFERRALS_PUBLIC_CREATE, self::PERM_REFERRALS_PRIVATE_CREATE, self::PERM_REFERRALS_TEAMS_CREATE, self::PERM_REASON_REFERRALS_PRIVATE_CREATE, self::PERM_REASON_REFERRALS_PUBLIC_CREATE, self::PERM_REASON_REFERRALS_TEAMS_CREATE, self::PERM_REFERRALS_RECEIVE]);

        $loggedInUserId = $this->getLoggedInUserId();
        $referral = $this->referralService->changeReferralStatus($referralsDto, $loggedInUserId, $id);
        return new Response($referral);
    }


    /**
     * Gets the list of possible assignees or interested parties for the given student during referral creation or editing.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Connection Referrals",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Referrals",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/campus_connections")
     * @QueryParam(name="student_id", description="Student ID")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReferralCampusConnectionsAction(ParamFetcher $paramFetcher)
    {
        $studentId = $paramFetcher->get('student_id');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $referralCampusConnections = $this->referralService->getReferralCampusConnections($organizationId, $loggedInUserId, $studentId);
        return new Response($referralCampusConnections);
    }


    /**
     * Gets referred campus resources.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Campus Resource Referrals",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Referrals",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/campus_resources")
     * @QueryParam(name="student_id", description="Student ID")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReferralCampusResourcesAction(ParamFetcher $paramFetcher)
    {
        $studentId = $paramFetcher->get('student_id');
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $referralCampusResources = $this->referralService->getReferralCampusResources($organizationId, $loggedInUserId, $studentId);
        return new Response($referralCampusResources);
    }


    /**
     * Get campus resource for bulk action.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Referral Campus Resources Bulk Action",
     * input = "Synapse\RestBundle\Entity\ReferralsDTO",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Referrals",
     * statusCodes = {
     *                  201 = "Campus resource referral bulk action was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/campus_resources", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     * @RequestParam(name="student_ids")
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getReferralCampusResourcesBulkAction(ParamFetcher $paramFetcher)
    {
        $studentIds = $paramFetcher->get('student_ids', false);
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();

        $referralCampusResources = $this->referralService->getPossibleAssigneesForBulkAction($organizationId, $loggedInUserId, $studentIds);
        return new Response($referralCampusResources);
    }
}