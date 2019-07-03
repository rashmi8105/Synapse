<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\CoreBundle\Entity\OrgGroupFaculty;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupTreeRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * This job as part bulk faculty groupObject upload
 * This is calling from GroupFacultyBulkUploadService
 */
class AddFacultyGroup extends ContainerAwareJob
{

    // Public variables

    /**
     * @var array
     */
    public $errors = [];


    // Private variables

    /**
     * @var int
     */
    private $createdRows;

    /**
     * @var int
     */
    private $deletedRows;

    /**
     * @var int
     */
    private $unchangedRows;

    /**
     * @var int
     */
    private $updatedRows;

    /**
     * @var int
     */
    private $validRows;


    // Repositories

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgGroupTreeRepository
     */
    private $orgGroupTreeRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;


    /**
     * @param array $args - associative array of arguments
     * @return array - array of errors
     */
    public function run($args)
    {
        $logger = $this->getContainer()->get('logger');

        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];
        $this->errors = [];
        $this->validRows = 0;
        $this->createdRows = 0;
        $this->deletedRows = 0;
        $this->updatedRows = 0;
        $this->unchangedRows = 0;

        $externalId = strtolower(GroupUploadConstants::EXTERNAL_ID);
        $firstname = strtolower(UploadConstant::FIRSTNAME);
        $lastname = strtolower(UploadConstant::LASTNAME);
        $email = strtolower(GroupUploadConstants::PRIMARY_EMAIL);
        $groupname = strtolower(GroupUploadConstants::GROUP_NAME);

        $fullPathName = strtolower('FullPathNames');
        $fullPathGroupIds = strtolower('FullPathGroupIDs');

        // Scaffolding

        $cache = $this->getContainer()->get('synapse_redis_cache');
        $repositoryResolver = $this->getContainer()->get('repository_resolver');

        // Services

        $orgService = $this->getContainer()->get('org_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $groupUploadValidatorService = $this->getContainer()->get('groupupload_validator_service');


        // Repositories

        $this->orgGroupRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
        $this->orgGroupFacultyRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $this->orgGroupTreeRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupTree');
        $this->orgPersonFacultyRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonFaculty');

        $organization = $orgService->find($organizationId);
        foreach ($creates as $id => $data) {

            $fatalError = false;
            $logger->info(GroupUploadConstants::MODULE_NAME . __FILE__ . __FUNCTION__ . " :  " . $data[strtolower(GroupUploadConstants::GROUP_ID)]);

            $requiredMissing = $this->checkRequired($id, $data);
            if ($requiredMissing) {
                continue;
            }

            $person = $this->orgPersonFacultyRepository->getPersonFacultByExternalId($data[strtolower(GroupUploadConstants::EXTERNAL_ID)], $organizationId);
            if (!$person) {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::EXTERNAL_ID,
                    'value' => '',
                    'errors' => [
                        "External ID does not exist"
                    ]
                ];
                continue;
            } else {
                /* check to see if the person's firstname lastname and email matches the person's externalId */
                $databasePersonFirstName = ($person->getFirstname());
                $databasePersonLastName = ($person->getLastname());
                $databasePersonEmail = $person->getUsername();
                /* make sure that if the upload has firstname, lastname, and email columns that are filled, the columns are filled correctly */
                if (!empty($data[$firstname])) {
                    if (trim(strtolower($data[$firstname])) != trim(strtolower($databasePersonFirstName))) {
                        /* need to make sure the firstname matches the database */
                        $this->errors[$id][] = [
                            'name' => GroupUploadConstants::FIRSTNAME,
                            UploadConstant::VALUE => '',
                            UploadConstant::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s first name is not " . $data[$firstname] . "; $data[$externalId]'s first name is " . $databasePersonFirstName // for capitalization
                            ]
                        ];
                        $fatalError = true;
                    }
                }
                if (isset($data[$lastname]) && !empty($data[$lastname])) {
                    if (trim(strtolower($data[$lastname])) != trim(strtolower($databasePersonLastName))) {
                        /* need to make sure the lastname matches the database */
                        $this->errors[$id][] = [
                            'name' => GroupUploadConstants::LASTNAME,
                            UploadConstant::VALUE => '',
                            UploadConstant::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s last name is not " . $data[$lastname] . "; $data[$externalId]'s last name is " . $databasePersonLastName // for capitalization
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
                            UploadConstant::VALUE => '',
                            UploadConstant::ERRORS => [
                                GroupUploadConstants::EXTERNAL_ID . " $data[$externalId]'s email is not " . $data[$email] . "; $data[$externalId]'s email is " . $databasePersonEmail
                            ]
                        ];
                        $fatalError = true;
                    }
                }
            }

