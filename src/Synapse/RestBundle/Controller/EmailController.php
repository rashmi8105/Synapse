<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\ContactsService;
use Synapse\CoreBundle\Service\Impl\EmailActivityService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\EmailDto;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * ContactsController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/emailactivity")
 */
class EmailController extends AbstractAuthController
{

    /**
     *
     * @var EmailActivityService
     *     
     *      @DI\Inject(EmailActivityService::SERVICE_KEY)
     */
    private $emailActivityService;

    /**
     *
     * @var EmailService
     *
     *      @DI\Inject(EmailService::SERVICE_KEY)
     */
    private $emailService;

    /**
     * Creates an email.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Email",
     * input = "Synapse\RestBundle\Entity\EmailDto",
     * output = "Synapse\RestBundle\Entity\EmailDto",
     * section = "Email",
     * statusCodes = {
     *                  201 = "Email was created. Representation of resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=201)
     *
     * @param EmailDto $emailDto
     * @param ConstraintViolationListInterface $validationErrors            
     * @return Response
     * @throws AccessDeniedException
     */
    public function createEmailAction(EmailDto $emailDto, ConstraintViolationListInterface $validationErrors)
    {
        // permission check for create email
        $this->ensureAccess([self::PERM_EMAIL_PUBLIC_CREATE, self::PERM_EMAIL_PRIVATE_CREATE, self::PERM_EMAIL_TEAMS_CREATE]);
        $loggedInUser = $this->getLoggedInUser();
        $this->emailService->verifyThatThePersonLoggedInIsThePersonSendingTheEmail($loggedInUser, $emailDto);

        // validates the email in the "From" section of the modal, this needs to be a valid email
        if (!filter_var($emailDto->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(["Please enter a valid email address."]);
        }

        // If there are other errors create the validation
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($emailDto, $errors), 400);
        } else {
            $email = $this->emailActivityService->createEmail($emailDto);
            return new Response($email);
        }
    }
    
    /**
     * Gets an email's information.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Email",
     * section = "Email",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Get("/{emailId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $emailId
     * @return Response
     */
    public function viewEmailAction($emailId)
    {   
        // permission check for view email
        $this->ensureAccess([self::PERM_EMAIL_PUBLIC_VIEW, self::PERM_EMAIL_PRIVATE_VIEW, self::PERM_EMAIL_TEAMS_VIEW]);
        $email = $this->emailActivityService->viewEmail($emailId);
        return new Response($email);
        
    }
    
    /**
     * Deletes an email.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Delete Email",
     * section = "Email",
     * statusCodes = {
     *                  204 = "Course was deleted. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access denied",
     *                  404 = "Not Found",
     *                  500 = "There was either errors with the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * }
     * )
     *
     * @Rest\Delete("/{emailId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param int $emailId
     * @return Response
     */
    public function deleteEmailAction($emailId)
    {
        $this->ensureAccess([self::PERM_EMAIL_PUBLIC_CREATE,self::PERM_EMAIL_PRIVATE_CREATE, self::PERM_EMAIL_TEAMS_CREATE]);
        $email = $this->emailActivityService->deleteEmail($emailId);
        return new Response($email);
    }

}