<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\FeatureService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\OrgPermissionSetDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class Organization Permission Set Controller
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/orgpermissionset")
 */
class OrgPermissionSetController extends AbstractAuthController
{

    /**
     * @var OrgPermissionsetService
     *
     *      @DI\Inject(OrgPermissionsetService::SERVICE_KEY)
     */
    private $permissionsetService;

    /**
     * @var FeatureService
     *
     *      @DI\Inject(FeatureService::SERVICE_KEY)
     */
    private $featureService;

    const PERM_DEMOGRAPHIC = "profileBlocks-1";

    /**
     * Creates an organization permission set.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Organization Permission Set",
     * input = "Synapse\RestBundle\Entity\OrgPermissionSetDto",
     * output = "Synapse\RestBundle\Entity\OrgPermissionSetDto",
     * section = "Organization Permission Set",
     * statusCodes = {
     *                  201 = "Permission set was created. Representation of resource(s) was returned",
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
     * @param OrgPermissionSetDto $orgPermissionSetDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createOrganizationPermissionSetAction(OrgPermissionSetDto $orgPermissionSetDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
            return View::create(new Response($orgPermissionSetDto, [$validationErrors[0]->getMessage ()]), 400);
        } else {
            $permissionSet = $this->permissionsetService->createOrgPermissionset($orgPermissionSetDto, false);
            return new Response($permissionSet);
        }
    }

    /**
     * Updates an organization permission set.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Organization Permission Set",
     * input = "Synapse\RestBundle\Entity\OrgPermissionSetDto",
     * output = "Synapse\RestBundle\Entity\OrgPermissionSetDto",
     * section = "Organization Permission Set",
     * statusCodes = {
     *                  201 = "Permission set was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param OrgPermissionSetDto $orgPermissionSetDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateOrganizationPermissionSetAction(OrgPermissionSetDto $orgPermissionSetDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        if (count($validationErrors) > 0) {
             return View::create(new Response($orgPermissionSetDto, [$validationErrors[0]->getMessage ()]), 400);
        } else {
            $permissionSet = $this->permissionsetService->updateOrgPermissionset($orgPermissionSetDto);
            return new Response($permissionSet);
        }
    }

    /**
     * Gets an organization's permission set.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Organization Permission Set",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Organization Permission Set",
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
     * @QueryParam(name="id", requirements="\d+", strict=true, description="Permissionset Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getOrganizationPersmissionsetAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $permissionSetId = (int) $paramFetcher->get('id');

        $permissionSet = $this->permissionsetService->getPermissionSetsDataById($permissionSetId);
        return new Response($permissionSet);
    }

    /**
     * Gets all permission sets of an organization.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Organization Permission Sets.",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Organization Permission Set",
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
     * @Rest\Get("/list", requirements={"_format"="json"})
     * @QueryParam(name="orgId", requirements="\d+", strict=true, description="Organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     * @deprecated
     */
    public function getOrganizationPersmissionsetsAction(ParamFetcher $paramFetcher)
    {
        $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
        $orgId = (int) $paramFetcher->get('orgId');

        $permissionSet = $this->permissionsetService->getPermissionSetsByOrganizationId($orgId);
        return new Response($permissionSet);
    }

