<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\ProxyService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\ProxyDto;
use Synapse\RestBundle\Entity\Response;

/**
 * ProxyController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/proxy")
 */
class ProxyController extends AbstractAuthController
{

    /**
     * @var ProxyService proxy service
     *     
     *      @DI\Inject(ProxyService::SERVICE_KEY)
     */
    private $proxyService;

    /**
     * Creates a new proxy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Proxy",
     * input = "Synapse\RestBundle\Entity\ProxyDto",
     * output = "Synapse\RestBundle\Entity\ProxyDto",
     * section = "Proxy",
     * statusCodes = {
     *                  201 = "Proxy was created. Representation of resource(s) was returned",
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
     * @param ProxyDto $proxyDto
     * @param ConstraintViolationListInterface $validationErrors            
     * @return Response|View
     */
    public function createProxyAction(ProxyDto $proxyDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($proxyDto, $errors), 400);
        } else {
            $contact = $this->proxyService->createProxy($proxyDto);
            return new Response($contact);
        }
    }

    /**
     * Deletes a proxy.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Proxy",
     * section = "Proxy",
     * statusCodes = {
     *                  204 = "Proxy was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Delete("/{userId}/{proxiedUserId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $userId
     * @param int $proxiedUserId
     * @return Response
     */
    public function deleteProxyAction($userId, $proxiedUserId)
    {
        $this->proxyService->deleteProxy($userId, $proxiedUserId);
    }
}