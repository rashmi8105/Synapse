<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\GroupUploadConstants;
use Synapse\CoreBundle\Entity\OrgGroup;
use Synapse\UploadBundle\Service\Impl\GroupUploadValidatorService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Gedmo\ReferenceIntegrity\Mapping\Validator;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;

class CreateSubGroup extends ContainerAwareJob
{


    const VALIDATOR_SERVICE = 'groupupload_validator_service';

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

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
    private $createdRows;

    /**
     * @var int
     */
    private $unchangedRows;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var GroupUploadValidatorService
     */
    private $groupUploadValidatorService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    /**
     * @var Validator
     */
    private $validator;

    public function run($args)
    {

        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];

        $this->errors = [];

        $this->validRows = 0;
        $this->updatedRows = 0;
        $this->createdRows = 0;

        $requiredItems = [
            'GroupId',
            'GroupName'
        ];

        $this->repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $this->uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
        $this->groupUploadValidatorService = $this->getContainer()->get(GroupUploadValidatorService::SERVICE_KEY);
        $this->validator = $this->getContainer()->get(SynapseConstant::VALIDATOR);


        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);

        foreach ($creates as $id => $data) {
            $data = array_change_key_case($data, CASE_LOWER);


            $requiredMissing = false;

            foreach ($requiredItems as $item) {

                // We wait until the last possible moment to lowercase the required
                //  items so we can display camel case column headers to the user
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

            if ($requiredMissing) {
                continue;
            }

            $parent = trim($data["parentgroupid"]);
            $parentGroup = null;

            $updateGroup = null;

            $organization = $this->organizationRepository->find($organizationId);

            if ($parent) {
                $parentGroup = $this->groupUploadValidatorService->validateContents("parentgroupid", $parent, array(
                    'class' => 'SynapseCoreBundle:OrgGroup',
                    'keys' => array(
                        'externalId' => $parent,
                        'organization' => $organizationId
                    )
                ));
                if ($parentGroup) {
                    $updateGroup = $this->orgGroupRepository->findOneBy([
                        'externalId' => $data["groupid"],
                        'parentGroup' => $parentGroup
                    ]);
                }
            } else {
                // Handle top-level groups (which will have no parent defined)
                $updateGroup = $this->orgGroupRepository->findOneBy([
                    'externalId' => $data["groupid"],
                    'parentGroup' => null,
                    'organization' => $organization
                ]);
            }

            $groupsWithDuplicateUserName = $this->orgGroupRepository->findBy(['groupName' => $data['groupname'], 'organization' => $organization]);

            foreach ($groupsWithDuplicateUserName as $groupWithDuplicateUserName) {

                if (!$updateGroup || $groupWithDuplicateUserName->getId() != $updateGroup->getId()) {
                    $message = "A group named '" . $data['groupname'] . "' already exists. Please rename your group. Group names must be unique.";
                    $duplicateGroupNameErrors = ['name' => 'GroupName', 'value' => '', 'errors' => [$message]];
                    $this->groupUploadValidatorService->setErrors($duplicateGroupNameErrors);
                    break;
                }
            }

            $errorsTrack = $this->groupUploadValidatorService->getErrors();
            $this->groupUploadValidatorService->clearErrors();

            if (sizeof($errorsTrack) > 0) {
                $this->errors[$id] = $errorsTrack;
            } else {
                $this->manageGroup($id, $data, $updateGroup, $parentGroup, $organization);
            }
        }

        $this->uploadFileLogService->updateValidRowCount($uploadId, $this->validRows);
        $this->uploadFileLogService->updateCreatedRowCount($uploadId, $this->createdRows);
        $this->uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));
        $this->uploadFileLogService->updateUpdatedRowCount($uploadId, $this->updatedRows + $this->unchangedRows);

        $this->orgGroupRepository->flush();
        $this->orgGroupRepository->clear();
        $jobs = $this->cache->fetch("subgroup.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $this->cache->save("subgroup.upload.{$uploadId}.jobs", $jobs);

        $this->cache->save("subgroup.upload.{$uploadId}.job.{$jobNumber}.errors", $this->errors);

        return $this->errors;
    }

    /**
     * If this is a new record for a group, it persists the group to the database. Otherwise it updates the existsing
     * group entity and updates it as part of batch processing in another method.
     *
     * @param int $id
     * @param array $data
     * @param OrgGroup $orgGroup
     * @param OrgGroup $parentGroup
     * @param Organization $organization
     */
    public function manageGroup($id, $data, $orgGroup, $parentGroup, $organization)
    {
        $groupName = '';
        try {
            if ($orgGroup) {
                // Update Group Name
                $groupName = $orgGroup->getGroupName();
                if ($groupName == $data["groupname"]) {
                    $this->unchangedRows++;
                } else {
                    $this->updatedRows++;
                }

                $orgGroup->setGroupName($data["groupname"]);

                $errors = $this->validator->validate($orgGroup);

                $this->validateOrgGroup($errors);

            } else {
                // Insert Group

                $orgGroup = new OrgGroup();
                $orgGroup->setGroupName($data["groupname"]);
                $orgGroup->setExternalId($data["groupid"]);
                $orgGroup->setOrganization($organization);
                $orgGroup->setParentGroup($parentGroup);

                $errors = $this->validator->validate($orgGroup);

                $this->validateOrgGroup($errors);

                $this->orgGroupRepository->persist($orgGroup, false);
                $this->createdRows++;

            }
            $this->validRows++;
        } catch (ValidationException $e) {


            $this->errors[$id][] = [
                'name' => "GroupId",
                'value' => '',
                'errors' => [
                    $e->getMessage()
                ]
            ];
            $orgGroup->setGroupName($groupName);
        }
    }

    /**
     * Function throws error when there is a validation exception
     *
     * @param array $errors
     */
    private function validateOrgGroup($errors)
    {
        if (count($errors) > 0) {
            throw new ValidationException([
                $errors[0]->getMessage()
            ], $errors[0]->getMessage(), 'group_error');
        }
    }
}