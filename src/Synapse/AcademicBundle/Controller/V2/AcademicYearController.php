<?php

namespace Synapse\AcademicBundle\Controller\V2;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\AcademicBundle\Service\Impl\AcademicTermService;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Service\Utility\APIValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response as Response;

/**
 * Class AcademicYearController
 *
 * @package Synapse\RestBundle\Controller
 *
 * @Rest\Version("2.0")
 * @Rest\Prefix("/organization/academicyears")
 *
 */
class AcademicYearController extends AbstractAuthController
{
    /**
     * @var AcademicTermService
     * @DI\Inject(AcademicTermService::SERVICE_KEY)
     */
    private $academicTermService;

    /**
     * @var AcademicYearService
     * @DI\Inject(AcademicYearService::SERVICE_KEY)
     */
    private $academicYearService;

    /**
     * @var Logger
     * @DI\Inject(SynapseConstant::CONTROLLER_LOGGING_CHANNEL)
     */
    private $apiLogger;

    /**
     * Get academic years for an organization.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get academic years",
     *          output = "Synapse\AcademicBundle\EntityDto\AcademicYearListResponseDto",
     *          section = "Academic Years",
     *          statusCodes = {
     *                          200 = "Returned when successful",
     *                          400 = "Validation errors have occurred.",
     *                          403 = "Access denied exception",
     *                          500 = "There was an internal server error.",
     *                          504 = "Request has timed out. Please re-try."
     *                        },
     *          views = { "public" }
     *
     * )
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @return Response
     */
    public function getAcademicYearsAction()
    {
        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; IP Address: $ipAddress");
        $this->apiValidationService->isAPIIntegrationEnabled();
        $this->apiValidationService->isOrganizationAPICoordinator($organizationId, $loggedInUserId);
        $response = $this->academicYearService->listAcademicYears($organizationId, false, false, false);
        return new Response($response);
    }

    /**
     * Get academic terms for a given Academic year and organization.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get academic terms",
     *          output = "Synapse\AcademicBundle\EntityDto\AcademicTermListResponseDto",
     *          section = "Academic Terms",
     *          statusCodes = {
     *                          200 = "Returned when successful",
     *                          400 = "Validation errors have occurred.",
     *                          403 = "Access denied exception",
     *                          500 = "There was an internal server error.",
     *                          504 = "Request has timed out. Please re-try."
     *                        },
     *          views = { "public" }
     *
     * )
     * @Rest\Get("/{yearId}/academicterms", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     * @param string $yearId
     * @return Response
     */
    public function getAcademicTermsAction($yearId)
    {
        $ipAddress = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
        $loggedInUser = $this->getUser();
        $loggedInUserId = $loggedInUser->getId();
        $organizationId = $loggedInUser->getOrganization()->getId();
        $this->apiLogger->notice(__FUNCTION__ . "; PersonId: $loggedInUserId; OrganizationId: $organizationId; IP Address: $ipAddress; YearId: $yearId");
        $this->apiValidationService->isAPIIntegrationEnabled();
        $this->apiValidationService->isOrganizationAPICoordinator($organizationId, $loggedInUserId);
        $response = $this->academicTermService->listAcademicTerms($organizationId, null, $loggedInUserId, null, false, $yearId);
        return new Response($response);
    }
}