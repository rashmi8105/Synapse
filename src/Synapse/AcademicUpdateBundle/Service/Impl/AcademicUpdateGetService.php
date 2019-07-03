<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateHistoryDto;
use Synapse\AcademicUpdateBundle\EntityDto\StudentHistoryDetailsDto;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\PersonBundle\Service\PersonService;

/**
 * @DI\Service("academicupdateget_service")
 */
class AcademicUpdateGetService extends AcademicUpdateServiceHelper
{

    const SERVICE_KEY = 'academicupdateget_service';


    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var OrganizationService
     */
    public $organizationService;

    /**
     * @var PersonService
     */
    public $personService;


    // Repositories

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * AcademicUpdateGetService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *            })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger, $container);

        // Scaffolding
        $this->container = $container;

        // Services
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);

        // Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Get the student's academic update history for the specified course ID.
     *
     * @param int $organizationId
     * @param int $courseId
     * @param int $studentId
     * @return AcademicUpdateHistoryDto
     */
    public function getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId)
    {
        //Get the course object for the requested course
        $course = $this->orgCoursesRepository->findOneBy([
            'id' => $courseId,
            'organization' => $organizationId
        ]);

        //get the current academic year ID for the organization, and get the student's academic update history for the specified course.
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
        $academicUpdateHistory = $this->academicUpdateRepository->getAcademicUpdateStudentHistory($organizationId, $courseId, $studentId, $orgAcademicYearId);

        //Format the student academic update history
        $academicUpdateHistoryDto = new AcademicUpdateHistoryDto();
        if (count($academicUpdateHistory)) {
            $academicUpdateHistoryDto->setOrganizationId($organizationId);
            $academicUpdateHistoryDto->setStudentId($studentId);
            $academicUpdateHistoryDto->setCourseId($courseId);
            $academicUpdateHistoryDto->setCourseName($course->getCourseName());

            $studentAcademicUpdateHistoryArray = array();
            foreach ($academicUpdateHistory as $academicUpdate) {
                $updatedDate = $academicUpdate['update_date'];

                if (!empty($updatedDate)) {
                    $updatedDate = $this->dateUtilityService->adjustDateTimeToOrganizationTimezone($organizationId, new \DateTime($updatedDate));
                }

                $failureRiskLevel = (!empty($academicUpdate['failure_risk_level'])) ? ucfirst($academicUpdate['failure_risk_level']) : null;
                $inProgressGrade = (!empty($academicUpdate['grade'])) ? $academicUpdate['grade'] : null;
                $absences = ($academicUpdate['absence'] == "0" || !empty($academicUpdate['absence'])) ? $academicUpdate['absence'] : null;
                $comments = (!empty($academicUpdate['comment'])) ? $academicUpdate['comment'] : null;
                $referForAssistance = (!empty($academicUpdate['refer_for_assistance'])) ? $academicUpdate['refer_for_assistance'] : null;
                $sendToStudent = (!empty($academicUpdate['send_to_student'])) ? $academicUpdate['send_to_student'] : null;

                $studentHistoryDetailsDto = new StudentHistoryDetailsDto();
                $studentHistoryDetailsDto->setDate($updatedDate);
                $studentHistoryDetailsDto->setFailureRisk($failureRiskLevel);
                $studentHistoryDetailsDto->setGrade($inProgressGrade);
                $studentHistoryDetailsDto->setAbsences($absences);
                $studentHistoryDetailsDto->setComments($comments);
                $studentHistoryDetailsDto->setAcademicAssistRefer($referForAssistance);
                $studentHistoryDetailsDto->setStudentSend($sendToStudent);
                $studentAcademicUpdateHistoryArray[] = $studentHistoryDetailsDto;
            }
            $academicUpdateHistoryDto->setAcademicUpdateHistory($studentAcademicUpdateHistoryArray);
        }

        return $academicUpdateHistoryDto;
    }

    /**
     * returns the count of academic updates
     *
     * @param int $loggedInUserId
     * @return array
     */
    public function getAcademicUpdateUploadCount($loggedInUserId)
    {
        $loggedInPerson = $this->personRepository->find($loggedInUserId);
        $organization = $loggedInPerson->getOrganization();

        $totalCount = $this->academicUpdateRepository->getAcademicUpdateUploadCount($organization);
        return [
            'total_upload_count' => (int)$totalCount
        ];
    }
}