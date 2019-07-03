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
use Synapse\CoreBundle\SynapseConstant;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use Synapse\DataBundle\Service\Impl\ProfileBlocksService;
use Synapse\RestBundle\Entity\Response;

/**
 * Class ProfileBlocksController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/profileblocks")
 */
class ProfileBlocksController extends AbstractAuthController
{

    /**
     * @var ProfileBlocksService profileblocks service
     *     
     *      @DI\Inject(ProfileBlocksService::SERVICE_KEY)
     */
    private $profileBlocksService;

    /**
     * Creates new ProfileBlocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Profile Blocks",
     * input = "Synapse\DataBundle\EntityDto\ProfileBlocksDto",
     * output = "Synapse\DataBundle\EntityDto\ProfileBlocksDto",
     * section = "Profile Blocks",
     * statusCodes = {
     *                  201 = "Profile blocks were created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ProfileBlocksDto $profileBlocksDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createProfileBlocksAction(ProfileBlocksDto $profileBlocksDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
           
            return View::create(new Response($profileBlocksDto, [$validationErrors[0]->getMessage ()]), 400);
        } else {
            $profileBlocks = $this->profileBlocksService->createProfileBlocks($profileBlocksDto);
            
            return new Response($profileBlocks);
        }
    }

    /**
     * Updates ProfileBlocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Update Profile Blocks",
     * input = "Synapse\DataBundle\EntityDto\ProfileBlocksDto",
     * output = "Synapse\DataBundle\EntityDto\ProfileBlocksDto",
     * section = "Profile Blocks",
     * statusCodes = {
     *                  201 = "Profile blocks were updated. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ProfileBlocksDto $profileBlocksDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function updateProfileBlocksAction(ProfileBlocksDto $profileBlocksDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
           
            return View::create(new Response($profileBlocksDto, [$validationErrors[0]->getMessage ()]), 400);
        } else {
            $profileBlocks = $this->profileBlocksService->updateProfileBlocks($profileBlocksDto);
           
            return new Response($profileBlocks);
        }
    }

    /**
     * Deletes ProfileBlocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Profile Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile Blocks",
     * statusCodes = {
     *                  204 = "Profile blocks were deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{id}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $id
     * @return Response
     */
    public function deleteProfileBlocksAction($id)
    {
        $profileBlock = $this->profileBlocksService->deleteProfileBlocks($id);
        return new Response($profileBlock);
    }
    
    /**
     * Get all ProfileBlocks.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Profile Blocks",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile Blocks",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("", requirements={"_format"="json"})
     * @QueryParam(name="type", default ="", strict=false,description="type")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getProfileBlocksAction(ParamFetcher $paramFetcher)
    {
        $loggedInUserId = $this->getLoggedInUserId();
        $type = $paramFetcher->get('type');
        $profileBlock = $this->profileBlocksService->getDatablocks($loggedInUserId, $type);
        return new Response($profileBlock);
    }

    /**
     * Gets a profile block by its id.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get a Single Profile Block",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Profile Blocks",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/{id}",requirements={"_format"="json"})
     * @QueryParam(name="exclude", default ="", strict=false,description="exclude")
     * @QueryParam(name="exclude-type", default ="", strict=false,description="exclude-type")
     * @Rest\View(statusCode=200)
     *
     * @param int $id
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function getProfileBlockByIdAction($id, ParamFetcher $paramFetcher)
    {
        $exclude = $paramFetcher->get('exclude');
        $excludeType = $paramFetcher->get('exclude-type');
        $profileBlock = $this->profileBlocksService->getBlockById($id, $exclude ,$excludeType);
        return new Response($profileBlock);
    }
}
