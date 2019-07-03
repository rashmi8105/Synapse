<?php
namespace Synapse\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Synapse\CoreBundle\Util\Constants\PdfDetailsConstant;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PdfBundle\Service\Impl\PdfDetailsService;
use Synapse\RestBundle\Entity\Response;

/**
 *
 * @package Synapse\RestBundle\Controller
 *         
 *          @Rest\Prefix("/pdf")
 */
class PdfController extends AbstractAuthController
{

    /**
     * @var PdfDetailsService
     *
     *      @DI\Inject(PdfDetailsService::SERVICE_KEY)
     */
    private $pdfService;

    /**
     * Gets the PDF details for a faculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Faculty Upload PDF Details",
     * section = "PDF",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getFacultyUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getFacultyUploadPdfDetails();

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=faculty.pdf");

        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }

    }

    /**
     * Gets the PDF details for a student upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Student Upload PDF Details",
     * section = "Permission Set",
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
     * @Rest\Get("/students/{orgId}", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     * @param int $orgId
     */
    public function getStudentUploadPdfDetailsAction($orgId)
    {
        $result = $this->pdfService->getStudentUploadPdfDetails($orgId);
        
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=student.pdf");
        
        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a course upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/courses", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getCourseUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getCourseUploadPdfDetails();

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=course.pdf");

        print $this->get('knp_snappy.pdf')->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a course-faculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course-Faculty Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/courses/faculty", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getCourseFacultyUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getCourseFacultyUploadPdfDetails();
        
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=course-faculty.pdf");

        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a course-students upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Course-Students Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/courses/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getCourseStudentsUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getCourseStudentsUploadPdfDetails();
        
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=course-students.pdf");
        
        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a academic-update upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Academic-Update Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/academicupdates", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getAcademicUpdateUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getAcademicUpdateUploadPdfDetails();

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=academic-update.pdf");
        
        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a sub-group upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Sub-Group Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/subgroups", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getSubGroupsUploadPdfDetailsAction()
    {
        $result = $this->pdfService->getSubGroupsUploadPdfDetails();

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=sub-group.pdf");

        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a group-faculty upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Group-Faculty Upload PDF Details",
     * section = "Permission Set",
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
     * @Rest\Get("/groups/faculty", requirements={"_format"="json"})
     * @QueryParam(name="orgid", requirements="\d+", strict=true, description="org id")
     * @Rest\View(statusCode=200)
     *
     * @param ParamFetcher $paramFetcher
     */
    public function getGroupsFacultyUploadPdfDetailsAction(ParamFetcher $paramFetcher)
    {
        $organizationId =  (int) $paramFetcher->get('orgid');
        $result = $this->pdfService->getGroupsFacultyUploadPdfDetails($organizationId);

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=group-faculty.pdf");

        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }

    /**
     * Gets the PDF details for a group-students upload.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Get Group-Students Upload PDF Details",
     * section = "Permission Set",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @Rest\Get("/groups/students", requirements={"_format"="json"})
     * @Rest\View(statusCode=200)
     *
     */
    public function getGroupStudentsUploadPdfDetailsAction()
    {
        $organizationId = $this->getLoggedInUserOrganizationId();
        $result = $this->pdfService->getGroupStudentsUploadPdfDetails($organizationId);

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=group-students.pdf");

        print $this->get("knp_snappy.pdf")->getOutputFromHtml($result);

        if (extension_loaded('newrelic')) {
            // NewRelic has a bug that causes it to interfere with and send no content for PDFs that
            // are shorter in length if newrelic.browser_monitoring.auto_instrument is set to true
            // in newrelic.ini. The newrelic_disable_autorum() doesn't solve the problem. So, we just
            // exit here if newrelic is being used.
            exit;
        }
    }
}