<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Monolog\Logger;

/**
 * This job as part bulk faculty group upload
 * This is calling from GroupStudentBulkUploadService
 */
class ManageGroupStudentBulkUpload extends ContainerAwareJob
{

    const LOGGER = 'logger';

    const VALIDATOR_SERVICE = 'groupupload_validator_service'; // TODO need to change the information

    const VALUE = 'value';

    const ERRORS = 'errors';

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var int
     */
    private $validRows;

    /**
     * @var int
     */
    private $updatedRows;

    /**
     * @var int
     */
    private $unchangedRows;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $groupStudentRepository;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgStudentRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;


    /**
     * @var OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * @param $args
     * @return array
     */
    public function run($args)
    {
        $this->logger = $this->getContainer()->get(self::LOGGER);

        /*
         * These are the variables that will be run through the
         * arguments
         */
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];
        $duplicatedExternalIDs = explode(",", $args['duplicatedExternalIDs']);

        $this->errors = [];
        $this->validRows = 0;
        $this->updatedRows = 0;
        $this->unchangedRows = 0;


        /**
         * This will make the header information easier to view
         */

        $externalId = strtolower(GroupUploadConstants::EXTERNAL_ID);
        $firstname = strtolower(UploadConstant::FIRSTNAME);
        $lastname = strtolower(UploadConstant::LASTNAME);
        $email = strtolower(GroupUploadConstants::PRIMARY_EMAIL);

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $this->organizationService = $this->getContainer()->get('org_service');

        // group student
        $this->groupStudentRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupStudents');
        $this->orgStudentRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');
        $this->orgGroupRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $this->orgGroupTreeRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');