            $group = $groupUploadValidatorService->validateContents(GroupUploadConstants::GROUP_ID, $data[strtolower(GroupUploadConstants::GROUP_ID)], array(
                'class' => 'SynapseCoreBundle:OrgGroup',
                'keys' => array(
                    'externalId' => $data[strtolower(GroupUploadConstants::GROUP_ID)],
                    'organization' => $organizationId
                )
            ), "Group ID does not exist");


            if (!$group) {
                // Only 1 row in CSV, $this->errors is not set.
                $errorsTrack = $groupUploadValidatorService->getErrors();
                $groupUploadValidatorService->clearErrors();
                $this->errors[$id] = $errorsTrack;
                continue;
            }


            $this->checkAncestryChain($data, $fullPathGroupIds, $organizationId, $group, $id, $fatalError, 'externalid', '|');
            $this->checkAncestryChain($data, $fullPathName, $organizationId, $group, $id, $fatalError, 'groupname', '|');
            if (isset($data[$groupname]) && !empty($data[$groupname])) {
                $fatalError = $this->checkGroupName($group, $data[strtolower('groupname')], $organizationId, $id, $fatalError);
            }
            if ($fatalError) {
                continue;
            }

            $groupFaculty = $this->orgGroupFacultyRepository->findOneBy([
                'organization' => $organizationId,
                'orgGroup' => $group,
                'person' => $person
            ]);

