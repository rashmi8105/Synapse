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
use Synapse\CoreBundle\Service\Impl\OrgProfileService;
use Synapse\CoreBundle\Service\Impl\ProfileService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Converter\ProfileDtoConverter;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class ProfileController
 *
 * @package Synapse\RestBundle\Controller
 *
 *          @Rest\Prefix("/profile_item")
 */
class ProfileController extends AbstractAuthController
{

    /**
     * @var OrgProfileService profile service
     *
     *      @DI\Inject(OrgProfileService::SERVICE_KEY)
     */
    private $orgProfileService;

    /**
     * @var ProfileService
     *
     *      @DI\Inject(ProfileService::SERVICE_KEY)
     */
    private $profileService;


    /**
     * Creates a new profile.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Profile",
     * input = "Synapse\RestBundle\Entity\ProfileDto",
     * output = "Synapse\RestBundle\Entity\ProfileDto",
     * section = "Profile",
     * statusCodes = {
     *                  201 = "Profile was created. Representation of resource(s) was returned",
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
     * @param ProfileDto $profileDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createProfileAction(ProfileDto $profileDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            return View::create(new Response($profileDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            if ($profileDto->getDefinitionType() == 'O') {
                $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
                $profile = $this->orgProfileService->createProfile($profileDto);
            } else {
                $profile = $this->profileService->createProfile($profileDto);
            }

            return new Response($profile);
        }
    }

    /**
     * Updates a profile.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Profile",
     * input = "Synapse\RestBundle\Entity\ProfileDto",
     * output = "Synapse\RestBundle\Entity\ProfileDto",
     * section = "Profile",
     * statusCodes = {
     *                  201 = "Profile was updated. Representation of resource(s) was returned",
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
     * @param ProfileDto $profileDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateProfileAction(ProfileDto $profileDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            return View::create(new Response($profileDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            if ($profileDto->getDefinitionType() == 'O') {
                $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
                $profile = $this->orgProfileService->editProfile($profileDto);
            } else {
                $profile = $this->profileService->updateProfile($profileDto);
            }

            return new Response($profile);
        }
    }

    /**
     * Reorders a profile.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Re-Order Profile",
     * input = "Synapse\RestBundle\Entity\ProfileDto",
     * output = "Synapse\RestBundle\Entity\ProfileDto",
     * section = "Profile",
     * statusCodes = {
     *                  201 = "Profile order was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/reorder", requirements={"_format"="json"})
     * @QueryParam(name="type", default ="", requirements ="[E,O]",strict=true, description="type")
     * @Rest\View(statusCode=201)
     *
     * @param ParamFetcher $paramFetcher
     * @param ReOrderProfileDto $reOrderProfileDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function reorderProfileAction(ParamFetcher $paramFetcher, ReOrderProfileDto $reOrderProfileDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            return View::create(new Response($reOrderProfileDto, [
                $validationErrors[0]->getMessage()
            ]), 400);
        } else {
            $type = $paramFetcher->get('type');
            if ($type == 'O') {
                $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
                $profile = $this->orgProfileService->reorderProfile($reOrderProfileDto);
            } else {
                $profile = $this->profileService->reorderProfile($reOrderProfileDto);
            }

            return new Response($profile);
        }
    }

    /**
     * Deletes a profile.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Profile",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile",
     * statusCodes = {
     *                  204 = "Profile was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @QueryParam(name="type", default ="", requirements ="[E,O]",strict=true, description="type")
     * @Rest\View(statusCode=201)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function deleteProfileAction($id, ParamFetcher $paramFetcher)
    {
        $type = $paramFetcher->get('type');
        if ($type == 'O') {
            $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
            $profile = $this->orgProfileService->deleteProfile($id);
        } else {
            $profile = $this->profileService->deleteProfile($id);
        }

        return new Response($profile);
    }

    /**
     * Gets EBI profiles.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get EBI Profile",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/ebi", requirements={"_format"="json"})
     * @QueryParam(name="status", requirements="(active || archive)", strict=true, description="status")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getEbiProfileAction(ParamFetcher $paramFetcher)
    {
        $status = $paramFetcher->get('status');
        $profile = $this->profileService->getProfiles($status);
        return new Response($profile);
    }

    /**
     * Gets organization-specific profiles.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Organization Profile",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile",
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
     * @Rest\Get("/org/{id}", requirements={"_format"="json"})
     * @QueryParam(name="exclude", default ="", strict=false,description="exclude")
     * @QueryParam(name="status", requirements="(active || archive)", strict=false, description="status")
     * @QueryParam(name="exclude-type", default ="", strict=false, description="exclude-type")
     * @QueryParam(name="has_students", default = false, strict=false,description="Determines if the API returns only profile items with students (true) or all profile items (false)")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getOrgProfileAction($id, ParamFetcher $paramFetcher)
    {
        //Common api for faculty and coordinator with same result        
        $exclude = $paramFetcher->get('exclude');
        $status = $paramFetcher->get('status');
        $excludeType = $paramFetcher->get('exclude-type');
        $hasStudents = filter_var($paramFetcher->get('has_students'), FILTER_VALIDATE_BOOLEAN);
        $authFlag = true; // This is added as the service expects a default param as true.
        $loggedInUserId = $this->getLoggedInUserId();

        if (!$hasStudents) {
            $loggedInUserId = null;
        }


        $profile = $this->orgProfileService->getInstitutionSpecificProfileBlockItems($id, $exclude, $status, $authFlag, $excludeType, $loggedInUserId);
        return new Response($profile);
    }

    /**
     * @ApiDoc(
     *          resource = true,
     *          description = "Get ISP or profile item metadata",
     *          output = "Synapse\RestBundle\Entity\Response",
     *          section = "Reports",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resources was returned.",
     *                          400 = "Validation error has occurred.",
     *                          403 = "Access Denied",
     *                          404 = "Not found",
     *                          500 = "Internal server error",
     *                          504 = "Request has timed out. Please re-try."
     *                         },
     *
     * )
     * @Rest\Get("/{id}", requirements={"_format"="json"})
     * @QueryParam(name="type", default ="", strict=true, requirements ="[E,O]",description="type")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getProfileAction($id, ParamFetcher $paramFetcher)
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $type = $paramFetcher->get('type');
        if ($type == 'O') {
            $profile = $this->orgProfileService->getProfile($id);
        } else {
            $profile = $this->profileService->getProfile($id);
        }
        return new Response($profile);
    }

    /**
     * Updates the status of a profile item.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Organization Permission Set",
     * input = "Synapse\RestBundle\Entity\ProfileDto",
     * output = "Synapse\RestBundle\Entity\ProfileDto",
     * section = "Profile",
     * statusCodes = {
     *                  201 = "Profile item was updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("/status", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ProfileDto $profileDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateProfileStatusAction(ProfileDto $profileDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {

            return View::create(new Response($profileDto, [
                $validationErrors[0]->getMessage()
                ]), 400);
        } else {
            if ($profileDto->getDefinitionType() == 'O') {
                $this->ensureAccess([self::PERM_COORINDATOR_SETUP]);
                $profile = $this->orgProfileService->updateProfileStatus($profileDto);
            } else {
                $profile = $this->profileService->updateProfileStatus($profileDto);
            }

            return new Response($profile);
        }
    }

    /**
     * Gets All Profile Blocks or a filtered subset of Profile blocks with specific student data,
     * contains year and term information.
     *
     * @ApiDoc(
     *          resource = true,
     *          description = "Get Profile blocks with data, contains year and term information",
     *          output = "Synapse\RestBundle\Entity\Response",
     *          section = "Reports",
     *          statusCodes = {
     *                          200 = "Request was successful. Representation of resources was returned.",
     *                          400 = "Validation error has occurred.",
     *                          404 = "Not found",
     *                          500 = "Internal server error",
     *                          504 = "Request has timed out. Please re-try."
     *                         },
     *
     * )
     * @Rest\Get("/profileitems/item", requirements={"_format"="json"})
     * @QueryParam(name="metakeys", default ="", strict=false, description="comma separated ebi metadata keys")
     * @Rest\View(statusCode=200)
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function getAllProfileBlocksAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $organizationId = $this->getLoggedInUserOrganizationId();
        $ebiMetaKeys = $paramFetcher->get('metakeys');
        $ebiMetaKeysArray = null;
        if($ebiMetaKeys != '' && $ebiMetaKeys != null) {
            $ebiMetaKeysArray = array_map('trim', explode(',', $ebiMetaKeys));
        }
        $profileBlocksAndBlockItems = $this->profileService->getDatablocksAndBlockitemsWithYearAndTermInformation($loggedInUserId , $organizationId, $ebiMetaKeysArray);
        return new Response($profileBlocksAndBlockItems);
    }
}