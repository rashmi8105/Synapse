<?php
namespace Synapse\UploadBundle\Job;

use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\OrgGroupStudents;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Entity\OrgPersonStudentCohort;
use Synapse\CoreBundle\Entity\OrgPersonStudentYear;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\PersonEbiMetadata;
use Synapse\CoreBundle\Entity\PersonOrgMetadata;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\EbiMetadataRepository;
use Synapse\CoreBundle\Repository\EntityRepository;
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
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\EntityService;
use Synapse\CoreBundle\Service\Impl\GroupService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\ReportsBundle\Entity\OrgPersonStudentRetentionTrackingGroup;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use Synapse\RiskBundle\Entity\RiskGroupPersonHistory;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;
use Synapse\RiskBundle\Repository\RiskGroupPersonHistoryRepository;
use Synapse\RiskBundle\Repository\RiskGroupRepository;
use Synapse\RiskBundle\Repository\RiskModelMasterRepository;
use Synapse\RiskBundle\Service\Impl\RiskCalculationService;
use Synapse\UploadBundle\Service\Impl\ProfileValidationService;
use Synapse\UploadBundle\Service\Impl\StudentUploadValidatorService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateStudent extends StudentBase
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ContactsRepository
     */
    private $contactRepository;

    /**
     * @var EbiMetadataRepository
     */
    private $ebiMetadataRepository;

    /**
     * @var EntityRepository
     */
    private $entityRepository;
    
    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var GroupService
     */
    private $groupService;

    /**
     * @var RiskCalculationService
     */
    private $riskCalculationService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    /**
     * @var StudentUploadValidatorService
     */
    private $validator;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgMetadataRepository
     */
    private $orgMetadataRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrgPersonStudentCohortRepository
     */
    private $orgPersonStudentCohortRepository;

    /**
     * @var orgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgRiskGroupModelRepository
     */
    private $orgRiskGroupModelRepository;


    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var orgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;
    
    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var RiskGroupRepository
     */
    private $riskGroupRepository;

    /**
     * @var RiskModelMasterRepository
     */
    private $riskModelMasterRepository;

    /**
     * @var RiskGroupPersonHistoryRepository
     */
    private $riskGroupPersonHistoryRepository;

    /**
     * @var personEbiMetadataRepository
     */
    private $personEbiMetadataRepository;

    /**
     * @var  personOrgMetadataRepository
     */
    private $personOrgMetadataRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;



    const FirstName = 'Firstname';
    const LastName = 'Lastname';
    const DateOfBirth = 'DateofBirth';


    public function run($args)
    {

        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];

        $output = new StreamOutput(fopen('php://stdout', 'w'));

        //services

        $this->container = $this->getContainer();
        $cache = $this->container->get(SynapseConstant::REDIS_CLASS_KEY);
        $personService = $this->container->get(PersonService::SERVICE_KEY);
        $profileItemValidationService = $this->container->get(ProfileValidationService::SERVICE_KEY);
        $this->entityService = $this->container->get(EntityService::SERVICE_KEY);
        $this->groupService = $this->container->get(GroupService::SERVICE_KEY);
        $this->riskCalculationService = $this->container->get(RiskCalculationService::SERVICE_KEY);
        $this->uploadFileLogService = $this->container->get(UploadFileLogService::SERVICE_KEY);
        $this->validator = $this->container->get(StudentUploadValidatorService::SERVICE_KEY);

        // repositories
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);

        $this->contactRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->ebiMetadataRepository = $this->repositoryResolver->getRepository(EbiMetadataRepository::REPOSITORY_KEY);
        $this->entityRepository = $this->repositoryResolver->getRepository(EntityRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->orgRiskGroupModelRepository = $this->repositoryResolver->getRepository(OrgRiskGroupModelRepository::REPOSITORY_KEY);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(PersonOrgMetaDataRepository::REPOSITORY_KEY);
        $this->riskGroupPersonHistoryRepository = $this->repositoryResolver->getRepository(RiskGroupPersonHistoryRepository::REPOSITORY_KEY);
        $this->riskGroupRepository = $this->repositoryResolver->getRepository(RiskGroupRepository::REPOSITORY_KEY);
        $this->orgPersonStudentCohortRepository = $this->repositoryResolver->getRepository(OrgPersonStudentCohortRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->riskModelMasterRepository = $this->repositoryResolver->getRepository(RiskModelMasterRepository::REPOSITORY_KEY);


        $studentEntity = $this->entityRepository->findOneBy([
            'name' => 'Student'
        ]);
        $errors = []; // Used for tracking all the errors
        $validRows = 0; // Counter  for the number of valid rows


        $requiredItems = [
            'externalid',
            'firstname',
            'lastname',
            'primaryemail'
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

        //Checking System Group Created Or Not
        //TODO:address graceful failure if the organization does not exist.
        $organization = $this->organizationRepository->find($organizationId);
        $systemGroup = $this->groupService->addSystemGroup('All Students', 'ALLSTUDENTS', $organization);

        foreach ($creates as $id => $data) {

            $errorsAlreadyReported = array();
            $studentPhotoURL = "";

            foreach ($requiredItems as $item) {
                // We wait until the last possible moment to lowercase the required items so we can display camel case column headers to the user
                if (!array_key_exists($item, $data) || empty($data[$item])) {
                    $errorMsg = "$uppercaseItems[$item] is a required field";
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => '',
                        'errors' => [
                            $errorMsg
                        ]
                    ];
                    continue(2);
                }

                if (preg_match("/(^\s+|\s+$|^\s+$)/", $data[$item])) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => $data[$item],
                        'errors' => ["{$uppercaseItems[$item]}: Field can not contain any empty spaces"]
                    ];
                    continue(2);
                }

                if (!$this->validator->validate($item, $data[$item], $organizationId)) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$item],
                        'value' => $data[$item],
                        'errors' => $this->validator->getErrors()
                    ];
                    continue(2);
                }
            }

            // setting academic year and academic term as null by default when we start processing each row.
            $academicYear = null;
            $academicTerm = null;

            // checking if the value provided by the user of YearId in csv , is a valid value or not
            if (!empty($data['yearid'])) {

                $academicYear = $this->validator->validateAcademicYear($organizationId, $data['yearid']);
                // If not a valid academic year report error and skip
                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => 'YearId',
                        'value' => '',
                        'errors' => [
                            'Year Id should be a valid academic year id for the organization'
                        ]
                    ];
                } else {
                    // check if we have valid academic term
                    if (!empty($data['termid'])) {
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
                }
            }

            $person = new Person();
            $contactInfo = new ContactInfo();
            $organization = $this->organizationRepository->find($organizationId);

            foreach (array_intersect_key($data, array_fill_keys(array_merge($personItems, $contactItems), 1)) as $name => $value) {

                // ignore the column if the user tries to clear a value that does not exist yet
                if (trim(strtolower($value)) === '#clear') {
                    continue;
                }

                try {
                    if (isset($value) && !empty($value)) {

                        if (!$this->validator->validate($name, $value, $organizationId)) {

                            if (!in_array(trim($name), $errorsAlreadyReported)) {
                                $errorsAlreadyReported[] = trim($name);
                                $errors[$id][] = [
                                    'name' => $name,
                                    'value' => $value,
                                    'errors' => $this->validator->getErrors()
                                ];
                            }

                            continue;
                        }

                        if (in_array($name, $personItems)) {
                            if ($name == 'dateofbirth') {
                                if ((bool)strtotime($value)) {
                                    $value = new \DateTime($value);
                                } else {
                                    $value = null;
                                }
                            }
                            call_user_func([
                                $person,
                                'set' . $uppercaseItems[$name]
                            ], $value);
                        }

                        if (in_array($name, $contactItems)) {
                            call_user_func([
                                $contactInfo,
                                'set' . $uppercaseItems[$name]
                            ], $value);
                        }

                    }
                } catch (\Exception $e) {
                    $output->writeln('No match for non metadata item: ' . $name);
                }
            }

            //check for valid email id
            if (filter_var($data['primaryemail'], FILTER_VALIDATE_EMAIL) === false) {
                $errorsString = 'This value is not a valid email address.';
                $errors[$id][] = [
                    'name' => "PrimaryEmail",
                    'value' => 'PrimaryEmail',
                    'errors' => [
                        $errorsString
                    ]
                ];
                $output->writeln('Error ----------------------------------------- ' . $errorsString);
                continue;
            }

            //Check is the primary email existing
            $contact = $this->contactRepository->findBy([
                'primaryEmail' => $data['primaryemail']
            ]);
            if (!empty($contact)) {
                $errorsString = ' Primary Email already Exists...';
                $errors[$id][] = [
                    'name' => "",
                    'value' => 'PrimaryEmail',
                    'errors' => [
                        $errorsString
                    ]
                ];
                $output->writeln('Error ----------------------------------------- ' . $errorsString);
                continue;
            }
            $person->setUsername($data['primaryemail']);
            $person->addEntity($studentEntity);
            $person->setOrganization($organization);

            $person = $personService->createPersonRaw($person, $contactInfo);

            foreach ($data as $name => $value) {
                if(!in_array($name, $uppercaseItems)){
                    $uppercaseItems[strtolower($name)] = $name;
                }
                $nonEmptyValue = trim($value);
                if (preg_match("/(^\s+|\s+$|^\s+$)/", $value)) {
                    $errors[$id][] = [
                        'name' => $uppercaseItems[$name],
                        'value' => $value,
                        'errors' => [
                            'Field can not contain any empty spaces'
                        ]
                    ];
                    continue;
                }

                // we do not want to validate again as the contact items are already validated.
                if (in_array($name, $contactItems)) {
                    continue;
                }

                if (isset($value) && strlen($nonEmptyValue) > 0 && !in_array($name, $requiredItems)) {

                    $output->writeln("$name has data, processing");

                    // ignore the column if the user tries to clear a value that does not exist yet
                    if (trim(strtolower($value)) === '#clear') {
                        continue;
                    }

                    //custom validations done here
                    $customValidationErrMsg = $profileItemValidationService->profileItemCustomValidations($uppercaseItems[$name], $value);
                    if (trim($customValidationErrMsg) != "") {
                        $errors[$id][] = [
                            'name' => $name,
                            'value' => $value,
                            'errors' => [
                                $customValidationErrMsg
                            ]
                        ];
                        continue;
                    }
                    //custom validations ends here
                    if (!$this->validator->validate($name, $value, $organizationId)) {
                        if (!in_array(trim($name), $errorsAlreadyReported)) {
                            $errorsAlreadyReported[] = trim($name);

                            $errors[$id][] = [
                                'name' => ucfirst($uppercaseItems[$name]),
                                'value' => $value,
                                'errors' => $this->validator->getErrors()
                            ];
                        }
                    } else {

                        if ($metadata = $this->ebiMetadataRepository->findOneBy(['key' => $name])) {

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
                                        $msg = "";
                                        continue;
                                    }
                                } else {

                                    if (!$academicYear) {
                                        $msg = 'academic year is invalid';
                                    }

                                    if (!$data['yearid']) {
                                        $msg = ' not updated: it is year-based and requires a value for YearID ';
                                    }

                                    if (trim($msg) != "") {
                                        $errors[$id][] = [
                                            'name' => $name,
                                            'value' => $value,
                                            'errors' => [
                                                $msg
                                            ]
                                        ];
                                        $msg = "";
                                        continue;
                                    }
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

                            if ($metadata->getMetadataType() == 'D') {

                                if (strlen(trim($value)) > 0 && date('m/d/Y', strtotime($value))) {
                                    $value = gmdate('m/d/Y', strtotime($value));
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


                            if ($academicYear && ($metadata->getScope() == 'T' || $metadata->getScope() == 'Y')) {
                                $profileValue->setOrgAcademicYear($academicYear);
                            }


                            if ($academicTerm && $metadata->getScope() == 'T') {
                                $profileValue->setOrgAcademicTerms($academicTerm);
                            }
                            $profileValue->setPerson($person);
                            $this->personEbiMetadataRepository->persist($profileValue);

                        } elseif ($metadata = $this->orgMetadataRepository->findOneBy([
                            'metaKey' => $name,
                            'organization' => $organization
                        ])
                        ) {
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
                                        $msg = "";
                                        continue;
                                    }
                                }else {

                                    if (!$academicYear) {
                                        $msg = 'academic year is invalid';
                                    }

                                    if (!$data['yearid']) {
                                        $msg = ' not updated: it is year-based and requires a value for YearID ';
                                    }

                                    if (trim($msg) != "") {
                                        $errors[$id][] = [
                                            'name' => $name,
                                            'value' => $value,
                                            'errors' => [
                                                $msg
                                            ]
                                        ];
                                        $msg = "";
                                        continue;
                                    }
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
                            if ($metadata->getMetadataType() == 'D') {

                                if (strlen(trim($value)) > 0 && date('m/d/Y', strtotime($value))) {
                                    $value = gmdate('m/d/Y', strtotime($value));
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

                            if ($academicYear && ($metadata->getScope() == 'T' || $metadata->getScope() == 'Y')) {
                                $profileValue->setOrgAcademicYear($academicYear);
                            }

                            if ($academicTerm && $metadata->getScope() == 'T') {
                                $profileValue->setOrgAcademicPeriods($academicTerm);
                            }
                            $profileValue->setPerson($person);
                            $this->personOrgMetadataRepository->persist($profileValue);
                        }
                    }
                } else {
                    $output->writeln("$name is empty, skipping");
                }
            }

            if (isset($data['studentphoto']) && strlen(trim($data['studentphoto'])) > 0) {
                $photourl = $data['studentphoto'];
                if (!empty($photourl) && ($this->get_http_response_code($photourl)['status'] == "200" || $photourl == -1 || $photourl == "#clear")) {
                    if ($photourl != -1 && $photourl != "#clear") {
                        if (isset($this->get_http_response_code($photourl)["type"]) && strstr($this->get_http_response_code($photourl)["type"], "image")) {
                            $studentPhotoURL = $photourl;
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
                        $studentPhotoURL = " ";
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
            }


            if (isset($data['surveycohort']) && strlen(trim($data['surveycohort'])) > 0) {
                $surveyCohort = $data['surveycohort'];
                if ($surveyCohort < 1 || $surveyCohort > 4) {
                    $errors[$id][] = [
                        'name' => 'SurveyCohort',
                        'value' => '',
                        'errors' => [
                            "invalid value for cohort."
                        ]
                    ];
                    $surveyCohort = null;


                } elseif (!$data['yearid']) {
                    $errors[$id][] = [
                        'name' => 'SurveyCohort',
                        'value' => '',
                        'errors' => [
                            "yearid is a required filed"
                        ]
                    ];
                    $surveyCohort = null;
                } elseif (!$academicYear) {
                    $errors[$id][] = [
                        'name' => 'SurveyCohort',
                        'value' => '',
                        'errors' => [
                            "Survey Cohort cannot be set without a valid academic year id "
                        ]
                    ];
                    $surveyCohort = null;
                }
            }

            // Removed the receive survey code , processReceiveSurvey method handles the  code to validate  receive survey for different surveys
            // The below method is common and is present the in studentBase abstract class extended above
            $this->processReceiveSurvey($data, $errors, $id, $organization, $person, "create");

            $orgPersonStudent = new OrgPersonStudent();
            $orgPersonStudent->setOrganization($organization);
            $orgPersonStudent->setPerson($person);
            $orgPersonStudent->setStatus(1); //  This line should be removed once we delete the status col from org_person_student table
            if ($studentPhotoURL) {
                $orgPersonStudent->setPhotoUrl($studentPhotoURL);
            }

            if (isset($surveyCohort)) {

                $orgPersonStudentCohort = new OrgPersonStudentCohort();
                $orgPersonStudentCohort->setOrganization($organization);
                $orgPersonStudentCohort->setPerson($person);
                $orgPersonStudentCohort->setOrgAcademicYear($academicYear);
                $orgPersonStudentCohort->setCohort($surveyCohort);
                $this->orgPersonStudentCohortRepository->persist($orgPersonStudentCohort);
            }

            $orgPersonStudent->setAuthKey($personService->generateAuthKey($person->getExternalId(), 'Student'));


            $personEntity = $this->orgPersonStudentRepository->persist($orgPersonStudent);


            $lowercaseParticipatingFieldName = "participating";
            $isParticipating = null;

            // check if the participating field is present and is not empty
            if (isset($data[$lowercaseParticipatingFieldName]) && trim($data[$lowercaseParticipatingFieldName]) != "") {

                $isParticipating = $data[$lowercaseParticipatingFieldName];
                // check if the is participating field is  valid or not, if it is not valid report an error and mark it as active
                if ($data[$lowercaseParticipatingFieldName] !== 1 && $data[$lowercaseParticipatingFieldName] !== "1"
                    && $data[$lowercaseParticipatingFieldName] !== 0 && $data[$lowercaseParticipatingFieldName] !== "0"
                ) {
                    $errors[$id][] = [
                        'name' => "Participating",
                        'value' => '',
                        'errors' => [
                            "Invalid value for Participating column, should be either 0 or 1"
                        ]
                    ];
                    $isParticipating = null;
                }

                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => "Participating",
                        'value' => '',
                        'errors' => [
                            "Participating students need to have a valid year to participate in"
                        ]
                    ];
                }
            }


            //  set student status here.
            $lowerCaseIsActiveFieldName = 'isactive';

            $isActive = null;
            if (isset($data[$lowerCaseIsActiveFieldName]) && trim($data[$lowerCaseIsActiveFieldName]) != "") {
                $isActive = $data[$lowerCaseIsActiveFieldName];

                // check if the is active field is  valid or not, if it is not valid report an error and mark it as active
                if ($data[$lowerCaseIsActiveFieldName] !== 1 && $data[$lowerCaseIsActiveFieldName] !== "1"
                    && $data[$lowerCaseIsActiveFieldName] !== 0 && $data[$lowerCaseIsActiveFieldName] !== "0"
                ) {

                    $errors[$id][] = [
                        'name' => 'IsActive',
                        'value' => '',
                        'errors' => [
                            "Invalid value for IsActive column: should be either 0 or 1"
                        ]
                    ];
                    $isActive = null;
                }
                if (!$academicYear) {
                    $errors[$id][] = [
                        'name' => "IsActive",
                        'value' => '',
                        'errors' => [
                            "Active students need to have a valid year to be active in"
                        ]
                    ];
                }

                // if they are trying to create a student that is active and not participating
                // then throw an error as our system is not set up for a non-participating active student.
                if ((is_null($isParticipating) || $isParticipating == 0) && $isActive == 1) {
                    $errors[$id][] = [
                        'name' => "IsActive",
                        'value' => '',
                        'errors' => [
                            "We cannot make a person active for a year they are not participating in"
                        ]
                    ];
                }

            }

            if ($isParticipating == 1 && $academicYear) {

                $orgPersonStudentYearObj = new OrgPersonStudentYear();
                $orgPersonStudentYearObj->setPerson($person);
                $orgPersonStudentYearObj->setOrganization($organization);
                $orgPersonStudentYearObj->setOrgAcademicYear($academicYear);
                if (!is_null($isActive)) {
                    $orgPersonStudentYearObj->setIsActive($data[$lowerCaseIsActiveFieldName]);
                } else {
                    $orgPersonStudentYearObj->setIsActive(1);
                }
                $this->orgPersonStudentYearRepository->persist($orgPersonStudentYearObj);
            }


            $retentionTrackVariable = "RetentionTrack";
            $lowercaseRetentionTrackFieldName = strtolower($retentionTrackVariable);

            // check if the RetentionTrack field is present and is not empty
            if (isset($data[$lowercaseRetentionTrackFieldName]) && trim($data[$lowercaseRetentionTrackFieldName]) != "") {
                $isRetentionVariableValueValid = $this->validator->validateRetentionVariable($retentionTrackVariable, $data[$lowercaseRetentionTrackFieldName], $academicYear);
                if (!$isRetentionVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $retentionTrackVariable,
                        'value' => '',
                        'errors' => $this->validator->getErrors()
                    ];

                }
                if ($isRetentionVariableValueValid && ($data[$lowercaseRetentionTrackFieldName] === 1 || $data[$lowercaseRetentionTrackFieldName] === "1")) {
                    $orgPersonStudentRetentionTrackingGroup = new OrgPersonStudentRetentionTrackingGroup();
                    $orgPersonStudentRetentionTrackingGroup->setPerson($person);
                    $orgPersonStudentRetentionTrackingGroup->setOrgAcademicYear($academicYear);
                    $orgPersonStudentRetentionTrackingGroup->setOrganization($organization);
                    $this->orgPersonStudentRetentionTrackingGroupRepository->persist($orgPersonStudentRetentionTrackingGroup);
                }
            }

            $enrolledAtBeginningOfAcademicYearVariable = "EnrolledAtBeginningOfAcademicYear";
            $lowercaseEnrolledAtBeginningOfAcademicYearVariable = strtolower($enrolledAtBeginningOfAcademicYearVariable);
            $isEnrolledAtBeginningOfAcademicYearVariableValueValid = false;

            if (isset($data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable]) && trim($data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable]) != "") {
                $enrolledAtBeginningOfAcademicYearVariableValue = $data[$lowercaseEnrolledAtBeginningOfAcademicYearVariable];
                $isEnrolledAtBeginningOfAcademicYearVariableValueValid = $this->validator->validateRetentionVariable($enrolledAtBeginningOfAcademicYearVariable, $enrolledAtBeginningOfAcademicYearVariableValue, $academicYear);
                if (!$isEnrolledAtBeginningOfAcademicYearVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $enrolledAtBeginningOfAcademicYearVariable,
                        'value' => '',
                        'errors' => $this->validator->getErrors()
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
                $isEnrolledAtMidpointOfAcademicYearVariableValueValid = $this->validator->validateRetentionVariable($enrolledAtMidpointOfAcademicYearVariable, $enrolledAtMidpointOfAcademicYearVariableValue, $academicYear);
                if (!$isEnrolledAtMidpointOfAcademicYearVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $enrolledAtMidpointOfAcademicYearVariable,
                        'value' => '',
                        'errors' => $this->validator->getErrors()
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
                $isCompletedADegreeVariableValueValid = $this->validator->validateRetentionVariable($completedADegreeVariable, $completedADegreeVariableValue, $academicYear);
                if (!$isCompletedADegreeVariableValueValid) {
                    $errors[$id][] = [
                        'name' => $completedADegreeVariable,
                        'value' => '',
                        'errors' => $this->validator->getErrors()
                    ];
                    $completedADegreeVariableValue = null;
                }
            } else {
                $completedADegreeVariableValue = null;
            }
            if ($isCompletedADegreeVariableValueValid || $isEnrolledAtMidpointOfAcademicYearVariableValueValid || $isEnrolledAtBeginningOfAcademicYearVariableValueValid) {
                $this->processRetentionVariables($academicYear, $person, $organization, $enrolledAtBeginningOfAcademicYearVariableValue, $enrolledAtMidpointOfAcademicYearVariableValue, $completedADegreeVariableValue);
            }

            //Add student To System Group
            $orgGroupStudents = new OrgGroupStudents();
            $orgGroupStudents->setOrganization($organization);
            $orgGroupStudents->setPerson($person);
            $orgGroupStudents->setOrgGroup($systemGroup);
            $this->orgGroupStudentsRepository->persist($orgGroupStudents);

            // This is used for risk group person history and the primary connection sections
            $currentDateTime = new \DateTime('now');

            // Primary Connection check
            if (isset($data['primaryconnect']) && !empty($data['primaryconnect'])) {
                $primaryConnect = $this->personRepository->findOneBy(['externalId' => $data['primaryconnect'], 'organization' => $organizationId]);
                if ($primaryConnect) {

                    $primaryConnectOrgPersonFacultyEntity = $this->orgPersonFacultyRepository->findOneBy([
                        'person' => $primaryConnect,
                        'organization' => $organizationId
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
                        // Checks to make sure that the primary connect is connected to the student at this time
                        $isPrimaryConnect = $this->validator->validatePrimaryConnect($person->getId(), $primaryConnect->getId(), $organizationId, $currentDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));
                        if ($isPrimaryConnect) {

                            // add the primary connect, then persist
                            $orgPersonStudent->setPersonIdPrimaryConnect($primaryConnect);
                            $personEntity = $this->orgPersonStudentRepository->persist($orgPersonStudent);
                        } else {
                            // throw an error if the faculty is not in the all students group
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
                    // The faculty does not exist within the system
                    $errors[$id][] = [
                        'name' => 'PrimaryConnect',
                        'value' => '',
                        'errors' => [
                            "Primary Connection not assigned. Faculty does not exist."
                        ]
                    ];
                }
            }

            // Integrating Risk Uploads
            //  addding the risk calculation part here as its checked that its a valid risk group Id.

            $riskCalculationInputDto = new RiskCalculationInputDto();
            $riskCalculationInputDto->setPersonId($personEntity->getPerson()->getId());
            $riskCalculationInputDto->setOrganizationId($personEntity->getPerson()->getOrganization()->getId());
            $riskCalculationInputDto->setIsRiskvalCalcRequired('n'); // marking it as n by default, field not being used

            $this->riskCalculationService->createRiskCalculationInput($riskCalculationInputDto);


            //adding the risk calculation part here as its checked that its a valid risk group Id.

            if (isset($data['riskgroupid']) && !empty($data['riskgroupid'])) {
                $riskGroup = $this->riskGroupRepository->find($data['riskgroupid']);
                if ($riskGroup) {

                    //Check Risk Group Mapped to Organization or not
                    $orgRiskGroupModel = $this->orgRiskGroupModelRepository->findOneBy(['org' => $organization, 'riskGroup' => $data['riskgroupid']]);
                    if (!$orgRiskGroupModel) {
                        $errors[$id][] = [
                            'name' => 'RiskGroupId',
                            'value' => '',
                            'errors' => [
                                "Risk Group does not exist."
                            ]
                        ];

                    } else {

                        $isEnrollmentDateValid = true;

                        $riskModel = $orgRiskGroupModel->getRiskModel();

                        if ($riskModel) {
                            $riskModelEnrollmentDeadline = $riskModel->getEnrollmentDate();

                            if ($riskModelEnrollmentDeadline < $currentDateTime) {
                                $errors[$id][] = [
                                    'name' => 'RiskGroupId',
                                    'value' => '',
                                    'errors' => [
                                        "Student risk enrollment end date has passed."
                                    ]
                                ];
                                $isEnrollmentDateValid = false;
                            }
                        }

                        if ($isEnrollmentDateValid) {
                            $riskGroupPersonHistory = new RiskGroupPersonHistory();
                            $riskGroupPersonHistory->setPerson($person);
                            $riskGroupPersonHistory->setRiskGroup($riskGroup);
                            $riskGroupPersonHistory->setAssignmentDate($currentDateTime);
                            $this->riskGroupPersonHistoryRepository->persist($riskGroupPersonHistory);
                            $this->personRepository->flush();
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
            $validRows++;
            $receiveSurvey = null;
            $surveyCohort = null;
        }

        $this->uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $this->uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $this->uploadFileLogService->updateErrorRowCount($uploadId, count($errors));


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
     * Function to validate image headers
     * @deprecated - Please validate using URLUtilityService::validatePhotoURL
     * @param string $url
     * @return array|bool
     */
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
        }
