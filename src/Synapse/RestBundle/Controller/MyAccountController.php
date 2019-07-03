<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CoreBundle\Service\Impl\AccessLogService;
use Synapse\CoreBundle\Service\Impl\ActivityLogService;
use Synapse\CoreBundle\Service\Impl\FeatureService;
use Synapse\CoreBundle\Service\Impl\LoggedInPersonService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\SurveyService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\AccessLogDto;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\MyAccountDto;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class MyAccountController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/myaccount")
 */
class MyAccountController extends AbstractAuthController
{

    /**
     * @var PersonService
     *     
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * @var AccessLogService
     *     
     *      @DI\Inject(AccessLogService::SERVICE_KEY)
     */
    private $accessLogService;

    /**
     * @var ActivityLogService
     *     
     *      @DI\Inject(ActivityLogService::SERVICE_KEY)
     */
    private $activityLogService;

    /**
     * @var FeatureService
     *     
     *      @DI\Inject(FeatureService::SERVICE_KEY)
     */
    private $featureService;

    /**
     * @var OrgPermissionsetService
     *
     *      @DI\Inject(OrgPermissionsetService::SERVICE_KEY)
     */
    private $orgPermissionSet;

    /**
     * @var LoggedInPersonService
     *     
     *      @DI\Inject(LoggedInPersonService::SERVICE_KEY)
     */
    private $loggedInPersonService;

    /**
     * @var SurveyService
     *
     *      @DI\Inject(SurveyService::SERVICE_KEY)
     */
    private $surveyService;

    /**
     * @var TokenService
     *
     *      @DI\Inject(TokenService::SERVICE_KEY)
     */
    private $tokenService;

    /**
     * @var CalendarIntegrationService
     *
     *      @DI\Inject(CalendarIntegrationService::SERVICE_KEY)
     */
    private $calendarIntegrationService;
    

