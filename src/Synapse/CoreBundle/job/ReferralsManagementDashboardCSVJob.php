<?php
namespace Synapse\CoreBundle\job;

use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\DashboardReferralService;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\UploadBundle\Job\CsvJob;

class ReferralsManagementDashboardCSVJob extends CsvJob
{
    public function run($args)
    {
        $loggedInPerson = $args['loggedInUser'];
        $currentDateTime = $args['currentDateTime'];
        $status = $args['status'];
        $filter = $args['filter'];
        $offset = $args['offset'];
        $pageNo = $args['pageNo'];
        $organizationId = $args['orgId'];

        // Calling service class method to get complete data set
        $personRepository = $this->getContainer()->get('repository_resolver')->getRepository(PersonRepository::REPOSITORY_KEY);
        $alertService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $CSVUtilityService = $this->getContainer()->get(CSVUtilityService::SERVICE_KEY);
        $dashboardReferralService = $this->getContainer()->get(DashboardReferralService::SERVICE_KEY);
        $utilServiceHelper = $this->getContainer()->get(UtilServiceHelper::SERVICE_KEY);

        $personObject = $personRepository->find($loggedInPerson);
        $orgTimeZone = $personObject->getOrganization()->getTimezone();
        $referralList = $dashboardReferralService->getReferralDetailsBasedFilters($personObject, $status, $filter, $offset, $pageNo, $data = '', $sortBy = '', $isCSV = false, $isJob = true);
        $fileName = $organizationId . "-" . "referrals_" . $currentDateTime . ".csv";

        $referralData = [];
        if (count($referralList) > 0) {
            foreach ($referralList as $data) {
                $referralDate = $utilServiceHelper->getDateByTimezone($orgTimeZone, 'm/d/y', false, $data['referral_date']);
                $data['referral_date'] = $referralDate;
                //Setting status from referral flag                
                $data['status'] = ($data['status'] == 'O') ? 'Open' : 'Close';
                $referralData[] = $data;
            }
        }

        $csvHeaders = array(
            'student_first_name' => 'Student First Name',
            'student_last_name' => 'Student Last Name',
            'student_email' => 'Student Email',
            'student_external_id' => 'Student External Id',
            'reason_text' => 'Reason',
            'referral_date' => 'Created On',
            'created_by_first_name' => 'Created By First Name',
            'created_by_last_name' => 'Created By Last Name',
            'created_by_email' => 'Created By Email',
            'created_by_external_id' => 'Created By External Id',
            'status' => 'Status',
            'assigned_to_name' => 'Assigned To Name'
        );

        // CsvUtilityService is used to generate csv
        $completeFilePath =  SynapseConstant::S3_ROOT . SynapseConstant::S3_CSV_EXPORT_DIRECTORY."/";
        $completeFilePathForNotification = SynapseConstant::S3_CSV_EXPORT_DIRECTORY . "/$fileName";
        $CSVUtilityService->generateCSV($completeFilePath, $fileName, $referralData, $csvHeaders);
        
        // Create Alert Notification after CSV generation
        $alertService->createNotification('Activity_Download', 'Your referral download has completed', $personObject, NULL, NULL, NULL, $completeFilePathForNotification, NULL, NULL, NULL, TRUE);
    }
}