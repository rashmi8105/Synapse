<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\SystemAlertService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Converter\SystemAlertDtoConverter;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\SystemAlertDto;

/**
 * Class AlertController
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/alerts")
 */
class SystemAlertController extends AbstractAuthController
{
    /**
     * @var SystemAlertDtoConverter
     *
     *      @DI\Inject(SystemAlertDtoConverter::SERVICE_KEY)
     */
    private $alertDtoConverter;

    /**
     * @var SystemAlertService
     *
     *      @DI\Inject(SystemAlertService::SERVICE_KEY)
     */
    private $systemAlertService;

    /**
     * Creates a new system alert.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Create System Alert",
     * input = "Synapse\RestBundle\Entity\SystemAlertDto",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "System Alerts",
     * statusCodes = {
     *                  201 = "System alert was updated. Representation of resource(s) was returned",
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
     * @param SystemAlertDto $systemAlertsDto
     * @param ConstraintViolationListInterface $validationErrors
     * @return Response|View
     */
    public function createSystemAlertAction(SystemAlertDto $systemAlertsDto, ConstraintViolationListInterface $validationErrors)
    {
        $this->ensureAdminAccess();
        if (count($validationErrors) > 0) {
            $errors = $this->convertValidationErrors($validationErrors);
            return View::create(new Response($systemAlertsDto, $errors), 400);
        } else {
            $systemAlert = $this->systemAlertService->createSystemAlert($systemAlertsDto);
            if (! $systemAlert instanceof Error) {
                $systemAlert = $this->alertDtoConverter->createAlertResponse($systemAlert);
            }
            return new Response($systemAlert);
        }
    }
}