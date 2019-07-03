<?php
namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\OrganizationlangService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Entity\OrgCalcFlagsStudentReports;
use Synapse\ReportsBundle\Repository\OrgCalcFlagsStudentReportsRepository;

/**
 * @DI\Service("student_report_email_service")
 */
class StudentReportEmailService extends AbstractService
{
    const SERVICE_KEY = 'student_report_email_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var MapworksActionService
     */
    private $mapworksActionService;

    /**
     * @var OrganizationlangService
     */
    private $organizationLangService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var TokenService
     */
    private $tokenService;

    // Repositories
    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCalcFlagsStudentReportsRepository
     */
    private $orgCalcFlagsStudentReportsRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * StudentReportEmailService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * 
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //services
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->mapworksActionService = $this->container->get(MapworksActionService::SERVICE_KEY);
        $this->organizationLangService = $this->container->get(OrganizationlangService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->tokenService =  $this->container->get(TokenService::SERVICE_KEY);

        //repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCalcFlagsStudentReportsRepository = $this->repositoryResolver->getRepository(OrgCalcFlagsStudentReportsRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }


    /**
     * Sends Student Report Emails
     *
     * @param int $orgCalcFlagsStudentReportsId
     * @param int $studentId
     * @param bool $isCompletionEmail
     * @return array
     */
    public function sendStudentReportEmails($orgCalcFlagsStudentReportsId, $studentId, $isCompletionEmail)
    {
        try {
            $orgCalcFlagsStudentReportsEntity = $this->orgCalcFlagsStudentReportsRepository->find($orgCalcFlagsStudentReportsId, new SynapseValidationException("Could not find Report for orgCalcFlagsStudentReportsId: $orgCalcFlagsStudentReportsId"));
            $person = $this->personRepository->find($studentId, new SynapseValidationException("Could not find Person for studentId: $studentId"));

            $organization = $orgCalcFlagsStudentReportsEntity->getOrganization();
            if (!$organization) {
                throw new SynapseValidationException("Could not find Organization");
            }
            $organizationId = $organization->getId();

            $tokenValues = $this->mapStudentReportToTokenVariables($person, $organizationId, $orgCalcFlagsStudentReportsEntity);

            $studentCommunicationSent = $this->mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'create', 'student', 'student_report', $studentId, null, null, null, $tokenValues);
            if ($studentCommunicationSent) {
                //if the email is a completion email, set completion flag, otherwise partial email flag
                if ($isCompletionEmail) {
                    $orgCalcFlagsStudentReportsEntity->setCompletionEmailSent(1);
                } else {
                    $orgCalcFlagsStudentReportsEntity->setInProgressEmailSent(1);
                }
                $this->orgCalcFlagsStudentReportsRepository->flush();
                $this->orgCalcFlagsStudentReportsRepository->clear();
                $responseArray['results'] = 'Email successfully sent.';
                $responseArray['success'] = true;
            } else {
                $responseArray['results'] = 'Did not successfully send email. Will retry later.';
                $responseArray['success'] = false;
            }
        } catch (SynapseValidationException $e) {
            $responseArray['results'] = $e->getUserMessage();
            $responseArray['success'] = false;
        }
        return $responseArray;
    }

    public function flushSpooler()
    {
        $mailer = $this->container->get('mailer');
        $transport = $mailer->getTransport();
        if (! $transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }
        $spool = $transport->getSpool();
        if (! $spool instanceof \Swift_MemorySpool) {
            return;
        }

        $spool->flushQueue($this->container->get('swiftmailer.transport.real'));
    }

    /**
     * Maps student report to token variables
     *
     * @param Person $student
     * @param int $organizationId
     * @param OrgCalcFlagsStudentReports $orgCalcFlagsStudentReportsEntity
     * @return array
     * @throws SynapseValidationException
     */
    public function mapStudentReportToTokenVariables($student, $organizationId, $orgCalcFlagsStudentReportsEntity)
    {
        $tokenValues = $this->mapworksActionService->getTokenVariablesFromPerson('student', $student);
        $currentAcademicYearDetail = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        $currentAcademicYear = $currentAcademicYearDetail['year_id'];

        $primaryCoordinatorPersonObject = $this->personService->getFirstPrimaryCoordinatorPerson($organizationId);
        $tokenValues = array_merge($tokenValues, $this->mapworksActionService->getTokenVariablesFromPerson('coordinator', $primaryCoordinatorPersonObject));

        $fileToken = null;
        $fileToken = str_replace('.pdf', "", $orgCalcFlagsStudentReportsEntity->getFileName());

        $systemApiUrlEbiConfigEntry = $this->ebiConfigRepository->findOneBy(['key' => 'System_API_URL'], new SynapseValidationException('Unable to locate ebi_config entry for System_API_URL.'));
        $apiUrl = $systemApiUrlEbiConfigEntry->getValue();
        $reportURL = $apiUrl . '/api/v1/storage/student_reports_uploads/' . $fileToken;
        $tokenValues['$$pdf_report$$'] = $reportURL;
        $tokenValues['$$academicyear$$'] = $currentAcademicYear;
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        if ($systemUrl) {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        } else {
            $tokenValues['$$Skyfactor_Mapworks_logo$$'] = '';
        }
        return $tokenValues;
    }
}