    /**
     * Get refresh.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Refresh",
     * section = "My Account",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/refresh", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function refreshAction()
    {
        $apiToken = $this->get('security.context')->getToken()->getToken();
        $loggedInUserId = $this->loggedInUser->getId();
        $tokenArray = $this->tokenService->regenerateToken($apiToken, $loggedInUserId);
        return new Response($tokenArray);
    }
    

    /**
     * Get My Account Details
     *
     * @ApiDoc(
     * resource = true,
     * description = "My Account",
     * section = "My Account",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/basic", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getMyAccountAction()
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $personDetails = $this->personService->getPerson($loggedInUserId);
        return new Response($personDetails);
    }

    /**
     * Updates the details of a user's account.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update My Account",
     * input = "Synapse\RestBundle\Entity\MyAccountDto",
     * section = "My Account",
     * statusCodes = {
     *                  204 = "Account was updated. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param MyAccountDto $myAccountDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response
     */
    public function updateMyAccountAction(MyAccountDto $myAccountDto, ConstraintViolationListInterface $validationErrors)
    {
        $logger = $this->get('logger');
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($myAccountDto, $errors), 400);
        } else {
            $loggedInUserId = $this->getLoggedInUserId();
            $userId = $myAccountDto->getPersonId();
            if ($loggedInUserId != $userId) {
                $logger->debug("Used Id does't match: User id as argument $userId and logged in user id $loggedInUserId");
                throw new ValidationException([
                    'Invalid Used ID'
                ], 'Used Id does\'t match-userid = loggedin', "invalid_user");
            }
            $personService = $this->personService;
            $personDetails = $personService->updateMyAccount($myAccountDto);
            $personDetails = $personService->getPerson($myAccountDto->getPersonId());
            return new Response($personDetails);
        }
    }

    /**
     * Gets the details of the user that is currently logged-in.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Logged-In User Details",
     * section = "My Account",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getLoggedInUserDetailsAction()
    {
        $person = $this->getLoggedInUser();
        if ($person) {
            $request = Request::createFromGlobals();
            $switchUser = $request->headers->get('switch-user');
            $loggedInUser = $this->personService->getLoggedInUserDetails($person);
            $orgId = $loggedInUser->getOrganizationId();
            $loggedUserId = $loggedInUser->getId();

            // direct user login only update the access Log
            if (!$switchUser) {
                $accessLogDtoObj = new AccessLogDto();

                $accessLogDtoObj->setOrganization($orgId);
                $accessLogDtoObj->setPerson($loggedUserId);
                $accessLogDtoObj->setEvent('Login');
                $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
                $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
                $accessLogDtoObj->setBrowser($userAgent);
                $accessLogDtoObj->setSourceip($remoteAddr);
                $apiToken = $this->get('security.context')->getToken()->getToken();
                $userToken = $this->get('security.context')->getToken()->getUser()->getActivationToken();
                $accessLogDtoObj->setUserToken($userToken);
                $accessLogDtoObj->setApiToken($apiToken);
                $this->accessLogService->createAccessLog($accessLogDtoObj);
            } else {
                // get proxy user details
                $proxyUserDetails = $this->container->get('ebi_user');
                if (!isset($proxyUserDetails) || empty($switchUser)) {
                    $proxy['is_proxy_user'] = false;
                } else {
                    $proxy['is_proxy_user'] = true;
                    $proxy['proxy_user_firstname'] = $proxyUserDetails->getFirstname();
                    $proxy['proxy_user_lastname'] = $proxyUserDetails->getLastname();
                    $proxy['proxy_user_id'] = $proxyUserDetails->getId();
                }
                $loggedInUser->setProxy($proxy);
            }

            // updating activity log
            $activityLogDto = new ActivityLogDto();
            $currentDate = new \DateTime('now');
            $activityLogDto->setActivityDate($currentDate);
            $activityLogDto->setActivityType("L");
            $activityLogDto->setOrganization($orgId);
            $activityLogDto->setPersonIdFaculty($loggedUserId);
            $activityLogDto->setReason('Login');
            $this->activityLogService->createActivityLog($activityLogDto);

            $riskIntendToLeave = $this->orgPermissionSet->getRiskIndicator($loggedUserId);
            $loggedInUser->setRiskIndicator($riskIntendToLeave['risk_indicator']);
            $loggedInUser->setIntentToLeave($riskIntendToLeave['intent_to_leave']);

            $retentionCompletion = $this->orgPermissionSet->getRetentionCompletion($loggedUserId, $orgId);
            $loggedInUser->setRetentionCompletion($retentionCompletion);

            $feature = $this->featureService->getFeaturesStatusByOrg($orgId);
            $loggedInUser->setOrgFeatures($feature);
            $loggedUserType = $loggedInUser->getType();
            $permissionSetsAssignedToPerson = $this->personService->getPersonPermission($loggedUserId, $loggedUserType, $feature);
            $isMultiCampusUser = $this->loggedInPersonService->getIsMulticampusUser($loggedUserId);
            $loggedInUser->setUserFeaturePermissions($permissionSetsAssignedToPerson);
            $loggedInUser->setIsMulticampusUser($isMultiCampusUser);
            $tierUserType = $this->loggedInPersonService->getUserTierType($loggedUserId);
            $loggedInUser->setTierLevel($tierUserType);

            $permissionTemplates = $this->loggedInPersonService->getUserPermissionTemplates($loggedUserId, $loggedUserType);
            $loggedInUser->setPermissions($permissionTemplates);
            $isCourseEnabled = $this->loggedInPersonService->getOrgPersonCourseTabPermission($loggedUserId, $orgId, $loggedUserType);
            $loggedInUser->setCourseTabEnable($isCourseEnabled);
            $accessLevel = $this->orgPermissionSet->getAccessLevelPermission($loggedUserId);
            $accessLevelOnly = $accessLevel['access_level'];

            $courseAccess = $this->orgPermissionSet->getCoursesAccess($loggedUserId);
            $loggedInUser->setCoursesAccess($courseAccess);
            $loggedInUser->setAccessLevel($accessLevelOnly);

            //Get Survey Status to show or hide top5 issues widget
            $surveyStatus = $this->surveyService->getWessLinkSurveyStatus($orgId);
            $loggedInUser->setIsSurveyClose($surveyStatus);
            $surveyAccess = $this->orgPermissionSet->getSurveyBlocksPermission($loggedUserId);
            $surveyAllowed = (!empty($surveyAccess['survey_blocks'])) ? true : false;
            $loggedInUser->setIsSurveyAllowed($surveyAllowed);
            $policy = $this->loggedInPersonService->getPrivacyPolicy($loggedUserId, $orgId, $loggedUserType);
            $loggedInUser->setIsPrivacyPolicyAccepted($policy['is_accepted']);
            $loggedInUser->setPrivacyPolicyAcceptedDate($policy['accepted_date']);
            $googleEmailAddress = $this->calendarIntegrationService->getFaultyGoogleEmail($orgId, $loggedUserId);
            $loggedInUser->setGoogleEmailId($googleEmailAddress);
        }
        return new Response($loggedInUser);
    }

}