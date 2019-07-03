<?php
namespace Synapse\ReportsBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Synapse\ReportsBundle\Service\Impl\ReportsService;
use Synapse\RestBundle\Controller\AbstractAuthController;
use Synapse\RestBundle\Entity\Response;

/**
 * Class ReportsController
 *
 * @package Synapse\ReportsBundle\Controller
 *         
 *          @Rest\Prefix("/studentreport")
 *         
 */
class StudentReportController extends AbstractAuthController
{
    /**
     * @var ReportsService
     *     
     *      @DI\Inject(ReportsService::SERVICE_KEY)
     */
    private $reportsService;
    
    /**
     * Generates a PDF student report.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Generate Student Report",
     * output = "Synapse\RestBundle\Entity\Response",
     * section = "Reports",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of resource(s) returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/generate", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @return Response
     */
    
    public function generateStudentReportAction()
    {
        $report = $this->reportsService->generateStudentReport(); 
        return new Response($report);                      
    }
}