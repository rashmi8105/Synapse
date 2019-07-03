<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\OrgPersonFaculty;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\PersonMetadata;
use Synapse\CoreBundle\Repository\MetadataMasterRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonMetadataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\EntityService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Service\Impl\FacultyUploadValidatorService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;

class CreateFaculty extends ContainerAwareJob
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
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = $args['orgId'];

        //scaffolding
        $cache = $this->getContainer()->get('synapse_redis_cache');
        $entityValidator = $this->getContainer()->get(SynapseConstant::VALIDATOR);


        //services
        $entityService = $this->getContainer()->get(EntityService::SERVICE_KEY);
        $personService = $this->getContainer()->get(PersonService::SERVICE_KEY);
        $this->synapseUploadService = $this->getContainer()->get(SynapseUploadService::SERVICE_KEY);
        $uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
        $validator = $this->getContainer()->get(FacultyUploadValidatorService::SERVICE_KEY);

        //repositories
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $organizationRepository = $repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $metadataMasterRepository = $repositoryResolver->getRepository(MetadataMasterRepository::REPOSITORY_KEY);
        $personMetadataRepository = $repositoryResolver->getRepository(PersonMetadataRepository::REPOSITORY_KEY);
        $orgPersonFacultyRepository = $repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);

        $errors = [];

        $validRows = 0;

        $requiredItems = [
            'externalid',
            'firstname',
            'lastname',
            'primaryemail'
        ];

        $personItems = [
            strtolower(UploadConstant::EXTERNALID),
            strtolower(UploadConstant::FIRSTNAME),
            strtolower(UploadConstant::LASTNAME),
            'title',
            strtolower(UploadConstant::DATEOFBIRTH),
            'authusername'
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

        // iterate through each row in the upload file
        foreach ($creates as $id => $data) {
            $requiredMissing = false;

            foreach ($requiredItems as $item) {

                if ($item == 'externalid' && $data['externalid'] == '#clear') {
                    $errors[$id][] = [
                        'name' => 'externalid',
                        'value' => '',
                        'errors' => ['The provided value for ExternalID is not allowed.']
                    ];

                    $requiredMissing = true;

                }

                // We wait until the last possible moment to lowercase the required
                //  items so we can display camel case column headers to the user
                if (!array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {

                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "{$item} is a required field"
                        ]
                    ];

                    $requiredMissing = true;
                }

                if (! $validator->validate(strtolower($item), $data[strtolower($item)], $orgId)) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => $data[strtolower($item)],
                        UploadConstant::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }

            }


            if ($requiredMissing) {
                continue;
            }

            $status = 1;

            if (isset($data[strtolower(UploadConstant::IS_ACTIVE)]) && $data[strtolower(UploadConstant::IS_ACTIVE)] != "") {
                $status = $data[strtolower(UploadConstant::IS_ACTIVE)];

                if (!is_numeric($status) || ($status != 0 && $status != 1)) {
                    $errors[$id][] = [
                        'name' => strtolower(UploadConstant::IS_ACTIVE),
                        'value' => '',
                        'errors' => [
                            "Invalid value. The value should be 0 or 1. "
                        ]
                    ];
                    continue;
                }
            }

            $person = new Person();
            $contactInfo = new ContactInfo();


            $organization = $organizationRepository->findOneById($orgId);

            foreach ($data as $name => $value) {

                // ignore the column if the user tries to clear a value that does not exist yet
                if (trim(strtolower($value)) == "#clear") {
                    continue;
                }

                try {
                    if (!empty($value)) {

                        if (in_array($name, $personItems)) {
                            if ($name == strtolower(UploadConstant::DATEOFBIRTH)) {
                                if ((bool)strtotime($value)) {
                                    $value = new \DateTime($value);
                                } else {
                                    $value = null;
                                }
                            }
                            call_user_func([
                                $person,
                                'set' . $name
                            ], $value);
                        }

                        if (in_array($name, $contactItems)) {
                            call_user_func([
                                $contactInfo,
                                'set' . $name
                            ], $value);
                        }
                    }
                } catch (\Exception $e) {
                    echo 'No match for non metadata item: ' . $name;
                }
            }

            $person->setUsername($data[strtolower(UploadConstant::PRIMARY_EMAIL)]);
            $facultyEntity = $entityService->findOneByName('Faculty');
            $person->addEntity($facultyEntity);
            $person->setOrganization($organization);

            $entityContactInfoErrors = $entityValidator->validate($contactInfo);
            if (count($entityContactInfoErrors) > 0) {

                // populate the errors
                $errors = $this->synapseUploadService->populateErrorsForUploadRecords($errors, $entityContactInfoErrors, $id);

                // if any error is fatal then exit the row
                if ($this->synapseUploadService->atLeastOneErrorIsFatal($entityContactInfoErrors, $requiredItems)) {
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {

                    // all benign columns should be set to null
                    $contactInfo = $this->synapseUploadService->unsetInvalidContactInfoFieldsForCreate($contactInfo, $entityContactInfoErrors);
                }
            }

            $entityPersonErrors = $entityValidator->validate($person);
            if (count($entityPersonErrors) > 0) {
                // populate the errors
                $errors = $this->synapseUploadService->populateErrorsForUploadRecords($errors, $entityPersonErrors, $id);

                // if any of the errors are fatal errors, exit the row
                if ($this->synapseUploadService->atLeastOneErrorIsFatal($entityPersonErrors, $requiredItems)) {
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {

                    // all benign columns should be set to null
                    $person = $this->synapseUploadService->unsetInvalidPersonFieldsForCreate($person, $entityPersonErrors);
                }
            }

            $person = $personService->createPersonRaw($person, $contactInfo);

            foreach ($data as $name => $value) {

                // ignore the column if the user tries to clear a value that does not exist yet
                if (trim(strtolower($value)) === '#clear') {
                    continue;
                }

                if (!empty($value) && !in_array($name, $requiredItems) && !in_array($name, $personItems) && !in_array($name, $contactItems)) {

                    if (! $validator->validate($name, $value, $orgId)) {
                        $errors[$id][] = [
                            'name' => $name,
                            UploadConstant::VALUE => $value,
                            UploadConstant::ERRORS => $validator->getErrors()
                        ];
                    } elseif ($metadata = $metadataMasterRepository->findOneByKey($name)) {
                        $profileValue = new PersonMetadata();
                        $profileValue->setValue($value);
                        $profileValue->setMetadata($metadata);
                        $profileValue->setPerson($person);
                        $profileItem = $personMetadataRepository->persist($profileValue);
                    }
                }
            }

            $status = 1;
            if (isset($data[strtolower(UploadConstant::IS_ACTIVE)]) && strtolower($data[strtolower(UploadConstant::IS_ACTIVE)]) !== "#clear") {

                if ($data[strtolower(UploadConstant::IS_ACTIVE)] != "") {
                    $status = $data[strtolower(UploadConstant::IS_ACTIVE)];
                    if (! is_numeric($status) || ($status != 0 && $status != 1)) {

                        $errors[$id][] = [
                        'name' => strtolower(UploadConstant::IS_ACTIVE),
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                        "Invalid Value. The Value should be 0 or 1. "
                            ]
                            ];
                        $status = 1;
                    }
                }
            }

            $orgPersonFaculty = new OrgPersonFaculty();
            $orgPersonFaculty->setOrganization($organization);
            $orgPersonFaculty->setPerson($person);
            $orgPersonFaculty->setStatus($status);
            $orgPersonFaculty->setAuthKey($personService->generateAuthKey($person->getExternalId(), UploadConstant::FACULTY));
            $personEntity = $orgPersonFacultyRepository->persist($orgPersonFaculty);

            $validRows++;
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));

        $this->personRepository->flush();
        $this->personRepository->clear();

        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);

        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }
}
