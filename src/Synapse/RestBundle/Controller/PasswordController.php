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
use Synapse\CoreBundle\Service\Impl\AppointmentsService;
use Synapse\CoreBundle\Service\Impl\EmailPasswordService;
use Synapse\CoreBundle\Service\Impl\PasswordService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\CreatePasswordDto;
use Synapse\RestBundle\Entity\Response;

/**
 * Class PasswordController
 *
 * @package Synapse\RestBundle\Controller
 *         
 * @Rest\Prefix("/password")
 */
class PasswordController extends AbstractSynapseController
{

    /**
     * @var AppointmentsService
     *
     *      @DI\Inject(AppointmentsService::SERVICE_KEY)
     */
    private $appointmentsService;

    /**
     * @var EmailPasswordService
     *
     *      @DI\Inject(EmailPasswordService::SERVICE_KEY)
     */
    private $emailPasswordService;

    /**
     * @var PasswordService
     *
     *      @DI\Inject(PasswordService::SERVICE_KEY)
     */
    private $passwordService;

    /**
     * Creates a new password.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create Password",
     * input = "Synapse\RestBundle\Entity\CreatePasswordDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Password",
     * statusCodes = {
     *                  204 = "Password was created. No representation of resource was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "There were errors either in the body of the request or an internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Post("", requirements={"_format"="json"})
     * @Rest\View(statusCode=204)
     *
     * @param CreatePasswordDto $createPasswordDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createPasswordAction(CreatePasswordDto $createPasswordDto, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($createPasswordDto, $errors), 400);
        } else {
            $personDetails = $this->passwordService->createPassword($createPasswordDto);
            unset($personDetails['email_detail']);
            return new Response($personDetails);
        }
    }

    /**
     * Validates a user's token.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Validate Token",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Password",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/validatetoken/{token}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param string $token
     * @return Response
     */
    public function validatetokenAction($token)
    {
        $personDetails = $this->passwordService->validateActivationLink($token);
        $response = array();
        $response['token_validation_status'] = true;
        $response['ebi_confidentiality_stmt'] = $personDetails->getOrganization()->getEbiConfidentialityStatement();
        return new Response($response);
    }

    /**
     * Gets a password reset email for a user who has forgotten their password.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Forgot Password",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Password",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/forgot", requirements={"_format"="json"})
     * @QueryParam(name="email", description="email address")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     * @return Response
     */
    public function forgotPasswordAction(ParamFetcher $paramFetcher)
    {
        $email = $paramFetcher->get('email');
        $personDetails = $this->emailPasswordService->sendEmailWithResetPasswordLink($email);
        unset($personDetails['email_detail']);
        return new Response($personDetails);
    }

    /**
     * Get Reminders for a user.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Reminder",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Password",
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
     * @Rest\Get("/{appId}",requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $appId
     * @return Response
     * @throws AccessDeniedException
     */
    public function getReminderAction($appId)
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->logger->error("Get Reminder Action- Not an authenticated user.");
            throw new AccessDeniedException();
        } else {
            $responseArray = $this->appointmentsService->getReminder($appId);
            return new Response($responseArray);
        }
    }
}
