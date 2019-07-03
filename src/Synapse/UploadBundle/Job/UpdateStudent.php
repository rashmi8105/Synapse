<?php
namespace Synapse\UploadBundle\Job;

use PHPExcel_Shared_Date;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\GoogleFormatService;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\OrgPersonStudentCohort;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\PersonEbiMetadata;
use Synapse\CoreBundle\Entity\PersonOrgMetadata;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentCohortRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonOrgMetaDataRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\ReferralService;
use Synapse\CoreBundle\Service\Impl\TalkingPointService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Entity\OrgPersonStudentRetentionTrackingGroup;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\RiskBundle\Entity\RiskGroupPersonHistory;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Repository\RiskModelMasterRepository;
use Synapse\RiskBundle\Service\Impl\RiskCalculationService;
use Synapse\SurveyBundle\Repository\WessLinkRepository;
use Synapse\UploadBundle\Repository\UploadFileLogRepository;
use Synapse\UploadBundle\Service\Impl\ProfileValidationService;
use Synapse\UploadBundle\Service\Impl\StudentUploadValidatorService;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class UpdateStudent extends StudentBase
{

    const FirstName = 'Firstname';

    const LastName = 'Lastname';

    const DateOfBirth = 'DateofBirth';

    /**
     * @var TalkingPointService
     */
    private $talkingPointService;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * @var RiskModelMasterRepository
     */
    private $riskModelMasterRepository;

    /**
     * @var OrgRiskGroupModelRepository
     */
    private $orgRiskGroupModelRepository;

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var ActivityLogRepository
     */
    private $activityLogRepository;

    /**
     * @var AppointmentRecepientAndStatusRepository
     */
    private $appointmentRecipientAndStatusRepository;

    /**
     * @var AppointmentsRepository
     */
    private $appointmentRepository;


    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var ReferralService
     */
    private $referralService;


    /**
     * @var UploadFileLogRepository
     */
    private $uploadFileLogRepository;


    public function run($args)
    {
        $updates = $args['updates'];

        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];

        $output = new StreamOutput(fopen('php://stdout', 'w'));

        //services
        $entityValidator = $this->getContainer()->get(SynapseConstant::VALIDATOR);
        $groupService = $this->getContainer()->get(GroupService::SERVICE_KEY);
        $personService = $this->getContainer()->get(PersonService::SERVICE_KEY);
        $profileItemValidationService = $this->getContainer()->get(ProfileValidationService::SERVICE_KEY);
        $riskCalculationService = $this->getContainer()->get(RiskCalculationService::SERVICE_KEY);
        $synapseUploadService = $this->getContainer()->get(SynapseUploadService::SERVICE_KEY);
        $this->academicYearService = $this->getContainer()->get(AcademicYearService::SERVICE_KEY);
        $this->referralService = $this->getContainer()->get(ReferralService::SERVICE_KEY);
        $this->talkingPointService = $this->getContainer()->get(TalkingPointService::SERVICE_KEY);
        $uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
        $validator = $this->getContainer()->get(StudentUploadValidatorService::SERVICE_KEY);

        // Scaffolding
        $cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $logger = $this->getContainer()->get(SynapseConstant::LOGGER_KEY);

        //repositories
        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $ebiMetadataRepository = $repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $orgGroupStudentsRepository = $repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $orgMetadataRepository = $repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $orgPersonStudentCohortRepository = $repositoryResolver->getRepository(OrgPersonStudentCohortRepository::REPOSITORY_KEY);
        $orgPersonStudentRepository = $repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $personOrgMetadataRepository = $repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $riskGroupRepository = $repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
        $this->activityLogRepository = $repositoryResolver->getRepository(ActivityLogRepository::REPOSITORY_KEY);
        $this->appointmentRecipientAndStatusRepository = $repositoryResolver->getRepository(AppointmentRecepientAndStatusRepository::REPOSITORY_KEY);
        $this->appointmentRepository = $repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgRiskGroupModelRepository = $repositoryResolver->getRepository(OrgRiskGroupModelRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->riskGroupPersonHistoryRepository = $repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);
        $this->riskModelMasterRepository = $repositoryResolver->getRepository(RiskModelMasterRepository::REPOSITORY_KEY);
        $this->uploadFileLogRepository = $repositoryResolver->getRepository(UploadFileLogRepository::REPOSITORY_KEY);
        $wessLinkRepository = $repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);

        $uploadFileLogObject = $this->uploadFileLogRepository->find($uploadId);
        $loggedInPerson = null;
        if ($uploadFileLogObject->getPersonId()) {
            $loggedInPersonId = $uploadFileLogObject->getPersonId();
            $loggedInPerson = $this->personRepository->find($loggedInPersonId);
        }

        $errors = [];

        $validRows = 0;
        $updatedStudentRows = 0;
        $createdStudentRows = 0;
        $unchangedStudentRows = 0;

        $surveyCohortSetEmpty = false;
        $surveyCohort = null;

        $requiredItems = [
            'externalid'
        ];

        $requiredItemsSkip = [
            'externalid',
            'firstname',
            'lastname',
            'primaryemail',
            'externalid'
        ];


        $personItems = [
            'externalid',
            'firstname',
            'lastname',
            'title',
            'dateofbirth',
            'authusername'
        ];

        $contactItems = [
            'address1',
            'address2',
            'city',
            'zip',
            'state',
            'country',
            'primarymobile',
            'alternatemobile',
            'homephone',
            'officephone',
            'primaryemail',
            'alternateemail',
            'primarymobileprovider',
            'alternatemobileprovider'
        ];

        $nonClearItems = [
            'externalid',
            'firstname',
            'lastname',
            'primaryemail',
            'surveycohort',
            'riskgroupid'
        ];

        $uppercaseItems = [
            'externalid' => 'ExternalId',
            'authusername' => 'AuthUsername',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'title' => 'title',
            'studentphoto' => 'StudentPhoto',
            'isactive' => 'IsActive',
            'surveycohort' => 'SurveyCohort',
            'yearid' => 'YearId',
            'termid' => 'TermId',
            'participating' => 'Participating',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'zip' => 'Zip',
            'state' => 'State',
            'country' => 'Country',
            'primarymobile' => 'PrimaryMobile',
            'alternatemobile' => 'AlternateMobile',
            'homephone' => 'HomePhone',
            'primaryemail' => 'PrimaryEmail',
            'alternateemail' => 'AlternateEmail',
            'primarymobileprovider' => 'PrimaryMobileProvider',
            'alternatemobileprovider' => 'AlternateMobileProvider',
            'dateofbirth' => 'DateofBirth',
        ];

        // Checking System Group Created Or Not
        //TODO:address graceful failure if the organization does not exist.
        $organization = $this->organizationRepository->find($organizationId);
        $systemGroup = $groupService->addSystemGroup('All Students', 'ALLSTUDENTS', $organization);

        // Get Current Date Time and convert to Organization Timezone
        $currentDateOrgTime = new \DateTime('now');

        foreach ($updates as $id => $personData) {
            $createdStudentFlag = false;
            $unchangedFlag = true;

            $person = $personService->findOneByExternalIdOrg($personData[0], $organizationId);

            if (!$person) {
                $errors[$id][] = [
                    'name' => 'ExternalId',
                    'value' => $personData[0],
                    'errors' => [
                        "Please re-upload this row of data, this user's account did not exist in Mapworks when this row of data was initially uploaded."
                    ]
                ];

                continue;
            }

            $data = $personData[1];

            $requiredMissing = false;
            $studentPhotoURL = "";

            foreach ($requiredItems as $item) {
                if (!array_key_exists($item, $data) || empty($data[$item])) {
                    if ($item == 'primaryemail') { // this won't happen
                        $errorMsg = 'ExternalID and Primary Email are required column names';
                    } else {
                        $errorMsg = "$uppercaseItems[$item] is a required field";
                    }
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => '',
                        'errors' => [
                            $errorMsg
                        ]
                    ];
                    $requiredMissing = true;
                }

                if (preg_match("/(^\s+|\s+$|^\s+$)/", $data[$item])) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => $data[$item],
                        'errors' => ['Field can not contain any empty spaces']
                    ];
                    $requiredMissing = true;
                }

                if (!$validator->validate($item, $data[$item], $organizationId, true)) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => $data[$item],
                        'errors' => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
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
            }

            $validData = false;
            foreach ($data as $name => $value) {
                if ($name != 'externalid' && isset($value)) {
                    $validData = true;
                }
            }

            if (!$validData) {
                $errors[$id][] = [
                    'name' => 'General',
                    'value' => '',
                    'errors' => [
                        "ExternalID and Primary Email are required column names."
                    ]
                ];
                $requiredMissing = true;
            }

            if ($requiredMissing) {
                continue;
            }

            $previousPersonProperties = [];

            foreach (array_intersect_key($data, array_fill_keys(array_merge($personItems, $contactItems), 1)) as $name => $value) {
                try {
                    if (isset($value) && !empty($value)) {

                        if (in_array($name, $personItems)) {

                            if ($value == '#clear' && !in_array($name, $nonClearItems)) {
                                $value = null;
                            } else {
                                if ($name == 'dateofbirth') {
                                    if ((bool)strtotime($value)) {
                                        $value = new \DateTime($value);
                                    } else {
                                        $value = null;
                                    }
                                }
                            }

                            $previousPersonProperties[$name] = call_user_func([
                                $person,
                                'get' . $uppercaseItems[$name]
                            ], $value);

                            call_user_func([
                                $person,
                                'set' . $uppercaseItems[$name]
                            ], $value);
                        }

                        if (in_array($name, $contactItems)) {

                            if ($name == 'primaryemail') {
                                $previousPersonProperties['Username'] = $person->getUsername();
                                $person->setUsername($value);
                            } else {
                                if ($value == '#clear' && !in_array($name, $nonClearItems)) {
                                    $value = null;
                                }
                            }

                            $previousPersonProperties[$name] = call_user_func([
                                $person->getContacts()[0],
                                'get' . $uppercaseItems[$name]
                            ], $value);

                            call_user_func([
                                $person->getContacts()[0],
                                'set' . $uppercaseItems[$name]
                            ], $value);
                        }
                    }

                } catch (\Exception $e) {
                    $output->writeln('No match for non metadata item: ' . $name);
                }
            }

            $entityPersonError = $entityValidator->validate($person);
            if (count($entityPersonError) > 0) {

                // populate the errors from the entity
                $errors = $synapseUploadService->populateErrorsForUploadRecords($errors, $entityPersonError, $id);

                // if any error is fatal then exit the row
                if ($synapseUploadService->atLeastOneErrorIsFatal($entityPersonError, $requiredItems)) {
                    // fail the upload
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {
                    // else the errors are benign, reset them and move on
                    $person = $synapseUploadService->resetInvalidPersonFieldsForUpdate($person, $entityPersonError, $previousPersonProperties);
                }
            }

            $validateContacts = $person->getContacts()[0];
            $entityContactErrors = $entityValidator->validate($validateContacts);

            // if there are errors
            if (count($entityContactErrors) > 0) {
                // populate the errors from the entity
                $errors = $synapseUploadService->populateErrorsForUploadRecords($errors, $entityContactErrors, $id);

                // if any error is fatal then exit the row
                if ($synapseUploadService->atLeastOneErrorIsFatal($entityContactErrors, $requiredItems)) {
                    // fail the upload
                    unset($person);
                    $this->personRepository->clear();
                    continue;
                } else {
                    // else the errors are benign, reset them and move on
                    $person = $synapseUploadService->resetInvalidContactInfoFieldsForUpdate($person, $entityContactErrors, $previousPersonProperties);
                }

            }

            foreach ($data as $name => $value) {
                if(!in_array($name, $uppercaseItems)){
                    $uppercaseItems[strtolower($name)] = $name;
                }
                $nonEmptyValue = trim($value);
                if (preg_match("/(^\s|\s$)/", $value)) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$name],
                        'value' => $value,
                        'errors' => ['Field can not contain any empty spaces']
                    ];
                    continue;
                }

                // we do not want to validate again as the contact items are already validated.
                if (in_array($name, $contactItems)) {
                    continue;
                }


                if (isset($value) && strlen($nonEmptyValue) > 0 && !in_array($name, $requiredItemsSkip)) {
                    $output->writeln("$name has data, processing");


                    // custom validations done here
                    $customValidationErrMsg = $profileItemValidationService->profileItemCustomValidations($uppercaseItems[$name], $value);
                    if (trim($customValidationErrMsg) != "") {
                        $errors[$id][] = [
                            'name' => $uppercaseItems[$name],
                            'value' => $value,
                            'errors' => [
                                $customValidationErrMsg
                            ]
                        ];
                        continue;
                    }
                    // custom validations ends here
                    if ($value == '#clear') {

                        // get individual and organization metadata
                        $metadata = $ebiMetadataRepository->findOneBy(['key' => $name ]);
                        $orgmetadata = $orgMetadataRepository->findOneBy([
                            'metaKey' => $name,
                            'organization' => $person->getOrganization()
                        ]);


                        // sometimes $metadata will be null/false, so use $orgmetadata if that is the case
                        $validMetadata = ($metadata ? $metadata : $orgmetadata);

                        // check if #clear is in a term specific field and if termId/yearId are empty
                        if ($validMetadata && $validMetadata->getScope() == 'T') {
                            if (!$data['termid'] || !$data['yearid']) {
                                // raise an appropriate error message if termId and/or yearId are missing
                                $errors[$id][] = [
                                    'name' => $uppercaseItems[$name],
                                    'value' => $value,
                                    'errors' => [
                                        'Because you are clearing term based data, both YearId and TermId are required columns.'
                                    ]
                                ];
                                continue;
                            }
                            // check if #clear is in a year specific field and if yearId is empty
                        } elseif ($validMetadata && $validMetadata->getScope() == 'Y') {
                            if (!$data['yearid']) {
                                // raise an appropriate error message if yearId is missing
                                $errors[$id][] = [
                                    'name' => $uppercaseItems[$name],
                                    'value' => $value,
                                    'errors' => [
                                        'Because you are clearing year based data, the YearId column is required.'
                                    ]
                                ];
                                continue;
                            }
                        }

                        if ($metadata) {
                            //add scope cases
                            $scope = $metadata->getScope();
                            $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                'organization' => $organization,
                                'yearId' => $data['yearid']
                            ]);


                            switch ($scope) {
                                case "T":
                                    if ($academicYear) {
                                        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                            'organization' => $organization,
                                            'termCode' => $data['TermId'],
                                            'orgAcademicYearId' => $academicYear->getId()
                                        ]);
                                        if ($academicTerm) {
                                            $personProfile = $this->personEbiMetadataRepository->findOneBy([
                                                'ebiMetadata' => $metadata->getId(),
                                                'person' => $person->getId(),
                                                'orgAcademicYear' => $academicYear->getId(),
                                                'orgAcademicTerms' => $academicTerm->getId()
                                            ]);
                                        } else {
                                            $personProfile = null;
                                        }
                                    } else {
                                        $personProfile = null;
                                    }

                                    break;
                                case "Y":
                                    if ($academicYear) {
                                        $personProfile = $this->personEbiMetadataRepository->findOneBy([
                                            'ebiMetadata' => $metadata,
                                            'person' => $person,
                                            'orgAcademicYear' => $academicYear->getId()
                                        ]);
                                    } else {
                                        $personProfile = null;
                                    }
                                    break;
                                default:
                                    $personProfile = $this->personEbiMetadataRepository->findOneBy([
                                        'ebiMetadata' => $metadata,
                                        'person' => $person
                                    ]);
                            }

                            if ($personProfile) {

                                switch ($scope) {
                                    case "T":
                                        $academicTermId = $academicTerm->getId();
                                        $academicYearId = $academicYear->getId();
                                        break;
                                    case "Y":
                                        $academicYearId = $academicYear->getId();
                                        $academicTermId = null;
                                        break;
                                    default:
                                        $academicTermId = null;
                                        $academicYearId = null;
                                }

                                // Delete last talking point record if exist
                                $this->talkingPointService->deleteLastOrgTalkingPointForPersonAndProfileItem($organization->getId(), $person->getId(), $metadata->getId(), $academicYearId, $academicTermId);

                                // Remove
                                $this->personEbiMetadataRepository->delete($personProfile, true);
                                $unchangedFlag = false;
                            }

                        } else {
                            $orgmetadata = $orgMetadataRepository->findOneBy([
                                'metaKey' => $name,
                                'organization' => $person->getOrganization()
                            ]);
                            if ($orgmetadata) {
                                //add scope cases
                                $scope = $orgmetadata->getScope();
                                $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                    'organization' => $organization,
                                    'yearId' => $data['yearid']
                                ]);

                                switch ($scope) {
                                    case "T":
                                        if ($academicYear) {
                                            $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                                'organization' => $organization,
                                                'termCode' => $data['termid'],
                                                'orgAcademicYearId' => $academicYear->getId()
                                            ]);
                                            if ($academicTerm) {
                                                $personProfile = $personOrgMetadataRepository->findOneBy([
                                                    'orgMetadata' => $orgmetadata->getId(),
                                                    'person' => $person->getId(),
                                                    'orgAcademicYear' => $academicYear->getId(),
                                                    'orgAcademicPeriods' => $academicTerm->getId()
                                                ]);
                                            } else {
                                                $personProfile = null;
                                            }
                                        } else {
                                            $personProfile = null;
                                        }

                                        break;
                                    case "Y":
                                        if ($academicYear) {
                                            $personProfile = $personOrgMetadataRepository->findOneBy([
                                                'orgMetadata' => $orgmetadata,
                                                'person' => $person,
                                                'orgAcademicYear' => $academicYear
                                            ]);
                                        } else {
                                            $personProfile = null;
                                        }
                                        break;
                                    default:
                                        $personProfile = $personOrgMetadataRepository->findOneBy([
                                            'orgMetadata' => $orgmetadata,
                                            'person' => $person
                                        ]);
                                }
                                if ($personProfile) {
                                    //Remove
                                    $personOrgMetadataRepository->delete($personProfile, true);
                                    $unchangedFlag = false;

                                }
                            }
                        }

                    } else {
                        if (!$validator->validate($uppercaseItems[$name], $value, $organizationId, true)) {
                            if (!in_array(trim($name), $contactItems)) {
                                $errors[$id][] = [
                                    'name' => $uppercaseItems[$name],
                                    'value' => $value,
                                    'errors' => $validator->getErrors()
                                ];
                            }
                        } else {
                            if ($metadata = $ebiMetadataRepository->findOneBy(['key' => $name])) {
                                $academicTerm = null;
                                $academicYear = null;
                                $msg = null;

                                if ($metadata->getScope() == 'Y' || $metadata->getScope() == 'T') {
                                    if ($metadata->getScope() == 'T') {
                                        if (!$data['termid']) {
                                            $msg = 'Because you have term based data, TermId is a required column.';
                                        }
                                        if (!$data['termid'] && !$data['yearid']) {
                                            $msg = 'Because you have term based data, both YearId and TermId are required columns.';
                                        }
                                        if (trim($msg) != "") {
                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    $msg
                                                ]
                                            ];
                                            continue;
                                        }
                                    }

                                    if (!$data['yearid']) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'YearId is a required column name.'
                                            ]
                                        ];

                                        continue;
                                    }

                                    $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                        'organization' => $organization,
                                        'yearId' => $data['yearid']
                                    ]);

                                    if (!$academicYear) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'YearId is invalid'
                                            ]
                                        ];

                                        continue;
                                    }
                                }

                                if ($metadata->getScope() == 'T') {

                                    if (!$data['termid']) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'Because you have term based data, TermId is a required column.'
                                            ]
                                        ];

                                        continue;
                                    }

                                    $academicTerm = null;

                                    if ($academicYear) {
                                        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                            'organization' => $organization,
                                            'termCode' => $data['termid'],
                                            'orgAcademicYearId' => $academicYear->getId()
                                        ]);
                                    }

                                    if (!$academicTerm) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'TermId is invalid'
                                            ]
                                        ];

                                        continue;
                                    }
                                }
                                $yearTermSearch = [];
                                if ($metadata->getScope() == 'Y' || $metadata->getScope() == 'T') {
                                    $yearTermSearch['orgAcademicYear'] = $academicYear;
                                }
                                if ($metadata->getScope() == 'T') {
                                    $yearTermSearch['orgAcademicTerms'] = $academicTerm;
                                }
                                if ($profileValue = $this->personEbiMetadataRepository->findOneBy(array_merge([
                                    'ebiMetadata' => $metadata,
                                    'person' => $person
                                ], $yearTermSearch))
                                ) {
                                    if ($profileValue->getEbiMetadata()->getMetadataType() == 'D') {

                                        if (strlen(trim($value)) > 0 && date(SynapseConstant::DATE_FORMAT, strtotime($value))) {
                                            $value = gmdate(SynapseConstant::DATE_FORMAT, strtotime($value));
                                        } else {

                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    "Not a valid date, date format should be in mm/dd/yyyy format"
                                                ]
                                            ];
                                            continue;
                                        }

                                    }

                                    $profileValue->setMetadataValue($value);
                                    $profileItem = $this->personEbiMetadataRepository->update($profileValue);
                                    $unchangedFlag = false;
                                } else {
                                    if ($metadata->getMetadataType() == 'D') {

                                        if (strlen(trim($value)) > 0 && date(SynapseConstant::DATE_FORMAT, strtotime($value))) {
                                            $value = gmdate(SynapseConstant::DATE_FORMAT, strtotime($value));
                                        } else {

                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    "Not a valid date, date format should be in mm/dd/yyyy format"
                                                ]
                                            ];
                                            continue;
                                        }
                                    }

                                    $profileValue = new PersonEbiMetadata();
                                    $profileValue->setMetadataValue($value);
                                    $profileValue->setEbiMetadata($metadata);

                                    $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                        'organization' => $organization,
                                        'yearId' => $data['yearid']
                                    ]);
                                    if (($metadata->getScope() == 'T' || $metadata->getScope() == 'Y') && $academicYear) {
                                        $profileValue->setOrgAcademicYear($academicYear);


                                        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                            'organization' => $organization,
                                            'orgAcademicYearId' => $academicYear->getId(),
                                            'termCode' => $data['termid']
                                        ]);
                                        if ($metadata->getScope() == 'T' && $academicTerm) {
                                            $unchangedFlag = false;
                                            $profileValue->setOrgAcademicTerms($academicTerm);

                                        }
                                    }


                                    $profileValue->setPerson($person);
                                    $unchangedFlag = false;
                                    $profileItem = $this->personEbiMetadataRepository->persist($profileValue);
                                }
                            } elseif ($metadata = $orgMetadataRepository->findOneBy([
                                'metaKey' => $name,
                                'organization' => $person->getOrganization()
                            ])
                            ) {
                                $academicYear = null;
                                $academicTerm = null;
                                $msg = null;

                                if ($metadata->getScope() == 'Y' || $metadata->getScope() == 'T') {
                                    if ($metadata->getScope() == 'T') {
                                        if (!$data['termid']) {
                                            $msg = 'Because you have term based data, TermId is a required column.';
                                        }
                                        if (!$data['termid'] && !$data['yearid']) {
                                            $msg = 'Because you have term based data, both YearId and TermId are required columns.';
                                        }
                                        if (trim($msg) != "") {
                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    $msg
                                                ]
                                            ];
                                            continue;
                                        }
                                    }

                                    if (!$data['yearid']) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'YearId is a required column name.'
                                            ]
                                        ];

                                        continue;
                                    }

                                    $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                        'organization' => $organization,
                                        'yearId' => $data['yearid']
                                    ]);

                                    if (!$academicYear) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'YearId is invalid'
                                            ]
                                        ];

                                        continue;
                                    }
                                }

                                if ($metadata->getScope() == 'T') {

                                    if (!$data['termid']) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'Because you have term based data, TermId is a required column.'
                                            ]
                                        ];

                                        continue;
                                    }

                                    $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                        'organization' => $organization,
                                        'termCode' => $data['termid'],
                                        'orgAcademicYearId' => $academicYear->getId()
                                    ]);

                                    if (!$academicTerm) {
                                        $errors[$id][] = [
                                            'name' => $uppercaseItems[$name],
                                            'value' => $value,
                                            'errors' => [
                                                'TermId is invalid'
                                            ]
                                        ];

                                        continue;
                                    }
                                }
                                $yearTermSearch = [];
                                if ($metadata->getScope() == 'Y' || $metadata->getScope() == 'T') {
                                    $yearTermSearch['orgAcademicYear'] = $academicYear;
                                }
                                if ($metadata->getScope() == 'T') {
                                    $yearTermSearch['orgAcademicPeriods'] = $academicTerm;
                                }
                                if ($profileValue = $personOrgMetadataRepository->findOneBy(array_merge([
                                    'orgMetadata' => $metadata,
                                    'person' => $person
                                ], $yearTermSearch))
                                ) {
                                    if ($profileValue->getOrgMetadata()->getMetadataType() == 'D') {

                                        if (strlen(trim($value)) > 0 && strtotime($value)) {

                                            $value = gmdate(SynapseConstant::DATE_FORMAT, strtotime($value));
                                        } else {

                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    "Not a valid date, date format should be in mm/dd/yyyy format"
                                                ]
                                            ];
                                            continue;
                                        }
                                    }
                                    $profileValue->setMetadataValue($value);
                                    $profileItem = $personOrgMetadataRepository->update($profileValue);

                                } else {
                                    if ($metadata->getMetadataType() == 'D') {

                                        if (strlen(trim($value)) > 0 && date(SynapseConstant::DATE_FORMAT, strtotime($value))) {
                                            $value = gmdate(SynapseConstant::DATE_FORMAT, strtotime($value));
                                        } else {
                                            $errors[$id][] = [
                                                'name' => $uppercaseItems[$name],
                                                'value' => $value,
                                                'errors' => [
                                                    "Not a valid date, date format should be in mm/dd/yyyy format"
                                                ]
                                            ];
                                            continue;
                                        }
                                    }
                                    $profileValue = new PersonOrgMetadata();
                                    $profileValue->setMetadataValue($value);
                                    $profileValue->setOrgMetadata($metadata);
                                    $profileValue->setPerson($person);
                                    $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                        'organization' => $organization,
                                        'yearId' => $data['yearid']
                                    ]);
                                    if (isset($data['yearid']) && !empty($data['yearid'])) {
                                        $academicYear = $this->orgAcademicYearRepository->findOneBy([
                                            'organization' => $organization,
                                            'yearId' => $data['yearid']
                                        ]);
                                        if (($metadata->getScope() == 'T' || $metadata->getScope() == 'Y') && $academicYear) {
                                            $profileValue->setOrgAcademicYear($academicYear);
                                        }
                                    }
                                    if (isset($data['termid']) && !empty($data['termid']) && $academicYear) {
                                        $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                                            'organization' => $organization,
                                            'orgAcademicYearId' => $academicYear->getId(),
                                            'termCode' => $data['termid']
                                        ]);
                                        if ($metadata->getScope() == 'T' && $academicTerm) {
                                            $profileValue->setOrgAcademicPeriods($academicTerm);
                                        }
                                    }

                                    $profileItem = $personOrgMetadataRepository->persist($profileValue);
                                }
                            }
                        }
                    }
                } else {
                    $output->writeln("$name is empty, skipping");
                }
            }

            $personService->updatePerson($person);

            if (isset($data['studentphoto']) && (filter_var($data['studentphoto'], FILTER_VALIDATE_URL) || $data['studentphoto'] == "#clear")) {

                $photourl = $data['studentphoto'];

                if (!empty($photourl) || $photourl == "#clear") {

                    if ($photourl != "#clear") {
                        $photoFileInfo = $this->get_http_response_code($photourl);
                        if (!$photoFileInfo || $photoFileInfo['status'] == "200") {
                            if (isset($photoFileInfo["type"]) && strstr($photoFileInfo["type"], "image")) {
                                try {
                                    $studentPhotoURL = $photourl;

                                } catch (\Exception $e) {

                                    $studentPhotoURL = false;
                                }
                            } else {
                                $errors[$id][] = [
                                    'name' => 'StudentPhoto',
                                    'value' => '',
                                    'errors' => [
                                        "Invalid image format. Image must be formatted as a .jpg file"
                                    ]
                                ];
                            }
                        } else {
                            $errors[$id][] = [
                                'name' => 'StudentPhoto',
                                'value' => '',
                                'errors' => [
                                    "Image URL could not be found."
                                ]
                            ];
                        }

                    } else {
                        $studentPhotoURL = null;
                    }
                }
            } else {
                if (isset($data['studentphoto']) && !(filter_var($data['studentphoto'], FILTER_VALIDATE_URL)) && trim($data['studentphoto']) != '') {
                    // not given a valid url
                    $errors[$id][] = [
                        'name' => 'StudentPhoto',
                        'value' => '',
                        'errors' => [
                            $data['StudentPhoto'] . " is not a valid url."
                        ]
                    ];
                }
                $studentPhotoURL = false;
            }

            if (isset($data['yearid']) && trim($data['yearid'] != "")) {
                $academicYear = $this->orgAcademicYearRepository->findOneBy([
                    'organization' => $organization,
                    'yearId' => $data['yearid']
                ]);

            } else {
                $academicYear = null;
            }

            $lowercaseParticipatingFieldName = "participating";

            $isParticipant = null; // If the value is null -  no db operation, 0- mark deleted_at to current date ,  1 - deleted_at Should be null
            if (isset($data[$lowercaseParticipatingFieldName]) && trim($data[$lowercaseParticipatingFieldName]) != "") {

                if ($data[$lowercaseParticipatingFieldName] !== 1 && $data[$lowercaseParticipatingFieldName] !== "1"
                    && $data[$lowercaseParticipatingFieldName] !== 0 && $data[$lowercaseParticipatingFieldName] !== "0"
                ) {
                    $errors[$id][] = [
                        'name' => 'Participating',
                        'value' => '',
                        'errors' => [
                            "Invalid value for participating column, should be either 1 or 0"
                        ]
                    ];
                } else {
                    $isParticipant = $data[$lowercaseParticipatingFieldName];
                }

                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => 'Participating',
                        'value' => '',
                        'errors' => [
                            "Participating students need to have a valid year to participate in"
                        ]
                    ];
                    $isParticipant = null;
                }
            }

            // checking if the value provided by the user of YearId in csv , is a valid value or not
            if (!empty($data['yearid'])) {
                $academicYear = $validator->validateAcademicYear($organizationId, $data['yearid']);
                // If not a valid academic year report error and skip
                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => 'YearId',
                        'value' => '',
                        'errors' => [
                            'Year Id should be a valid academic year id for the organization'
                        ]
                    ];
                }
            }
            // check if we have valid academic term
            if (!empty($data['termid']) && !empty($academicYear)) {
                $academicTerm = $this->orgAcademicTermRepository->findOneBy([
                    'organization' => $organization,
                    'termCode' => $data['termid'],
                    'orgAcademicYearId' => $academicYear->getId()
                ]);
                if (!$academicTerm) {
                    $errors[$id][] = [
                        'name' => 'TermId',
                        'value' => '',
                        'errors' => [
                            'Term Id should be a valid academic term for the organization'
                        ]
                    ];
                }
            }


            //  set student status here.
            $lowerCaseIsActiveFieldName = 'isactive';
            $updateIsActiveColumn = null; // if 1 -  update is Active , 0 -  don't update db

            if (isset($data[$lowerCaseIsActiveFieldName]) && $data[$lowerCaseIsActiveFieldName] != "") {

                $updateIsActiveColumn = $data[$lowerCaseIsActiveFieldName];
                if ($data[$lowerCaseIsActiveFieldName] !== 1 && $data[$lowerCaseIsActiveFieldName] !== "1"
                    && $data[$lowerCaseIsActiveFieldName] !== 0 && $data[$lowerCaseIsActiveFieldName] !== "0"
                ) {
                    $errors[$id][] = [
                        'name' => 'IsActive',
                        'value' => '',
                        'errors' => [
                            "Invalid value for IsActive column"
                        ]
                    ];
                    $updateIsActiveColumn = null;
                }

                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => 'IsActive',
                        'value' => '',
                        'errors' => [
                            "Invalid academic year to set student as active"
                        ]
                    ];
                    $updateIsActiveColumn = null;
                }
            }


            // we need to update the org_person_student_year table only if we are  updating the is_active or if we are updating student participant
            if ((!is_null($updateIsActiveColumn) || !is_null($isParticipant)) && $academicYear) {

                // The YearId  provided may or may not be current academic year, check if the YearId provided is current academic year
                //if the YearId provided is the current academic year then cancel the appointments, else we don't want to cancel the appointments

                $isCurrentAcademicYear = 0;

                $orgPersonStudentYearObj = $this->orgPersonStudentYearRepository->findOneBy([
                    "person" => $person,
                    "organization" => $organization,
                    "orgAcademicYear" => $academicYear
                ]);

                // Breaking up if statement trying to make it more readable.
                // if you are setting the participant field off
                $isParticipantSetToZero = $isParticipant === "0" || $isParticipant === 0;

                // or there is no org person student year object and you are not trying to add one into the table
                $noParticipantValue = (!$orgPersonStudentYearObj && !($isParticipant === "1" || $isParticipant === 1));

                // and you are trying to activate teh is active column
                $updatingIsActiveColumn = ($updateIsActiveColumn === "1" || $updateIsActiveColumn === 1);

                // Then throw an error
                if (($isParticipantSetToZero || $noParticipantValue) && $updatingIsActiveColumn) {
                    $errors[$id][] = [
                        'name' => "IsActive",
                        'value' => '',
                        'errors' => [
                            "We cannot make a person active for a year they are not participating in"
                        ]
                    ];
                }

                $currentDate = new \DateTime('now');
                $currentDateString = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $currentAcademicYear = $this->orgAcademicYearRepository->getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateString, $organization->getId());

                $newOrgPersonStudentYearObject = false;
                // if the person in question does not have an org person student year column
                if ((!$orgPersonStudentYearObj) && (!$isParticipantSetToZero)) {
                    $newOrgPersonStudentYearObject = true;

                    // create a new one
                    $orgPersonStudentYearObj = new OrgPersonStudentYear();
                    $orgPersonStudentYearObj->setPerson($person);
                    $orgPersonStudentYearObj->setOrganization($organization);
                    $orgPersonStudentYearObj->setOrgAcademicYear($academicYear);

                    // defaults new orgAcademicStudentYear to active
                    $orgPersonStudentYearObj->setIsActive(1);

                    // Send referral related emails as student made participating
                    $mapworksAction = 'student_made_participant';
                    $communicationsSent = $this->referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($person, $organizationId, $mapworksAction, $loggedInPerson, true);
                    if (!$communicationsSent) {
                        $errors[$id][] = [
                            'name' => "Participating",
                            'value' => '',
                            'errors' => [
                                "Communication failed for notifying faculty with active referrals on the student"
                            ]
                        ];
                    }
                }
                // is current year check
                if ($orgPersonStudentYearObj && ($currentAcademicYear[0]['org_academic_year_id'] == $orgPersonStudentYearObj->getOrgAcademicYear()->getId())) {
                    $isCurrentAcademicYear = 1;
                }

                // set the isActive column if there is not an error
                if ($orgPersonStudentYearObj && (!is_null($updateIsActiveColumn))) {
                    $orgPersonStudentYearObj->setIsActive($data[$lowerCaseIsActiveFieldName]);
                }

                // if the column has stated that they do not want to add the student
                // as a participant for the given year
                if ($orgPersonStudentYearObj && ($isParticipant === "0" || $isParticipant === 0)) {
                    $orgPersonStudentYearObj->setDeletedAt(new \DateTime('now'));

                }

                // This allows a new row to be added but will not add a deleted row
                // we need to not add rows that are new and will have a deleted at value
                // do they are new and are not participate
                if ((!$newOrgPersonStudentYearObject && $orgPersonStudentYearObj) || $isParticipant) {
                    $this->orgPersonStudentYearRepository->persist($orgPersonStudentYearObj);
                }

                if (($isParticipant === "0" || $isParticipant === 0) && $isCurrentAcademicYear) {
                    $this->cancelUpcomingAppointmentsForStudent($person);

                    // Send referral related emails as student made non-participant
                    $mapworksAction = 'student_made_nonparticipant';
                    $communicationsSent = $this->referralService->sendCommunicationsRelatedToReferralsUponStudentParticipationStatusUpdate($person, $organizationId, $mapworksAction, $loggedInPerson, true);
                    if (!$communicationsSent) {
                        $errors[$id][] = [
                            'name' => "Participating",
                            'value' => '',
                            'errors' => [
                                "Communication failed for notifying faculty with active referrals on the student"
                            ]
                        ];
                    }
                }
            }


            // Retention group Check starts here

            $retentionTrackVariable = "RetentionTrack";
            $lowercaseRetentionTrackFieldName = strtolower($retentionTrackVariable);

            // check if the RetentionTrack field is present and is not empty
            if (isset($data[$lowercaseRetentionTrackFieldName]) && trim($data[$lowercaseRetentionTrackFieldName]) != "") {
                $retentionTrackValue = $data[$lowercaseRetentionTrackFieldName];
                $isRetentionVariableValueValid = $validator->validateRetentionVariable($retentionTrackVariable, $retentionTrackValue, $academicYear);
                if (!$isRetentionVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $retentionTrackVariable,
                        'value' => '',
                        'errors' => $validator->getErrors()
                    ];

                } else {
                    $orgPersonStudentRetentionTrackingGroup = $this->orgPersonStudentRetentionTrackingGroupRepository->findOneBy([
                        'organization' => $organization,
                        'person' => $person,
                        'orgAcademicYear' => $academicYear
                    ]);

                    if ($orgPersonStudentRetentionTrackingGroup && ($retentionTrackValue == 0 || $retentionTrackValue == "0")) {
                        // if retention tracking value is 0 then set deleted at value , else do not make any change
                        $orgPersonStudentRetentionTrackingGroup->setDeletedAt(new \DateTime('now'));
                        $this->orgPersonStudentRetentionTrackingGroupRepository->flush();
                    } else if (!$orgPersonStudentRetentionTrackingGroup && ($retentionTrackValue == 1 || $retentionTrackValue == "1")) {
                        $orgPersonStudentRetentionTrackingGroup = new OrgPersonStudentRetentionTrackingGroup();
                        $orgPersonStudentRetentionTrackingGroup->setPerson($person);
                        $orgPersonStudentRetentionTrackingGroup->setOrgAcademicYear($academicYear);
                        $orgPersonStudentRetentionTrackingGroup->setOrganization($organization);
                        $this->orgPersonStudentRetentionTrackingGroupRepository->persist($orgPersonStudentRetentionTrackingGroup);
                    }
                }
            }

            $enrolledAtBeginningOfAcademicYearVariable = "EnrolledAtBeginningOfAcademicYear";
            $lowercaseEnrolledAtBeginningOfAcademicYearVariable = strtolower($enrolledAtBeginningOfAcademicYearVariable);
            $isEnrolledAtBeginningOfAcademicYearVariableValueValid = false;
            if (isset($data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable]) && trim($data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable]) != "") {
                $enrolledAtBeginningOfAcademicYearVariableValue = $data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable];
                $isEnrolledAtBeginningOfAcademicYearVariableValueValid = $validator->validateRetentionVariable($enrolledAtBeginningOfAcademicYearVariable, $enrolledAtBeginningOfAcademicYearVariableValue, $academicYear);
                if (!$isEnrolledAtBeginningOfAcademicYearVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $enrolledAtBeginningOfAcademicYearVariable,
                        'value' => '',
                        'errors' => $validator->getErrors()
                    ];
                    $enrolledAtBeginningOfAcademicYearVariableValue = null;
                }
            } else {
                $enrolledAtBeginningOfAcademicYearVariableValue = null;
            }


            $enrolledAtMidpointOfAcademicYearVariable = "EnrolledAtMidpointOfAcademicYear";
            $lowercaseEnrolledAtMidpointOfAcademicYearVariable = strtolower($enrolledAtMidpointOfAcademicYearVariable);
            $isEnrolledAtMidpointOfAcademicYearVariableValueValid = false;
            if (isset($data[$lowercaseEnrolledAtMidpointOfAcademicYearVariable]) && trim($data[$lowercaseEnrolledAtMidpointOfAcademicYearVariable]) != "") {
                $enrolledAtMidpointOfAcademicYearVariableValue = $data[$lowercaseEnrolledAtMidpointOfAcademicYearVariable];
                $isEnrolledAtMidpointOfAcademicYearVariableValueValid = $validator->validateRetentionVariable($enrolledAtMidpointOfAcademicYearVariable, $enrolledAtMidpointOfAcademicYearVariableValue, $academicYear);
                if (!$isEnrolledAtMidpointOfAcademicYearVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $enrolledAtMidpointOfAcademicYearVariable,
                        'value' => '',
                        'errors' => $validator->getErrors()
                    ];
                    $enrolledAtMidpointOfAcademicYearVariableValue = null;
                }
            } else {
                $enrolledAtMidpointOfAcademicYearVariableValue = null;
            }

            $completedADegreeVariable = "CompletedADegree";
            $lowercaseCompletedADegreeVariable = strtolower($completedADegreeVariable);
            $isCompletedADegreeVariableValueValid = false;
            if (isset($data[$lowercaseCompletedADegreeVariable]) && trim($data[$lowercaseCompletedADegreeVariable]) != "") {
                $completedADegreeVariableValue = $data[$lowercaseCompletedADegreeVariable];
                $isCompletedADegreeVariableValueValid = $validator->validateRetentionVariable($completedADegreeVariable, $completedADegreeVariableValue, $academicYear);
                if (!$isCompletedADegreeVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $completedADegreeVariable,
                        'value' => '',
                        'errors' => $validator->getErrors()
                    ];
                    $completedADegreeVariableValue = null;
                }
            } else {
                $completedADegreeVariableValue = null;
            }


            // if any of the the retention values have valid values , update them to database
            if ($isCompletedADegreeVariableValueValid || $isEnrolledAtMidpointOfAcademicYearVariableValueValid || $isEnrolledAtBeginningOfAcademicYearVariableValueValid) {
                $this->processRetentionVariables($academicYear, $person, $organization, $enrolledAtBeginningOfAcademicYearVariableValue, $enrolledAtMidpointOfAcademicYearVariableValue, $completedADegreeVariableValue);
            }


            // Checking survey cohort for the student that will be updated
            if (isset($data['surveycohort']) && trim($data['surveycohort']) !== '') {

                // I need to check the surveyCohort values
                // first I need to make sure that $yearId is set
                $yearId = trim($data['yearid']);

                // if year id is set
                if ($yearId) {
                    // get the academic Year
                    $academicYear = $this->orgAcademicYearRepository->findOneBy([
                        'organization' => $organization,
                        'yearId' => $data['yearid']
                    ]);
                    if ($academicYear) {

                        // does the surveyCohort have errors?
                        if ($this->doesSurveyCohortHaveErrors($data)) {
                            $errors = $this->setError($errors, $id, 'SurveyCohort',
                                '', "Survey Cohort must be an integer between the numbers of 1 and 4");
                        } else {
                            // does the student have a surveyCohort?
                            $orgPersonStudentCohort = $orgPersonStudentCohortRepository->findOneBy(['person' => $person, 'orgAcademicYear' => $academicYear]);

                            if ($orgPersonStudentCohort) {

                                $currentStudentCohort = $orgPersonStudentCohort->getCohort();

                                if (!empty($currentStudentCohort)) {
                                    // Check to see if the cohort the student is attempting to be put into has a survey launched.
                                    $launchedAndClosedSurveysForCohort = $wessLinkRepository->getSurveysForCohortAndYear($organizationId, $currentStudentCohort, $yearId);
                                    if (count($launchedAndClosedSurveysForCohort) > 0) {
                                        if ($currentStudentCohort != trim($data['surveycohort'])) {
                                            $errors = $this->setError($errors, $id, 'SurveyCohort', '',
                                                'cohort not updated: already set to ' . $orgPersonStudentCohort->getCohort() . ' and a survey has already launched for this year.');
                                        }
                                    } else {
                                        // update the student's cohort if the existing cohort does not have launched surveys
                                        $unchangedFlag = false;
                                        $orgPersonStudentCohort->setCohort((int)trim($data['surveycohort']));
                                    }

                                } else {
                                    // update the student's cohort if the existing cohort value from the DB is NULL
                                    $unchangedFlag = false;
                                    $orgPersonStudentCohort->setCohort((int)trim($data['surveycohort']));
                                }
                            } else {

                                // create a new org person student cohort
                                $unchangedFlag = false;
                                $orgPersonStudentCohort = new OrgPersonStudentCohort;
                                $orgPersonStudentCohort->setOrganization($organization);
                                $orgPersonStudentCohort->setPerson($person);
                                $orgPersonStudentCohort->setOrgAcademicYear($academicYear);
                                $orgPersonStudentCohort->setCohort((int)trim($data['surveycohort']));
                                $orgPersonStudentCohortRepository->persist($orgPersonStudentCohort);
                            }

                        }
                    } else { // Year id does not map to a academic year
                        // Year id must map to an academicYear in order
                        // to update SurveyCohort, tell the user that

                        // does SurveyCohort have Errors?
                        if ($this->doesSurveyCohortHaveErrors($data)) {
                            $errors = $this->setError($errors, $id, 'SurveyCohort',
                                '', "Survey Cohort must be an integer between the numbers of 1 and 4");
                        }
                        $surveyCohort = null;
                        $academicYear = null;
                    }
                } else { //Yearid is not set

                    // Year id must be set in order to set the SurveyCohort
                    // tell the user that
                    $errors = $this->setError($errors, $id, 'YearId', '',
                        "YearId is required in order to set the SurveyCohort");
                    // going the extra mile an checking survey cohort despite not
                    // adding into the db
                    if ($this->doesSurveyCohortHaveErrors($data)) {
                        $errors = $this->setError($errors, $id, 'SurveyCohort',
                            '', "Survey Cohort must be an integer between the numbers of 1 and 4");
                    }
                    $surveyCohort = null;
                    $academicYear = null;
                }
            }

            // Validate receive survey for different surveys. The below method is common and is present in the studentBase abstract class extended above
            $this->processReceiveSurvey($data, $errors, $id, $organization, $person, "update");


            $primaryConnectValue = (isset($data['primaryconnect'])) ? $data['primaryconnect'] : NULL;
            if (!$orgPersonStudent = $orgPersonStudentRepository->findOneByPerson($person)) {

                $orgPersonStudent = new OrgPersonStudent();
                $orgPersonStudent->setOrganization($person->getOrganization());
                $orgPersonStudent->setPerson($person);
                if ($studentPhotoURL || is_null($studentPhotoURL)) {
                    $studentPhotoURL = is_null($studentPhotoURL) ? null : $studentPhotoURL;
                    $orgPersonStudent->setPhotoUrl($studentPhotoURL);
                }

                if (isset($academicYear) && (isset($surveyCohort) || $surveyCohortSetEmpty)) {
                    $orgPersonStudent->setSurveyCohort($surveyCohort);
                }
                if (isset($primaryConnectValue)) {
                    $primaryConnect = $personService->findOneByExternalIdOrg($primaryConnectValue, $organizationId);
                    $orgPersonStudent->setPersonIdPrimaryConnect($primaryConnect);
                }
                $orgPersonStudent->setAuthKey($personService->generateAuthKey($person->getExternalId(), 'Student'));
                $orgPersonStudent->setStatus(1); //  This line should be removed once we delete the status col from org_person_student table
                $orgPersonStudentRepository->persist($orgPersonStudent);

                $createdStudentFlag = true;
            } else {

                $orgPersonStudent->setOrganization($person->getOrganization());
                $orgPersonStudent->setPerson($person);

                if ($studentPhotoURL || is_null($studentPhotoURL)) {
                    if ($orgPersonStudent->getPhotoUrl() != $studentPhotoURL) {
                        $unchangedFlag = false;
                    }
                    $studentPhotoURL = is_null($studentPhotoURL) ? null : $studentPhotoURL;
                    $orgPersonStudent->setPhotoUrl($studentPhotoURL);
                }
                if (isset($academicYear) && (isset($surveyCohort) || $surveyCohortSetEmpty)) {
                    if ($orgPersonStudent->getSurveyCohort() != $surveyCohort) {
                        $unchangedFlag = false;
                    }
                    $orgPersonStudent->setSurveyCohort($surveyCohort);
                }
                if (isset($primaryConnectValue) && !empty($primaryConnectValue)) {
                    if ($primaryConnectValue == "#clear") {
                        $orgPersonStudent->setPersonIdPrimaryConnect(null);
                    } else {
                        $primaryConnect = $this->personRepository->findOneBy([
                            'externalId' => $primaryConnectValue,
                            'organization' => $organizationId
                        ]);
                        if ($primaryConnect) {

                            $primaryConnectOrgPersonFacultyEntity = $this->orgPersonFacultyRepository->findOneBy([
                                'person' => $primaryConnect
                            ]);
                            if ($primaryConnectOrgPersonFacultyEntity && !is_null($primaryConnectOrgPersonFacultyEntity->getStatus()) && $primaryConnectOrgPersonFacultyEntity->getStatus() == 0) {
                                $errors[$id][] = [
                                    'name' => 'PrimaryConnect',
                                    'value' => '',
                                    'errors' => [
                                        "Could not set Primary Connection because the faculty/staff is marked as inactive."
                                    ]
                                ];
                            } else {
                                $isPrimaryConnect = $validator->validatePrimaryConnect($person->getId(), $primaryConnect->getId(), $organizationId, $currentDateOrgTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));
                                if ($isPrimaryConnect) {
                                    if ($orgPersonStudent->getPersonIdPrimaryConnect() != $primaryConnect) {
                                        $unchangedFlag = false;
                                    }
                                    $orgPersonStudent->setPersonIdPrimaryConnect($primaryConnect);
                                } else {
                                    $errors[$id][] = [
                                        'name' => 'PrimaryConnect',
                                        'value' => '',
                                        'errors' => [
                                            "Could not add as Primary Connection. Faculty is not connected to the student."
                                        ]
                                    ];
                                }
                            }
                        } else {
                            $errors[$id][] = [
                                'name' => 'PrimaryConnect',
                                'value' => '',
                                'errors' => [
                                    "Primary Connection not assigned. Faculty does not exist."
                                ]
                            ];
                        }
                    }
                }
            }


            // Add to System Group
            $studentSystemGroup = $orgGroupStudentsRepository->findOneBy(['organization' => $organization, 'person' => $person, 'orgGroup' => $systemGroup]);
            if (!$studentSystemGroup) {
                $orgGroupStudents = new OrgGroupStudents();
                $orgGroupStudents->setOrganization($organization);
                $orgGroupStudents->setPerson($person);
                $orgGroupStudents->setOrgGroup($systemGroup);
                $orgGroupStudentsRepository->persist($orgGroupStudents);
                $unchangedFlag = false;
            }

            $riskCalculationRepository = $repositoryResolver->getRepository("SynapseRiskBundle:OrgRiskvalCalcInputs");
            $orgRiskValCalcInputs = $riskCalculationRepository->findOneBy(array(
                'org' => $person->getOrganization()->getId(),
                'person' => $person->getId()
            ));

            if (!$orgRiskValCalcInputs) {
                $riskCalculationInputDto = new RiskCalculationInputDto();
                $riskCalculationInputDto->setPersonId($person->getId());
                $riskCalculationInputDto->setOrganizationId($person->getOrganization()->getId());
                $riskCalculationInputDto->setIsRiskvalCalcRequired('n');
                $result = $riskCalculationService->createRiskCalculationInput($riskCalculationInputDto);
                $unchangedFlag = false;
            } else {
                $currentDateTime = new \DateTime('now');
                $orgRiskValCalcInputs->setModifiedAt($currentDateTime);
                $riskCalculationRepository->update($orgRiskValCalcInputs);
                $riskCalculationRepository->flush();
            }
            if (isset($data['riskgroupid']) && !empty($data['riskgroupid'])) {

                $riskGroup = $riskGroupRepository->findOneById($data['riskgroupid']);
                if ($riskGroup) {

                    // Check Risk Group Mapped to Organization or not
                    $orgRiskGroupModel = $this->orgRiskGroupModelRepository->findOneBy([
                        'org' => $organizationId,
                        'riskGroup' => $data['riskgroupid']
                    ]);

                    if (!$orgRiskGroupModel) {
                        $errors[$id][] = [
                            'name' => 'RiskGroupId',
                            'value' => '',
                            'errors' => [
                                "Risk Group does not exist."
                            ]
                        ];
                    } else {
                        $currentDateTime = new \DateTime('now');

                        $riskModel = $orgRiskGroupModel->getRiskModel();

                        $isEnrollmentDateValid = true;

                        if (isset($riskModel)) {
                            $riskModelEnrollmentDeadline = $riskModel->getEnrollmentDate();
                            if ($riskModelEnrollmentDeadline < $currentDateTime) {
                                $isEnrollmentDateValid = false;
                            }
                        }

                        $studentRiskGroup = $this->riskGroupPersonHistoryRepository->getStudentRiskGroup($person, $riskGroup);
                        if ($studentRiskGroup) {

                            // If user trying to update the same group no need to trigger error
                            if ($riskGroup->getId() != $studentRiskGroup[0]['riskGroupId']) {
                                $errors[$id][] = [
                                    'name' => 'RiskGroupId',
                                    'value' => '',
                                    'errors' => [
                                        "RiskGroupId already assigned. Contact support if changes need to be made."
                                    ]
                                ];
                            }
                        } else if ($isEnrollmentDateValid) {
                            try {

                                $riskGroupPersonHistory = new RiskGroupPersonHistory();

                                $riskGroupPersonHistory->setPerson($person);
                                $riskGroupPersonHistory->setRiskGroup($riskGroup);
                                $riskGroupPersonHistory->setAssignmentDate($currentDateTime);
                                $this->riskGroupPersonHistoryRepository->persist($riskGroupPersonHistory);
                            } catch (\Exception $e) {

                                continue;
                            }
                            $unchangedFlag = false;
                        } else {

                            // the enrollment date is not valid record an error
                            $errors[$id][] = [
                                'name' => 'RiskGroupId',
                                'value' => '',
                                'errors' => [
                                    "Student risk enrollment end date has passed."
                                ]
                            ];
                        }
                    }
                } else {
                    $errors[$id][] = [
                        'name' => 'RiskGroupId',
                        'value' => '',
                        'errors' => [
                            "Invalid RiskGroupId, please see Risk Setup page."
                        ]
                    ];
                }
            }
            if ($createdStudentFlag) {
                $createdStudentRows++;
            } else {
                if ($unchangedFlag) {
                    $unchangedStudentRows++;
                } else {
                    $updatedStudentRows++;
                }
            }
            $validRows++;
            $receiveSurvey = null;
            $surveyCohort = null;
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdStudentRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, ($updatedStudentRows + $unchangedStudentRows));
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

    private function get_http_response_code($url)
    {
        try {
            $headers = get_headers($url);
        } catch (\Exception $e) {
            return false;
        }

        $fileInfo = array();
        $fileInfo[UploadConstant::STATUS] = substr($headers[0], 9, 3);
        if ($fileInfo[UploadConstant::STATUS] == 200) {
            $imgInfo = getimagesize($url);
            $fileInfo["type"] = $imgInfo['mime'];
            return $fileInfo;
        } else {
            return false;
        }
    }


    /**
     * set an error for the row within the upload file
     *
     * @param $errors => array for keeping track of errors in the file
     * @param $id => the row number
     * @param $name => the column name of the header
     * @param $value => the value of the error
     * @param $message => message to be displayed to the user
     * @return mixed => sends back the error array
     */
    private function setError($errors, $id, $name, $value, $message)
    {
        $errors[$id][] = [
            'name' => $name,
            UploadConstant::VALUE => $value,
            UploadConstant::ERRORS => [
                $message
            ]
        ];
        return $errors;
    }

    /**
     * Checks the row of the datafile to see if the
     * surveycohort has an error in it. returns true
     * if there is an error, false otherwise
     *
     * @param $data => the row that is to be checked
     * @return bool
     */
    private function doesSurveyCohortHaveErrors($data)
    {
        // if surveyCohort is not between the correct numbers than
        // it is invalid so return true else false
        $surveyCohort = $data['surveycohort'];

        // double checking decimals and if the string is less than 1 or greater than 4
        if (strpos($surveyCohort, '.') !== false || strpos($surveyCohort, ',') !== false || $surveyCohort < 1 || $surveyCohort > 4) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * cancels the appointments for the given student and send mail to the faculty
     *
     * @param Person $person
     *
     */
    private function cancelUpcomingAppointmentsForStudent($person)
    {
        $studentId = $person->getId();
        $currentDate =  new \DateTime('now');

        // find the list of upcoming appointments for the student
        $appointmentList = $this->appointmentRecipientAndStatusRepository->getStudentsUpcomingAppointments($studentId, $currentDate->format('Y-m-d'));
        $appointmentIdsArray = [];
        foreach($appointmentList  as $appointment){

            $appointmentIdsArray[] = $appointment['appointment_id']; // collecting the appointment ids of the student

            $appointmentId = $appointment['appointment_id'];
            $facultyId = $appointment['personId'];

            $criteria = [
                'personIdFaculty'=> $facultyId,
                'personIdStudent' => $studentId,
                'appointments' =>  $appointmentId
            ];

            $appointmentRecipient = $this->appointmentRecipientAndStatusRepository->findOneBy($criteria);

            if($appointmentRecipient) {
                $this->appointmentRecipientAndStatusRepository->delete($appointmentRecipient); // cancelling the appointment for the archived student
                $activityLog = $this->activityLogRepository->findOneBy($criteria);
                if($activityLog){
                    $this->activityLogRepository->delete($activityLog); // removing it from the activity log
                }
                $this->removeGoogleEvent($appointmentId, $studentId, $facultyId);
                $appointmentDetails = $this->appointmentRepository->findOneById($appointmentId);
                $this->sendCancellationMail($appointmentDetails, $facultyId, $studentId);
            }
        }
        // now check if there are any other attendees for the appointment, if not delete the appointment , else do nothing.
        foreach($appointmentIdsArray as $appointmentId){
            $appointmentExists =  $this->appointmentRecipientAndStatusRepository->findOneBy([ 'appointments' =>  $appointmentId, 'deletedAt' => NULL ]);

            if(!$appointmentExists){
                $appointmentEntity = $this->appointmentRepository->findOneById($appointmentId);
                $this->appointmentRepository->delete($appointmentEntity);
            }
        }
    }

    /**
     * Method to  send cancellation mail to faculty and student  once the student is archived.
     * @param $appointmentDetails
     * @param $facultyId
     * @param $studentId
     */
    private function sendCancellationMail($appointmentDetails, $facultyId, $studentId)
    {

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $personRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $ebiConfigRepository = $repositoryResolver->getRepository('SynapseCoreBundle:EbiConfig');
        $dateUtlityService = $this->getContainer()->get('date_utility_service');
        $organizationService = $this->getContainer()->get('org_service');
        $emailService = $this->getContainer()->get('email_service');

        $appointmentStartTime = $appointmentDetails->getStartDateTime();
        $appointmentEndTime = $appointmentDetails->getEndDateTime();

        $organizationId = $appointmentDetails->getOrganization()->getId();

        $appointmentStartTime = $dateUtlityService->adjustDateTimeToOrganizationTimezone($organizationId, $appointmentStartTime);
        $appointmentEndTime = $dateUtlityService->adjustDateTimeToOrganizationTimezone($organizationId, $appointmentEndTime);

        $tokenValues['app_datetime'] = $appointmentStartTime->format('m/d/Y h:ia') . " to " . $appointmentEndTime->format('m/d/Y h:ia');

        $staffObj = $personRepository->findOneById($facultyId);
        $studentObj = $personRepository->findOneById($studentId);
        $tokenValues['staff_email'] = $staffObj->getUsername();
        $tokenValues['student_email'] = $studentObj->getUsername();
        $tokenValues['student_name'] = $studentObj->getFirstname() . " " . $studentObj->getLastname();
        $tokenValues['staff_name'] = $staffObj->getFirstname() . " " . $staffObj->getLastname();

        $systemUrlConfigItem = $ebiConfigRepository->findOneBy(['key' => 'System_URL']);
        $tokenValues['Skyfactor_Mapworks_logo'] = "";
        // Set the path for SkyFactor Logo.
        if ($systemUrlConfigItem) {
            $systemUrl = $systemUrlConfigItem->getValue();
            $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . SynapseConstant::SKYFACTOR_LOGO_IMAGE_PATH;
        }

        $staffEmailKey = "Archived_Cancel_Appointment_Staff";
        $studentEmailKey = "Archived_Cancel_Appointment_Student";

        $organizationLang = $organizationService->getOrganizationDetailsLang($organizationId);
        $orgLangId = $organizationLang->getLang()->getId();


        //SENDING MAIL TO STAFF
        $emailDetails = $this->sendMailDetails($staffEmailKey, $orgLangId, $organizationId, $staffObj, $tokenValues);
        $email = $emailService->sendEmailNotification($emailDetails);
        $emailService->sendEmail($email);

        //SENDING MAIL TO STUDENT
        $emailDetails = $this->sendMailDetails($studentEmailKey, $orgLangId, $organizationId, $studentObj, $tokenValues);
        $email = $emailService->sendEmailNotification($emailDetails);
        $emailService->sendEmail($email, false, null, true);
    }

    /**
     * creating to be send mail  details array
     * @param $emailKey
     * @param $orgLangId
     * @param $organizationId
     * @param $personObj
     * @param $tokenValues
     * @return array
     */
    private function sendMailDetails($emailKey, $orgLangId, $organizationId, $personObj, $tokenValues)
    {

        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $emailTemplateLangRepository = $repositoryResolver->getRepository("SynapseCoreBundle:EmailTemplateLang");
        $emailTemplate = $emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $orgLangId);
        $emailBody = $emailTemplate->getBody();
        $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
        $subject = $emailTemplate->getSubject();
        $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
        $emailBody = $this->replaceTokenValues($emailBody, $tokenValues);

        return [
            'from' => $from,
            'subject' => $subject,
            'bcc' => $bcc,
            'body' => $emailBody,
            'to' => $personObj->getUsername(),
            'emailKey' => $emailKey,
            'organizationId' => $organizationId
        ];

    }

    /**
     * Replacing token values in string
     * @param string $message
     * @param  array $tokenValues
     * @return string
     */
    private function replaceTokenValues($message, $tokenValues)
    {
        preg_match_all('/\\$\$(.*?)\$\$/', $message, $tokenArrays);
        $tokenArray = $tokenArrays[0];
        $tokenKeys = $tokenArrays[1];
        for ($tokenCount = 0; $tokenCount < count($tokenArray); $tokenCount++) {
            if (isset($tokenValues[$tokenKeys[$tokenCount]])) {
                $message = str_replace($tokenArray[$tokenCount], $tokenValues[$tokenKeys[$tokenCount]], $message);
            }
        }
        return $message;
    }

    /**
     * Function to remove Google event
     *
     * @param int $appointmentId
     * @param int $facultyId
     */
    private function removeGoogleEvent($appointmentId, $facultyId)
    {
        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $calendarIntegrationService = $this->getContainer()->get(CalendarIntegrationService::SERVICE_KEY);

        $appointmentRepository = $repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);

        $appointmentDetails = $appointmentRepository->findOneById($appointmentId);
        $appointmentExists = $this->appointmentRecipientAndStatusRepository->findOneBy(['appointments' => $appointmentId, 'deletedAt' => NULL]);

        $organizationId = $appointmentDetails->getOrganization()->getId();
        if ($appointmentExists) {
            $calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointmentId, 'appointment', 'update');
        } else {
            $externalCalendarEventId = $appointmentDetails->getGoogleAppointmentId();
            if ($externalCalendarEventId) {
                $calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $appointmentId, 'appointment', 'delete', $externalCalendarEventId);
            }
        }
    }
}
