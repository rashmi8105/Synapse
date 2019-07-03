<?php
namespace Synapse\AcademicUpdateBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Config\Definition\Exception\Exception;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;

class CreateAcademicUpdateHistoryJob extends ContainerAwareJob
{
    const JOB_KEY = 'CreateAcademicUpdateHistoryJob';

    /**
     * @var AcademicUpdateService
     */
    private $academicUpdateService;

    /**
     * @var JobService
     */
    private $jobService;

    public function __construct()
    {
        $this->queue = 'academicupdate';
    }

    public function run($args)
    {
        $this->jobService = $this->getContainer()->get(JobService::SERVICE_KEY);
        $this->academicUpdateService = $this->getContainer()->get(AcademicUpdateService::SERVICE_KEY);
        $logger = $this->getContainer()->get(SynapseConstant::LOGGER_KEY);

        $loggedInUserId = $args['loggedInUserId'];
        $organizationId = $args['organizationId'];
        $coursesStudentsAcademicUpdateDTO = unserialize($args['coursesStudentsAcademicUpdateDTO']);
        $isAdhoc = $args['isAdhoc'];
        $isUpload = $args['isUpload'];
        $updateType = $args['updateType'];
        $jobNumber = $args['jobNumber'];


        try {
            $this->jobService->updateJobStatus($organizationId, self::JOB_KEY, SynapseConstant::JOB_STATUS_INPROGRESS, $jobNumber, $loggedInUserId);
            $coursesStudentsAcademicUpdateDTO = $this->academicUpdateService->prepareCoursesStudentAdhocAcademicUpdatesDTOForAcademicUpdateJob($coursesStudentsAcademicUpdateDTO);
            $this->academicUpdateService->createAcademicUpdateHistory($coursesStudentsAcademicUpdateDTO, $loggedInUserId, $updateType, $isUpload, $isAdhoc);
            $this->jobService->updateJobStatus($organizationId, self::JOB_KEY, SynapseConstant::JOB_STATUS_SUCCESS, $jobNumber, $loggedInUserId);

        } catch (\Exception $exception) {

            $this->jobService->updateJobStatus($organizationId, self::JOB_KEY, SynapseConstant::JOB_STATUS_FAILURE, $jobNumber, $loggedInUserId, $exception->getMessage());
            $message = $exception->getMessage();
            $stackTrace = $exception->getTraceAsString();
            $logger->error("CreateAcademicUpdateHistoryJob Failed - CoursesStudentsAcademicUpdateDTO: ".$args['coursesStudentsAcademicUpdateDTO'].", organizationId: $organizationId, loggedInUserId: $loggedInUserId Error Message: $message Stack Trace: $stackTrace");
            //TODO:: Create a notification for this failure.
        }

    }
}