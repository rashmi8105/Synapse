<?php
namespace Synapse\HelpBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\HelpBundle\EntityDto\TicketDto;
use Synapse\HelpBundle\Service\Impl\ZendeskService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class TicketsController
 *
 * @package Synapse\HelpBundle\Controller
 *
 * @Rest\Prefix("/tickets")
 */
class TicketsController extends AbstractAuthController
{

    /**
     * @var ZendeskService
     *
     *      @DI\Inject(ZendeskService::SERVICE_KEY)
     */
    private $helpdeskService;

    /**
     * Gets all help ticket categories that can be assigned.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Ticket Categories",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tickets",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/categories", requirements={"_format" = "json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getTicketCategoriesAction()
    {
        $categories = $this->helpdeskService->getCategories();

        return new Response($categories);
    }

    /**
     * Creates a new help desk ticket.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Ticket",
     * input = "Synapse\HelpBundle\EntityDto\TicketDto",
     * output = "Synapse\HelpBundle\EntityDto\TicketDto",
     * section = "Tickets",
     * statusCodes = {
     *                  201 = "Resource(s) created. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param TicketDto $ticketDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createTicketAction(TicketDto $ticketDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            return View::create(new Response($ticketDto, [$validationErrors[0]->getMessage ()]), 400);
        } else {
            $ticket = $this->helpdeskService->createTicket($ticketDto);
            return new Response($ticket);
        }
    }

    /**
     * Creates an attachment to a ticket.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Attachment",
     * input = "Symfony\Component\HttpFoundation\Request",
     * section = "Tickets",
     * statusCodes = {
     *                  204 = "Resource(s) created. No representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("/attachment")
     * @Rest\View(statusCode=201)
     *
     * @param Request $request
     */
    public function createAttachmentAction(Request $request)
    {
       $file = $request->files->get('file');
       $this->helpdeskService->createAttachment($file);
    }
    
    /**
     * Gets a ticket's subdomain.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get SubDomain",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Tickets",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/subdomain", requirements={"_format" = "json"})
     * @Rest\View(statusCode=200)
     */
    public function getTicketSubdomainAction()
    {
        $subDomain = $this->helpdeskService->getSubDomain();
        return new Response($subDomain);
    }
}
