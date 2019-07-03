<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CalendarBundle\Service\Impl\CronofyWrapperService;
use Synapse\CoreBundle\Entity\PersonMetadata;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Repository\MetadataMasterRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonMetadataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Service\Impl\FacultyUploadValidatorService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;

class UpdateFaculty extends ContainerAwareJob
{
    /**
     * @var SynapseUploadService
     */
    private $synapseUploadService;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    public function run($args)
    {
        $updates = $args['updates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];

        //scaffolding
        $cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $entityValidator = $this->getContainer()->get(SynapseConstant::VALIDATOR);

        $cronofyWrapperService = $this->getContainer()->get(CronofyWrapperService::SERVICE_KEY);
        $personService = $this->getContainer()->get(PersonService::SERVICE_KEY);
        $this->synapseUploadService = $this->getContainer()->get(SynapseUploadService::SERVICE_KEY);
        $uploadFileLogService = $this->getContainer()->get(UploadConstant::UPLOAD_FILE_LOG_SERVICE);
        $validator = $this->getContainer()->get(FacultyUploadValidatorService::SERVICE_KEY);

        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $metadataMasterRepository = $repositoryResolver->getRepository(MetadataMasterRepository::REPOSITORY_KEY);
        $personMetadataRepository = $repositoryResolver->getRepository(PersonMetadataRepository::REPOSITORY_KEY);
        $orgPersonFacultyRepository = $repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);

        $errors = [];

        $validRows = 0;
        $updatedRows = 0;
        $createdRows = 0;
        $unchangedRows = 0;
        $unchangedFlag = true;
        $createdFlag = false;

        $requiredItems = [
            strtolower(UploadConstant::EXTERNALID)
        ];

        $personItems = [
            strtolower(UploadConstant::FIRSTNAME),
            strtolower(UploadConstant::LASTNAME),
            strtolower('Title'),
            strtolower(UploadConstant::DATEOFBIRTH),
            strtolower('AuthUsername')
        ];

        $contactItems = [
            strtolower('Address1'),
            strtolower('Address2'),
            strtolower('City'),
            strtolower('Zip'),
            strtolower('State'),
            strtolower('Country'),
            strtolower('PrimaryMobile'),
            strtolower('AlternateMobile'),
            strtolower('HomePhone'),
            strtolower('OfficePhone'),
            strtolower(UploadConstant::PRIMARY_EMAIL),
            strtolower('AlternateEmail'),
            strtolower('PrimaryMobileProvider'),
            strtolower('AlternateMobileProvider')
        ];

        $nonClearItems = [
            strtolower(UploadConstant::EXTERNALID),
            strtolower(UploadConstant::FIRSTNAME),
            strtolower(UploadConstant::LASTNAME),
            strtolower(UploadConstant::PRIMARY_EMAIL)
        ];

        foreach ($updates as $id => $personData) {

            // This array will be used to save the contactInfo and Person data that
            // is in the database. This will be used reset the person object if there
            // is an error
            $previousPersonProperties = array();
            $person = $personService->findOneByExternalIdOrg($personData[0], $organizationId);
            
            if (! $person) {
                $errors[$id][] = [
                    'name' => 'ExternalId',
                    UploadConstant::VALUE => $personData[0],
                    UploadConstant::ERRORS => [
                        "Please re-upload this row of data, this user's account did not exist in Mapworks when this row of data was initially uploaded."
                    ]
                ];

                continue;
            }

            $data = $personData[1];

            $requiredMissing = false;

            foreach ($requiredItems as $item) {
                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "$item is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }

                if (! $validator->validate($item, $data[strtolower($item)], $organizationId, true)) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => $data[strtolower($item)],
                        UploadConstant::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
            }

            $validData = false;
            foreach ($data as $name => $value) {
                if ($name != strtolower(UploadConstant::EXTERNALID) && ! empty($value)) {
                    $validData = true;
                }
            }

            if (! $validData) {
                $errors[$id][] = [
                    'name' => 'General',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "ExternalID and Primaryemail are required column names."
                    ]
                ];
                $requiredMissing = true;
            }

            //check for valid email id
            if (!empty($data['primaryemail']) && filter_var($data['primaryemail'], FILTER_VALIDATE_EMAIL) === false) {
                $errorsString = 'This value is not a valid email address.';
                $errors[$id][] = [
                    'name' => "PrimaryEmail",
                    'value' => 'PrimaryEmail',
                    'errors' => [
                        $errorsString
                    ]
                ];
                $requiredMissing = true;
            }

            if ($requiredMissing) {
                continue;
            }

            foreach ($data as $name => $value) {
                try {
                    if (! empty($value)) {

                        if (in_array($name, $personItems)) {
                            if ($value == UploadConstant::CLEAR_FIELD && !in_array($name, $nonClearItems)) {
                                $value = null;
                            }else{
                                if ($name == strtolower(UploadConstant::DATEOFBIRTH)) {
                                    if ((bool) strtotime($value)) {
                                        $value = new \DateTime($value);
                                    } else {
                                        $value = null;
                                    }
                                }
                            }

                            // Save the previous value within an array to
                            // reset the person object there is an error
                            $previousPersonProperties[strtolower($name)] = call_user_func([
                                $person,
                                'get' . $name
                            ], $value);

                            call_user_func([
                                $person,
                                'set' . $name
                            ], $value);
                        }

                        if (in_array($name, $contactItems)) {
                            if ($name == strtolower(UploadConstant::PRIMARY_EMAIL)) {
                                $previousPersonProperties['Username'] = $person->getUsername();
                                $person->setUsername($value);
                            }else{
                                if ($value == UploadConstant::CLEAR_FIELD && !in_array($name, $nonClearItems)) {
                                    $value = null;
                                }
                            }

                            // Save the previous value within an array to
                            // reset the person object there is an error
                            $previousPersonProperties[strtolower($name)] = call_user_func([
                                $person->getContacts()[0],
                                'get' . $name
                            ], $value);

                            call_user_func([
                                $person->getContacts()[0],
                                'set' . $name
                            ], $value);
                        }
                    }
                } catch (\Exception $e) {
                }
            }

            $entityPersonErrors = $entityValidator->validate($person);
            if (count($entityPersonErrors) > 0) {

                // populate the errors from the entity
                $errors = $this->synapseUploadService->populateErrorsForUploadRecords($errors, $entityPersonErrors, $id);

                // if any error is fatal then exit the row
                if ($this->synapseUploadService->atLeastOneErrorIsFatal($entityPersonErrors, $requiredItems)) {
                    // fail the upload
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {
                    // else the errors are benign, reset them and move on
                    $person = $this->synapseUploadService->resetInvalidPersonFieldsForUpdate($person, $entityPersonErrors, $previousPersonProperties);
                }
            }

            $validateContacts = $person->getContacts()[0];
            $entityContactInfoErrors = $entityValidator->validate($validateContacts);
            if (count($entityContactInfoErrors) > 0) {

                // populate the errors in the csv
                $errors = $this->synapseUploadService->populateErrorsForUploadRecords($errors, $entityContactInfoErrors, $id);

                // if any error is fatal then exit the row
                if ($this->synapseUploadService->atLeastOneErrorIsFatal($entityContactInfoErrors, $requiredItems)) {
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {

                    // all benign columns should be set to their previous value
                    $person = $this->synapseUploadService->resetInvalidContactInfoFieldsForUpdate($person, $entityContactInfoErrors, $previousPersonProperties);
                }
            }

            foreach ($data as $name => $value) {
                if (! empty($value)) {
                    if (! $validator->validate($name, $value, $organizationId, true)) {
                        $errors[$id][] = [
                            'name' => $name,
                            UploadConstant::VALUE => $value,
                            UploadConstant::ERRORS => $validator->getErrors()
                        ];
                    } elseif ($metadata = $metadataMasterRepository->findOneByKey($name)) {

                        $unchangedFlag = false;

                        $profileValue = new PersonMetadata();
                        $profileValue->setValue($value);
                        $profileValue->setMetadata($metadata);
                        $profileValue->setPerson($person);

                        $profileItem = $personMetadataRepository->persist($profileValue);
                    }
                }
            }

            $this->getContainer()
                ->get('person_service')
                ->updatePerson($person);

            $status = null;
            $lowerCaseIsActiveFieldName = 'isactive';
            $orgFaculty = $orgPersonFacultyRepository->findOneByPerson($person);
            if ((isset($data[$lowerCaseIsActiveFieldName]) || $data[$lowerCaseIsActiveFieldName] != "")) {
                if ($data[$lowerCaseIsActiveFieldName] == "#clear") {
                    $status = 1;
                    $orgFaculty->setStatus($status);
                    $unchangedFlag = false;
                } else {
                    $status = $data[$lowerCaseIsActiveFieldName];
                    if ($status != '') {
                        if (! is_numeric($status) || ($status != 0 && $status != 1)) {

                            $errors[$id][] = [
                                'name' => 'IsActive',
                                'value' => '',
                                'errors' => [
                                    "Invalid value. The value should be 0 or 1. "
                                ]
                                ];
                            $status = null;
                        }else{
                            if ($orgFaculty) {
                                if($orgFaculty->getStatus() != $status) {
                                    $unchangedFlag = false;
                                }
                                $orgFaculty->setStatus($status);

                                // Revoke access for the faculty
                                if ($status === 0) {
                                    $cronofyWrapperService->revokeAccess($person->getId(), $organizationId);
                                }
                            }
                        }
                    }
                }
            }

            if (! $orgFaculty) {
                $createdFlag = true;
                $orgPersonFaculty = new OrgPersonFaculty();
                $orgPersonFaculty->setOrganization($person->getOrganization());
                $orgPersonFaculty->setPerson($person);
                $orgPersonFaculty->setStatus(is_null($status) ? $status : 1);
                $orgPersonFaculty->setAuthKey($personService->generateAuthKey($person->getExternalId(), 'Faculty'));
                $orgPersonFacultyRepository->persist($orgPersonFaculty);
            }

            if($createdFlag){
                $createdRows++;
            }else{
                if($unchangedFlag){
                    $unchangedRows++;
                } else {
                    $updatedRows++;
                }
            }

            $validRows ++;
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedRows + $unchangedRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));

        $this->personRepository->flush();
        $this->personRepository->clear();

        $jobs = $cache->fetch("organization.{$organizationId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$organizationId}.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:{$organizationId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }


    /**
     * Given an array ($outputErrors) of upload errors append additional validation errors ($validationErrors) to
     * the array, for the given row ($rowIdWithValidationErrors) that has errors.
     *
     * @param array $outputErrors - Resulting array that includes new validation errors for this row
     * @param array $validationErrors -  The validation errors that happened on this row
     * @param int $rowIdWithValidationErrors - The row ID that has errors
     * @return mixed
     */
    private function populateErrorsForUploadRecords($outputErrors, $validationErrors, $rowIdWithValidationErrors)
    {
        foreach ($validationErrors as $error) {
            $value = $error->getInvalidValue();
            $outputErrors[$rowIdWithValidationErrors][] = [
                'name' => ucfirst($error->getPropertyPath()),
                UploadConstant::VALUE => $value,
                UploadConstant::ERRORS => [
                    $error->getMessage()
                ]
            ];

        }

        return $outputErrors;
    }

}