        // starting the adding students to db loop
        foreach ($creates as $id => $data) {

            $organization = $this->organizationService->find($organizationId);

            $updatedRowFlag = false;

            $nonGroupRowHeaders = array(
                $externalId,
                $firstname,
                $lastname,
                $email
            );

            $headerArray = array_keys($data);
            $addStudentTo = null;
            $clearStudentFromAllChildrenGroups = false;

            // $ParenGroupIds is an array that will contain only the groups that the upload
            // is suppose to mess with. They will be the external_ids

            // set flag back to false, messes with declaration
            // of the variable but is needed
            $fatalError = false;

            // check required information
            $requiredMissing = $this->checkRequired($data, $id);
            if ($requiredMissing) {
                // Error is handled within the checkRequired Function
                continue;
            }


            // This checks the external_id is in the database
            $person = $this->orgStudentRepository->getPersonStudentByExternalId($data[$externalId], $organizationId);

            // need to check the person exists within the db
            if (!$person) {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::EXTERNAL_ID,
                    self::VALUE => '',
                    self::ERRORS => [
                        "The " . GroupUploadConstants::EXTERNAL_ID . " '$data[$externalId]' does not exist in system"
                    ]
                ];
                continue;
            } else {
                /* check to see if the person's firstname lastname and email matches the person's externalId */
                $databasePersonFirstName = strtolower($person->getFirstname());
                $databasePersonLastName = strtolower($person->getLastname());
                $databasePersonEmail = $person->getUsername();
                /* make sure the upload has firstname, lastname, and email column and that the columns are filled with something */
                if (!empty($data[$firstname])) {
                    if (trim(strtolower($data[$firstname])) != $databasePersonFirstName) {
                        /* need to make sure the firstname matches the database */
                        $this->errors[$id][] = [
                            'name' => GroupUploadConstants::FIRSTNAME,
                            self::VALUE => '',
                            self::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s first name is not " . $data[$firstname] . "; $data[$externalId]'s first name is " . $person->getFirstname() // for capitalization
                            ]
                        ];
                        $fatalError = true;
                    }
                }
                if (isset($data[$lastname]) && !empty($data[$lastname])) {
                    if (trim(strtolower($data[$lastname])) != $databasePersonLastName) {
                        /* need to make sure the lastname matches the database */
                        $this->errors[$id][] = [
                            'name' => GroupUploadConstants::LASTNAME,
                            self::VALUE => '',
                            self::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s last name is not " . $data[$lastname] . "; $data[$externalId]'s last name is " . $person->getLastname() // for capitalization
                            ]
                        ];
                        $fatalError = true;
                    }
                }
                if (isset($data[$email]) && !empty($data[$email])) {
                    if (trim(strtolower($data[$email])) != trim(strtolower($databasePersonEmail))) {
                        /* need to make sure the email matches the database */
                        $this->errors[$id][] = [
                            'name' => GroupUploadConstants::PRIMARY_EMAIL,
                            self::VALUE => '',
                            self::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s email is not " . $data[$email] . "; $data[$externalId]'s email is " . $databasePersonEmail
                            ]
                        ];
                        $fatalError = true;
                    }
                }
                if (in_array($data[$externalId], $duplicatedExternalIDs)){
                    $this->errors[$id][] = [
                        'name' => GroupUploadConstants::EXTERNAL_ID,
                        self::VALUE => '',
                        self::ERRORS => [
                            "Warning: The External ID $data[$externalId] was duplicated in this file, rows were updated but results may not be as expected."
                        ]
                    ];
                }
            }


            foreach($headerArray as $header) {
                $addStudentTo = array();

                if (in_array($header, $nonGroupRowHeaders)) {
                    continue;
                }

                if (empty($data[$header])) {
                    continue;
                } else {
                    $childrenGroupArrayDirty = explode(';', $data[$header]);
                }

                $headerId = $this->orgGroupRepository->convertExternalIdOrGroupNameToId($header, $organizationId);
                if(is_array($headerId) && isset($headerId['error'])){
                    $this->errors[$id][] = [
                        'name' => $header,
                        self::VALUE => '',
                        self::ERRORS => [
                            $headerId['error']
                        ]
                    ];
                    continue;
                }

                // cleaning up the children groups
                $childrenGroupArray = array();
                $childrenGroupExternalId = array();
                foreach($childrenGroupArrayDirty as $dirtyChildrenGroup){

                    $dirtyChildrenGroup = trim($dirtyChildrenGroup);

                    // check to see if one of the groups sent want to clear out
                    // the student from all of the descendants of the header
                    if (strtolower($dirtyChildrenGroup) == "#clear") {
                        $clearStudentFromAllChildrenGroups = true;
                        continue;
                    } elseif(empty($dirtyChildrenGroup)){
                        // in case the user puts in ;; or ; ;
                        // in turn people with no external_id group cannot
                        // use the blank space as an externalid
                        continue;
                    }
                    else {
                        $cleanChildrenGroups = $this->orgGroupRepository->convertExternalIdOrGroupNameToId($dirtyChildrenGroup, $organizationId);

                        if(is_array($cleanChildrenGroups)&& isset($cleanChildrenGroups['error'])){
                            $this->errors[$id][] = [
                                'name' => $header . ' - ' . $dirtyChildrenGroup,
                                self::VALUE => '',
                                self::ERRORS => [
                                    $cleanChildrenGroups['error']
                                ]
                            ];
                        } else {
                            // This will be the ids of the uploaded groups
                            $childrenGroupArray[] = $cleanChildrenGroups;

                            // For reporting error purposing; in this case,
                            // the $cleanChildrenGroups should not be an array
                            $childrenGroupExternalId[$cleanChildrenGroups] = $dirtyChildrenGroup;
                        }
                    }
                }

                $allChildrenGroupsDirty =  $this->orgGroupTreeRepository->getEachGeneration($headerId, $organizationId);
                $allChildrenGroups = array();
                foreach($allChildrenGroupsDirty as $allChildrenGroupDirty){
                    $allChildrenGroups[] = $allChildrenGroupDirty['id'];
                }

                // get the person's id and all groups the person is in
                $personId = $person->getId();
                $groupsThePersonIsCurrentlyIn = $this->groupStudentRepository->getStudentGroupsDetails($personId, $organizationId);

                // This loop will take each group the person is in and see if each
                // group is within the parent tree
                $deleteStudentFrom = array();
                foreach ($groupsThePersonIsCurrentlyIn as $groupThePersonIsCurrentlyIn) {
                    if (in_array($groupThePersonIsCurrentlyIn['group_id'], $allChildrenGroups)) {
                        // This creates an array of groups the student is currently in and
                        // student should be deleted from, set up for unsetting student from
                        // will be associated array of ['group name' => 'group name', ...]
                        $deleteStudentFrom[$groupThePersonIsCurrentlyIn['group_id']] = $groupThePersonIsCurrentlyIn['group_id'];
                    }
                }

                foreach ($childrenGroupArray as $childrenGroup) {

                    // I need to check to make sure every child group is
                    // a child of said parent...
                    if ($this->orgGroupTreeRepository->isAncestor($childrenGroup, $headerId)) {
                        $addStudentTo[$childrenGroup] = $childrenGroup;

                        if (in_array($childrenGroup, $deleteStudentFrom)) {
                            // the reason for the associative array
                            // I don't want to delete a student only to
                            // put them back into the list
                            unset($deleteStudentFrom[$childrenGroup]);
                        }
                    } else {
                        // In this case the group is not a child of the parent child
                        $this->errors[$id][] = [
                            'name' => $header,
                            self::VALUE => '',
                            self::ERRORS => [
                                $childrenGroupExternalId[$childrenGroup] . " is not a child of " . $header
                            ]
                        ];
                    }

                }


                if ($fatalError) {
                    continue;
                }

                // Delete Student from these groups
                if ($clearStudentFromAllChildrenGroups) {
                    foreach ($deleteStudentFrom as $deleteStudentFromGroup) {

                        $allStudentsCheck = $this->orgGroupRepository->convertIdToExternalIdOrGroupName($deleteStudentFromGroup, $organizationId);
                        if(strtolower($allStudentsCheck) == strtolower('ALLSTUDENTS') ){
                            continue;
                        }

                        $groupStudent = $this->groupStudentRepository->findOneBy([
                            'organization' => $organizationId,
                            'orgGroup' => $deleteStudentFromGroup,
                            'person' => $person
                        ]);

                        if ($groupStudent) {
                            $this->groupStudentRepository->remove($groupStudent);
                            $updatedRowFlag = true;

                        } else {
                            // Student is not in the group, no need to delete them from it again...
                            // this only occurs during race time conditions where the use is trying
                            // to add and delete the same student from the same group repeatably.
                        }
                    }
                }

                // Add student into these groups
                    foreach ($addStudentTo as $addStudentToGroup) {

                        $allStudentsCheck = $this->orgGroupRepository->convertIdToExternalIdOrGroupName($addStudentToGroup, $organizationId);
                        if(strtolower($allStudentsCheck) == strtolower('ALLSTUDENTS') ){
                            $this->errors[$id][] = [
                                'name' => $header,
                                self::VALUE => '',
                                self::ERRORS => [
                                    "Cannot Add Student Into All Students Group"
                                ]
                            ];
                            continue;
                        }

                        $groupStudent = $this->groupStudentRepository->findOneBy([
                            'organization' => $organizationId,
                            'orgGroup' => $addStudentToGroup,
                            'person' => $personId
                        ]);

                        if ($groupStudent) {
                            // student is already in the group
                            // No need to throw an error, I guess...
                            continue;
                        }
                        $groupStudent = new OrgGroupStudents();
                        $groupStudent->setOrganization($organization);
                        $getGroup = $this->orgGroupRepository->getGroupDetails($organizationId, $addStudentToGroup);
                        $groupStudent->setOrgGroup($getGroup);
                        $groupStudent->setPerson($person);
                        $this->groupStudentRepository->persist($groupStudent, false);
                        $rbacManager = $this->getContainer()->get('tinyrbac.manager');
                        $rbacManager->refreshPermissionCache($person->getId());
                        $updatedRowFlag = true;

                        // added student to a group
                    }
                }
            if(!$fatalError) {
                $this->validRows++;
                if($updatedRowFlag){
                    $this->updatedRows++;
                } else {
                    $this->unchangedRows++;
                }

            }
            try {
                $this->orgGroupRepository->flush();
            } catch (\Exception $e) {

                // This normally gets thrown if there is a race time condition that occurs when
                // the user tries to delete the user from the same group in multiple rows.
                $this->errors[$id][] = [
                    'name' => 'ExternalId',
                    self::VALUE => '',
                    self::ERRORS => [
                        "One or more groups on this row were not updated for this student because another row in this file already updated the same groups for this student."
                    ]
                ];

                $this->logger->addWarning($e->getTraceAsString());
            }
            $this->orgGroupRepository->clear();
        }


        // after uploading each row, update valid rows and  flush/clear the data
        $uploadFileLogService->updateValidRowCount($uploadId, $this->validRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $this->updatedRows + $this->unchangedRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));


        // cache the information, unset the jobs
        $jobs = $cache->fetch("groupstudentbulk.upload.{$uploadId}.jobs");

        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("groupstudentbulk.upload.{$uploadId}.jobs", $jobs);

        $cache->save("groupstudentbulk.upload.{$uploadId}.job.{$jobNumber}.errors", $this->errors);

        return $this->errors;
    }


    /**
     * This function will return an array
     * with externalId in it... That is all
     *
     * @return array
     */

    public function getRequired(){
    return [

        strtolower(GroupUploadConstants::EXTERNAL_ID)
    ];
}

    /**
     * This is a default check, that will check each row has the
     * required columns.
     *
     * @param $data
     * @param $id
     * @return bool
     */
    public function checkRequired($data, $id){
    $requiredMissing = false;
    $requiredItems = $this->getRequired();
    foreach ($requiredItems as $item) {
        if (! array_key_exists($item, $data) || empty(trim($data[$item]))) {
            $this->errors[$id][] = [
                'name' => $item,
                self::VALUE => '',
                self::ERRORS => [
                    "{$item} is a required field"
                ]
            ];
            $requiredMissing = true;
        }
    }
    return $requiredMissing;
}


}