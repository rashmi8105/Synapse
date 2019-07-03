<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\ContactsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;

/**
 * ContactsController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/contacts")
 */
class ContactsController extends AbstractAuthController
{

    /**
     * @var ContactsService Contacts service
     *     
     *      @DI\Inject(ContactsService::SERVICE_KEY)
     */
    private $contactsService;

    /**
     * Create Contact
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Contact",
     * input = "Synapse\RestBundle\Entity\ContactsDto",
     * output = "Synapse\RestBundle\Entity\ContactsDto",
     * section = "Contacts",
     * statusCodes = {
     *                  201 = "Contact was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ConstraintViolationListInterface $validationErrors
     * @param ContactsDto $contactsDto
     * @return Response
     */
    public function createContactAction(ContactsDto $contactsDto, ConstraintViolationListInterface $validationErrors)
    {
        // permission check for create contact        
        $this->ensureAccess([self::PERM_CONTACTS_PUBLIC_CREATE, self::PERM_CONTACTS_PRIVATE_CREATE, self::PERM_CONTACTS_TEAMS_CREATE]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($contactsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $contact = $this->contactsService->createContact($contactsDto);
            return new Response($contact);
        }
    }

    /**
     * Get ContactTypes
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Contacts",
     * section = "Contacts",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Get("/contactTypes", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    public function getContactTypesAction()
    {
        $contact_types_list = $this->contactsService->getContactTypes();
        return new Response($contact_types_list);
    }

    /**
     * Edit Contact Action
     *
     * @ApiDoc(
     * resource = true,
     * description = "Edit Contacts",
     * input = "Synapse\RestBundle\Entity\ContactsDto",
     * output = "Synapse\RestBundle\Entity\ContactsDto",
     * section = "Contacts",
     * statusCodes = {
     *                  201 = "Contact was edited. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\Put("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param ConstraintViolationListInterface $validationErrors
     * @param ContactsDto $contactsDto
     * @return Response
     */
    public function editContactAction(ContactsDto $contactsDto, ConstraintViolationListInterface $validationErrors)
    {   
        $this->ensureAccess([self::PERM_CONTACTS_PUBLIC_CREATE, self::PERM_CONTACTS_PRIVATE_CREATE, self::PERM_CONTACTS_TEAMS_CREATE]);
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($contactsDto, $errors), SynapseConstant::BAD_REQUEST_HTTP_ERROR_CODE);
        } else {
            $contact = $this->contactsService->editContacts($contactsDto);
            return new Response($contact);
        }
    }

    /**
     * Deletes a Contact
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Contact",
     * section = "Contacts",
     * statusCodes = {
     *                  204 = "Contact was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{id}", requirements={"id" = "\d+","_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param integer $id
     */
    public function deleteContactAction($id)
    {
        $this->ensureAccess([self::PERM_CONTACTS_PUBLIC_CREATE, self::PERM_CONTACTS_PRIVATE_CREATE, self::PERM_CONTACTS_TEAMS_CREATE]);
        $this->contactsService->deleteContact($id);
        // NO RETURN!!!
    }

    /**
     * View Contacts
     *
     * @ApiDoc(
     * resource = true,
     * description = "View Contacts",
     * section = "Contacts",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     * @Rest\GET("/{id}", requirements={"id" = "\d+","_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param integer $id
     * @return Response
     */
    public function viewContactAction($id)
    {
        $this->ensureAccess([self::PERM_CONTACTS_PUBLIC_VIEW, self::PERM_CONTACTS_PRIVATE_VIEW, self::PERM_CONTACTS_TEAMS_VIEW]);
        $contact = $this->contactsService->viewContact($id);
        return new Response($contact);
    }
}