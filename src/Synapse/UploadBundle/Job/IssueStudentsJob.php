<?php
namespace Synapse\UploadBundle\Job;

use Synapse\UploadBundle\Job\CsvJob;
use SplFileObject;
use Synapse\CoreBundle\Util\UploadHelper;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\SurveyBundle\Util\Constants\SurveyConstant;

class IssueStudentsJob extends CsvJob
{
    public function run($args)
    {
        $loggedInUser = $args['personId'];
        $currentDateTime = $args['currentDateTime'];
        $issueId = $args['issueId'];
        $orgId = $args['orgId'];
        
        /**
         *  Calling service class method to get complete data set
         */
        $issueStudents = $this->getContainer()->get('issue_service')->getStudentsByIssue($issueId,$loggedInUser,$orgId,'','','','', true);
        $fileName = $orgId."-top_issues_".$currentDateTime.".csv";
        
        /**
         *  Array defined to ignore column data and change the column name
         */
        $options = array(
            'columnNamesMap' => array(
                'external_id' => array(
                    'display_name' => 'External Id'
                ),
                'login_cnt' => array(
                    'display_name' => 'Activities Logged'
                )
            ),
            'ignored' => array('id','intent_imagename','risk_imagename','status','intent_color','risk_color','risk_flag','intent_flag')
        );
        
        /**
         *  UtilServiceHelper function: Generates the CSV and stores it to defined path
         */
        $utilServiceHelper = $this->getContainer()->get('util_service');
        $utilServiceHelper->generateCSV($issueStudents, $fileName, $options);
        
        $completeFilePath = ReportsConstants::EXPORT_CSV."/$fileName";
        
        /**
         *  Create Alert Notification after CSV generation
         */
        $personRepository = $this->getContainer()->get('repository_resolver')->getRepository(PersonConstant::PERSON_REPO);
        
        $personObj = $personRepository->find($loggedInUser);
        $alertService = $this->getContainer()->get("alertNotifications_service");
        $alertService->createNotification(ReportsConstants::ACTIVITY_DOWNLOAD, SurveyConstant::ISSUE_STUDENTS_DESCRIPTION, $personObj, NULL, NULL, NULL, $completeFilePath, NULL, NULL, NULL, TRUE);
    }
}