    /**
     * Get master feature status.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Features List",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Organization Permission Set",
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
     * @Rest\Get("/feature", requirements={"_format"="json"})
     * @QueryParam(name="orgid", requirements="\d+", strict=true, description="Organization Id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getFeatureMasterStatusAction(ParamFetcher $paramFetcher)
    {
        $organizationId = (int) $paramFetcher->get('orgid');

        $feature = $this->featureService->getListMasterFeaturesStatus($organizationId);
        return new Response($feature);
    }

    /**
     * Gets the permission set for the logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Logged-In User's Permission Set",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/permissions", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getLoggedInPermissionSetAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $permissionSet = $this->permissionsetService->getPermissionSetsByUser($loggedInUserId);
        return new Response($permissionSet);
    }

    /**
     * Gets all allowed profile blocks for logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Profile Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/profileBlocks", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedProfileBlockAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $profileBlocks = $this->permissionsetService->getProfileblockPermission($loggedInUserId);
        return new Response($profileBlocks);
    }

    /**
     * Gets all allowed survey blocks for logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Survey Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/surveyBlocks", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedSurveyBlocksAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $surveyBlocks = $this->permissionsetService->getSurveyBlocksPermission($loggedInUserId);
        return new Response($surveyBlocks);
    }

    /**
     * Gets all allowed feature blocks for logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Feature Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/features", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedFeaturesAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $features = $this->permissionsetService->getFeaturesPermission($loggedInUserId);
        return new Response($features);
    }

    /**
     * Gets the allowed access level for the logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Access Level",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/accessLevel", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedAccessLevelAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $accessLevels = $this->permissionsetService->getAccessLevelPermission($loggedInUserId);
        return new Response($accessLevels);
    }

    /**
     * Get allowed Risk and intent to leave for logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Risk Indicator",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/riskIndicator", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedRiskIndicatorAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $riskIndicators = $this->permissionsetService->getRiskIndicator($loggedInUserId);
        return new Response($riskIndicators);
    }

    /**
     * Gets the allowed feature blocks for logged-in person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Feature Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/featuresBlock", requirements={"_format"="json"})     
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedFeaturesBlockAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $featureBlocks = $this->permissionsetService->getFeaturesBlockPermission($loggedInUserId);
        return new Response($featureBlocks);
    }
    
    /**
     * Get allowed feature blocks for person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Feature Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
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
     *
     * @Rest\Get("/usersFeature", requirements={"_format"="json"})
     * @QueryParam(name="userId", strict=true, description="user id should be loggedUserId")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getUserFeaturesBlockAction(ParamFetcher $paramFetcher)
    {        
        $userId = $paramFetcher->get('userId');
        // Verifying passed personId through API to loggedInPersonId
        $this->rbacManager->validateUserAsAuthorizedAppointmentUser($userId);

        $featureBlocks = $this->permissionsetService->getUserFeaturesPermission($userId);
        return new Response($featureBlocks);
    }

    /**
     * Check allowed feature permission for loggedin person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Feature Access",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/featureAccess", requirements={"_format"="json"})
     * @QueryParam(name="featureType", strict=true, description="Feature type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getAllowedFeatureAccessAction(ParamFetcher $paramFetcher)
    {
        $featureType = $paramFetcher->get('featureType');
        $loggedInUserId = $this->getLoggedInUserId();
        $featureAccess = $this->permissionsetService->getAllowedFeatureAccess($loggedInUserId, $featureType);
        return new Response($featureAccess);
    }

    /**
     * Get allowed ISP block from permission set for loggedin person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed ISP Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/ispBlocks", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedIspBlocksAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $ispBlocks = $this->permissionsetService->getAllowedIspIsqBlocks('isp', $loggedInUserId);
        return new Response($ispBlocks);
    }

    /**
     * Get allowed ISQ block from permission set for loggedin person.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed ISQ Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/isqBlocks", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedIsqBlocksAction()
    {
        $isqBlocks = $this->permissionsetService->getAllowedIspIsqBlocks('isq');
        return new Response($isqBlocks);
    }
    
    /**
     * Gets the allowed reports for the logged-in user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Reports",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/reports", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getAllowedReportsAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $reports = $this->permissionsetService->getAllowedReports($loggedInUserId);
        return new Response($reports);
    }

    /**
     * Gets a student's accessible features.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Allowed Student Features",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/studentFeatures", requirements={"_format"="json"})
     * @QueryParam(name="student", strict=true, description="Feature type")
     * @QueryParam(name="staff", strict=true, description="Feature type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getStudentFeatureAction(ParamFetcher $paramFetcher)
    {
        $student = $paramFetcher->get('student');
        $staff = $paramFetcher->get('staff');

        $students = $this->permissionsetService->getStudentFeature($student, $staff);
        return new Response($students);
    }
    
    /**
     * Gets course permission access from permission.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Courses Access",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/coursesAccess", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getCoursesAccessAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $students = $this->permissionsetService->getCoursesAccess($loggedInUserId);
        return new Response($students);
    }
    
}