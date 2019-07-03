<?php
namespace Synapse\StaticListBundle\Job;
 
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\StaticListBundle\Service\Impl\StaticListStudentsService;
use Synapse\UploadBundle\Job\CsvJob;

class StaticListCSVJob extends CsvJob
{
    public function run($args)
    {
        $loggedUserId = $args['loggedUserId'];
        $currentDateTime = $args['currentDateTime'];
        $staticListId = $args['staticListId'];
        $organizationId = $args['orgId'];

        $container = $this->getContainer();
        $alertService = $container->get(AlertNotificationsService::SERVICE_KEY);
        $csvUtilityService = $container->get(CSVUtilityService::SERVICE_KEY);
        $dataProcessingUtilityService = $container->get(DataProcessingUtilityService::SERVICE_KEY);
        $staticListStudentsService = $container->get(StaticListStudentsService::SERVICE_KEY);

        $personRepository = $container->get('repository_resolver')->getRepository(PersonRepository::REPOSITORY_KEY);

        $person = $personRepository->find($loggedUserId);

        $personDTO = $staticListStudentsService->viewStaticListDetails($staticListId, $person, '', '', '', false, true);
        if ($personDTO) {
            $studentList = $personDTO->getTotalStudentsList();
        } else {
            $studentList = [];
        }

        $studentListsArray = $dataProcessingUtilityService->serializeObjectToArray($studentList);

        // Array defined to ignore column data and change the column name

        $csvHeaders = array(
            'externalId' => 'External Id',
            'studentFirstName' => 'Student First Name',
            'studentLastName' => 'Student Last Name',
            'studentRiskStatus' => 'Student Risk Status',
            'lastActivity' => 'Last Activity',
            'studentClasslevel' => 'Class Standing',
            'studentLogins' => 'Activities Logged',
            'studentIntentToLeaveText' => 'Student Intent To Leave',
            'primaryEmail' => 'Primary Email'
        );

        $fileName = $organizationId . "-staticList_" . $currentDateTime . ".csv";
        $filePath = SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY. '/';


        // CSV utility service function: Generates the CSV and stores it to defined path
        $csvUtilityService->generateCSV($filePath, $fileName, $studentListsArray, $csvHeaders);

        // Create Alert Notification after CSV generation

        $alertService->createNotification('Activity_Download', 'Your Static List download has completed', $person, NULL, NULL, NULL, SynapseConstant::S3_CSV_EXPORT_DIRECTORY. '/' . $fileName, NULL, NULL, NULL, TRUE);
    }
}