            if (strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == strtolower(GroupUploadConstants::REMOVE) && (!$groupFaculty)) {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::REMOVE,
                    'value' => '',
                    'errors' => [
                        "Group ID does not exist"
                    ]
                ];
                continue;
            }

            // Added validation for Invisible field
            $isInvisibleInputString = $data[strtolower(GroupUploadConstants::INVISIBLE)];

            if ($isInvisibleInputString === "0" || (empty(trim($isInvisibleInputString)) && (!$groupFaculty))) {
                $data[strtolower(GroupUploadConstants::INVISIBLE)] = 0;
            } else if (empty(trim($isInvisibleInputString))) {
                $data[strtolower(GroupUploadConstants::INVISIBLE)] = $groupFaculty->getIsInvisible();
            } elseif ($isInvisibleInputString !== "1") {
                $this->errors[$id][] = [
                    'name' => GroupUploadConstants::INVISIBLE,
                    'value' => '',
                    'errors' => [
                        "Invisible can only be 1 or 0"
                    ]
                ];
                continue;
            }

            /**
             * Permission Set
             */
            $permissionset = null;
            if (strtolower($data[strtolower(GroupUploadConstants::PERMISSION_SET)]) != strtolower(GroupUploadConstants::CLEAR) && !empty($data[strtolower(GroupUploadConstants::PERMISSION_SET)])) {
                $permissionset = $groupUploadValidatorService->validateContents(GroupUploadConstants::PERMISSION_SET, $data[strtolower(GroupUploadConstants::PERMISSION_SET)], array(
                    'class' => 'SynapseCoreBundle:OrgPermissionset',
                    'keys' => array(
                        'permissionsetName' => $data[strtolower(GroupUploadConstants::PERMISSION_SET)],
                        'organization' => $organizationId
                    )
                ));
            }

            $errorsTrack = $groupUploadValidatorService->getErrors();

            $groupUploadValidatorService->clearErrors();

            if (sizeof($errorsTrack) > 0) {
                $this->errors[$id] = $errorsTrack;
            } else {
                $this->validRows++;
                $this->manageGroupFaculty($data, $groupFaculty, $permissionset, $organization, $group, $person, $id);
            }
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $this->validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $this->createdRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $this->updatedRows + $this->deletedRows + $this->unchangedRows);
        $this->orgGroupRepository->flush();
        $this->orgGroupRepository->clear();
        $jobs = $cache->fetch("groupfacultybulk.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("groupfacultybulk.upload.{$uploadId}.jobs", $jobs);

        $cache->save("groupfacultybulk.upload.{$uploadId}.job.{$jobNumber}.errors", $this->errors);

        return $this->errors;
    }

    /**
     * Returns true if a required field in $data is missing, false otherwise.
     * Also sets upload errors on missing items.
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function checkRequired($id, $data)
    {
        $requiredMissing = false;
        $requiredItems = $this->getRequired();
        foreach ($requiredItems as $item) {
            if (!array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                $this->errors[$id][] = [
                    'name' => $item,
                    'value' => '',
                    'errors' => [
                        "{$item} is a required field"
                    ]
                ];
                $requiredMissing = true;
            }
        }
        return $requiredMissing;
    }

    /**
     * @return array - array of required fields.
     */
    public function getRequired()
    {
        return [
            GroupUploadConstants::GROUP_ID,
            GroupUploadConstants::EXTERNAL_ID
        ];
    }

    /**
     * This will check the Ancestry Chain for
     * the two rows: FullPathNames and FullPathGroupIDs
     *
     * @param array $data => the row of information
     * @param string $headerName => FullPathNames or FullPathGroupIDs
     * @param int $organizationId => int
     * @param OrgGroup $groupObject => the group the to compare the FullPathNames and FullPathGroupIDs chain against
     * @param string $rowId => row id for error reporting
     * @param bool $fatalError => short circuit flag for the upload
     * @param string $groupNameOrExternalId => 'groupname' for FullPathNames or 'externalid' for FullPathGroupIDs
     * @param string $delimiter => to explode on
     * @return bool
     */
    public function checkAncestryChain($data, $headerName, $organizationId, $groupObject, $rowId, $fatalError, $groupNameOrExternalId = 'groupname', $delimiter = '|')
    {

        // this will check to see if the given file path is actually a path
        // to the given group
        if (isset($data[$headerName]) && !empty($data[$headerName])) {
            $dirtyGroupInformationArray = explode($delimiter, $data[$headerName]);
            $groupIdsArray = array();

            // if there is an error within the convert function
            // do not continue to relationships
            $doAllGroupsExist = true;
            foreach ($dirtyGroupInformationArray as $dirtyGroupId) {
                $cleanGroupInformation = trim($dirtyGroupId);
                $cleanGroupId = $this->orgGroupRepository->convertExternalIdOrGroupNameToId($cleanGroupInformation, $organizationId, $groupNameOrExternalId);


                // if groupID does not map to an system id,
                // it will return an array
                if (is_array($cleanGroupId)) {
                    $fatalError = true;
                    $doAllGroupsExist = false;
                    $this->errors[$rowId][] = [
                        'name' => $headerName,
                        'value' => '',
                        'errors' => [
                            "Warning: " . $cleanGroupId['error']
                        ]
                    ];
                }
                $groupIdsArray[] = $cleanGroupId;
            }

            // short circuit; we are
            // done error checking
            if (!$doAllGroupsExist) {
                return $fatalError;
            }
            $count = 0;

            if ($groupIdsArray[$count] != null) {

                while ($groupIdsArray[$count + 1] != null) {
                    $isAncestorOf = $this->orgGroupTreeRepository->isAncestor($groupIdsArray[$count + 1], $groupIdsArray[$count]);
                    $isRelatedToInsert = $this->orgGroupTreeRepository->isAncestor($groupObject->getId(), $groupIdsArray[$count]);
                    if (!$isRelatedToInsert) {
                        $fatalError = true;
                        $this->errors[$rowId][] = [
                            'name' => $headerName,
                            'value' => '',
                            'errors' => [
                                "Warning: " . trim($dirtyGroupInformationArray[$count]) . ' is not an ancestor of ' . $data[strtolower(GroupUploadConstants::GROUP_ID)]
                            ]
                        ];
                    }

                    if (!$isAncestorOf) {
                        $fatalError = true;
                        $this->errors[$rowId][] = [
                            'name' => $headerName,
                            'value' => '',
                            'errors' => [
                                "Warning: " . trim($dirtyGroupInformationArray[$count]) . ' is not an ancestor of ' . trim($dirtyGroupInformationArray[$count + 1])
                            ]
                        ];
                    }
                    $count = $count + 1;
                }

                // Need to make sure the last groupObject name given is an ancestor of the insert groupObject
                $isRelatedToInsert = $this->orgGroupTreeRepository->isAncestor($groupObject->getId(), $groupIdsArray[$count]);
                if (!$isRelatedToInsert) {
                    $fatalError = true;
                    $this->errors[$rowId][] = [
                        'name' => $headerName,
                        'value' => '',
                        'errors' => [
                            "Warning: " . trim($dirtyGroupInformationArray[$count]) . ' is not an ancestor of ' . $data[strtolower(GroupUploadConstants::GROUP_ID)]
                        ]
                    ];
                }
            }
        }
        return $fatalError;

    }

    /**
     * This will check the group name for the upload column to the externalid group
     *
     * @param OrgGroup $groupObject => the group the to compare the FullPathNames and FullPathGroupIDs chain against
     * @param string $groupName => the name of the column
     * @param string $organizationId => the organization Id
     * @param string $rowId => the row id for error
     * @param bool $fatalError => short circuit flag for the upload
     * @return bool
     */
    public function checkGroupName($groupObject, $groupName, $organizationId, $rowId, $fatalError)
    {
        $groupNameId = $this->orgGroupRepository->convertExternalIdOrGroupNameToId($groupName, $organizationId, 'groupname');

        // Convert GroupName to ID
        if (is_array($groupNameId)) {
            $this->errors[$rowId][] = [
                'name' => GroupUploadConstants::GROUP_NAME,
                'value' => '',
                'errors' => [
                    $groupNameId['error']
                ]
            ];
            $fatalError = true;
            return $fatalError;
        }

        // make sure the Id converted
        // equals the expected Id
        $groupObjectId = $groupObject->getId();
        if ($groupObjectId != $groupNameId) {
            $this->errors[$rowId][] = [
                'name' => GroupUploadConstants::GROUP_NAME,
                'value' => '',
                'errors' => [
                    $groupName . " is not the group name of " . $groupObject->getExternalId() . ". " . $groupObject->getExternalId() . "'s group name is " . $groupObject->getGroupName()
                ]
            ];
            $fatalError = true;
        }
        return $fatalError;
    }

    /**
     * Persisting Group Faculty Data
     *
     * @param array $data
     * @param OrgGroupFaculty $groupFaculty
     * @param OrgPermissionset $orgPermissionSet
     * @param Organization $organization
     * @param OrgGroup $group
     * @param Person $person
     * @param int $rowId
     * @return bool
     */
    public function manageGroupFaculty($data, $groupFaculty, $orgPermissionSet, $organization, $group, $person, $rowId)
    {
        $logger = $this->getContainer()->get('logger');
        $data[strtolower(GroupUploadConstants::INVISIBLE)] = ($data[strtolower(GroupUploadConstants::INVISIBLE)]) ? 1 : 0;
        if (strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == strtolower(GroupUploadConstants::REMOVE) && (!$groupFaculty)) {
            $this->errors[$rowId][] = [
                'name' => GroupUploadConstants::REMOVE,
                'value' => $data[strtolower(GroupUploadConstants::REMOVE)],
                'errors' => [
                    "ExternalId does not exist"
                ]
            ];
            return false;
        }
        if ($groupFaculty) {

            // Update or remove the row being uploaded
            $logger->info("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ I AM UPDATING/REMOVING" . $data[strtolower(GroupUploadConstants::REMOVE)]);

            if (strtolower($data[strtolower(GroupUploadConstants::REMOVE)]) == 'remove') {
                $logger->info("$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ /REMOVING");
                $this->orgGroupFacultyRepository->remove($groupFaculty);
                $this->deletedRows++;
            } elseif (trim($data[strtolower(GroupUploadConstants::REMOVE)]) != "") {
                $logger->info("Uploaded CSV: Invalid value for remove.");
                $this->errors[$rowId][] = [
                    'name' => GroupUploadConstants::REMOVE,
                    'value' => $data[strtolower(GroupUploadConstants::REMOVE)],
                    'errors' => [
                        "Invalid value for column " . GroupUploadConstants::REMOVE . "."
                    ]
                ];
                return false;
            } else {
                $thisRowHasChanged = false;
                if (!empty($orgPermissionSet)) {
                    if ($groupFaculty->getOrgPermissionset() != $orgPermissionSet) {
                        $thisRowHasChanged = true;
                        $groupFaculty->setOrgPermissionset($orgPermissionSet);
                    }
                } else {
                    if (strtolower($data[strtolower(GroupUploadConstants::PERMISSION_SET)]) == strtolower(GroupUploadConstants::CLEAR)) {
                        $groupFaculty->setOrgPermissionset(null);
                    }
                }

                $isInvisible = $data[strtolower(GroupUploadConstants::INVISIBLE)];

                if ($isInvisible !== '') {
                    if ($groupFaculty->getIsInvisible() != $isInvisible) {
                        $thisRowHasChanged = true;
                        $groupFaculty->setIsInvisible($isInvisible);
                    }
                }

                if ($thisRowHasChanged) {
                    $this->updatedRows++;
                } else {
                    $this->unchangedRows++;
                }
            }
        } else {

            // Insert a new record from the upload
            $groupFaculty = new OrgGroupFaculty();
            $groupFaculty->setIsInvisible($data[strtolower(GroupUploadConstants::INVISIBLE)]);
            $groupFaculty->setOrgPermissionset($orgPermissionSet);
            $groupFaculty->setOrganization($organization);
            $groupFaculty->setOrgGroup($group);
            $groupFaculty->setPerson($person);
            $this->orgGroupFacultyRepository->persist($groupFaculty, false);
            $this->createdRows++;
        }
        return true;
    }
}