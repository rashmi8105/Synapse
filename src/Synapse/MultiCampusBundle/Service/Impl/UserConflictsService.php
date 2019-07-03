<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\CoreBundle\Util\Constants\UsersConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\MultiCampusBundle\EntityDto\ConflictDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictPersonDetailsDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictResponseDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictsByCategoryDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictsSummaryDto;
use Synapse\MultiCampusBundle\EntityDto\DestinationOrgDto;
use Synapse\MultiCampusBundle\EntityDto\SourceOrgDto;
use Synapse\MultiCampusBundle\Repository\OrgConflictRepository;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("user_conflicts_service")
 */
class UserConflictsService extends ConflictsServiceHelper
{

    const SERVICE_KEY = 'user_conflicts_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    // Repositories

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    /**
     * @var OrgConflictRepository
     */
    private $orgConflictRepository;

    /**
     * @var OrganizationlangRepository
     */
    private $organizationlangRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);

        // Repositories
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->emailTemplateRepository = $this->repositoryResolver->getRepository(EmailTemplateRepository::REPOSITORY_KEY);
        $this->orgConflictRepository = $this->repositoryResolver->getRepository(OrgConflictRepository::REPOSITORY_KEY);
        $this->organizationlangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    public function listConflicts($sourceId = '', $destinationId = '', $viewmode)
    {
        if ($sourceId == '' && $destinationId == '' && $viewmode == 'json') {
            $conflicts = $this->listConflictTable();
        } else {
            $conflicts = $this->listConflictsUsers($sourceId, $destinationId, $viewmode);
        }
        return $conflicts;
    }

    public function usersConflictCSV($studentSummary, $facultySummary, $hybridSummary)
    {
        $csvHeader = [
            'CREATED ON',
            'FIRST NAME',
            'LAST NAME',
            'EMAIL',
            'CAMPUS ID',
            'USER ID'
        ];
        
        $currentDate = time();
        $fh = @fopen("data://roaster_uploads/1-list-user-conflict-details-roaster.csv", 'w');
        fputcsv($fh, $csvHeader);
        $rows = [];
        $summaryDetailsCsv = $this->conflictListCSV($studentSummary, $facultySummary, $hybridSummary);
        if (isset($summaryDetailsCsv) && count($summaryDetailsCsv) > 0) {
            foreach ($summaryDetailsCsv as $detail) {
                fputcsv($fh, $detail);
            }
        }
        fclose($fh);
    }

    public function listConflictTable()
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $userConflicts = $this->orgConflictRepo->getConflictsUserAccount();
        $conflictDto = new ConflictDto();
        $conflictArray = array();
        foreach ($userConflicts as $userConflict) {
            $conflictResponseDto = new ConflictResponseDto();
            $conflictResponseDto->setCountConflicts($userConflict['conflictCount'] / 2);
            $conflictResponseDto->setConflictsDate($userConflict['createdAt']);
            $sourceOrgArray = array();
            $sourceOrgDto = new SourceOrgDto();
            $sourceOrgDto->setType('solo');
            $sourceOrgDto->setCampusId($userConflict['sourceOrganization']);
            $campus = $this->tierDetailsRepository->findOneBy([
                'organization' => $userConflict['sourceOrganization']
            ]);
            $sourceOrgDto->setCampusName($campus->getOrganizationName());
            $sourceOrgArray[] = $sourceOrgDto;
            $conflictResponseDto->setSourceOrg($sourceOrgArray);
            $destinationOrgArray = array();
            $destinationOrgDto = new DestinationOrgDto();
            $destinationOrgDto->setType('hierarchy');
            $tierHierarchy = call_user_func_array(TierConstant::FUNC_ARRAY_MERGE, $this->tierRepository->getHierarchyOrder($userConflict['destinationOrganization']));
            $destinationOrgDto->setPrimaryTierName($tierHierarchy['primaryName']);
            $destinationOrgDto->setPrimaryTierId($tierHierarchy['primaryId']);
            $destinationOrgDto->setSecondaryTierName($tierHierarchy['secondaryName']);
            $destinationOrgDto->setSecondaryTierId($tierHierarchy['secondaryId']);
            $destinationOrgDto->setCampusName($tierHierarchy['campusName']);
            $destinationOrgDto->setCampusId($tierHierarchy[TierConstant::CAMPUSID]);
            $destinationOrgArray[] = $destinationOrgDto;
            $conflictResponseDto->setDestinationOrg($destinationOrgArray);
            $conflictArray[] = $conflictResponseDto;
        }
        $conflictDto->setConflicts($conflictArray);
        return $conflictDto;
    }

    public function listConflictsUsers($sourceId, $destinationId, $viewmode)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->conflictRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $userConflicts = $this->conflictRepository->findBy([
            'srcOrgId' => $sourceId,
            'dstOrgId' => $destinationId
        ]);
        $conflicts = '';
        if (! empty($userConflicts)) {
            $persons = $this->createConflictArray($userConflicts);
            $results = $this->getConflictPersonDetails($persons);
            $studentSummary = [];
            $facultySummary = [];
            $hybridSummary = [];
            $studentsDto = [];
            $staffDto = [];
            $hybridDto = [];
            $hybridDto = [];
            if (! empty($results[TierConstant::FIELD_STUDENT])) {
                $studentSummary = $this->showConflicts($results[TierConstant::FIELD_STUDENT]);
                $studentsDto[TierConstant::CONFLICT_CATEGORY] = 'students';
                $studentsDto[TierConstant::CONFLICTS] = $studentSummary;
            }
            if (! empty($results[TierConstant::FIELD_FACULTY])) {
                $facultySummary = $this->showConflicts($results[TierConstant::FIELD_FACULTY]);
                $staffDto[TierConstant::CONFLICT_CATEGORY] = TierConstant::FIELD_STAFF;
                $staffDto[TierConstant::CONFLICTS] = $facultySummary;
            }
            if (! empty($results[TierConstant::FIELD_HYBRID])) {
                $hybridSummary = $this->showConflicts($results[TierConstant::FIELD_HYBRID]);
                $hybridDto[TierConstant::CONFLICT_CATEGORY] = TierConstant::FIELD_HYBRID;
                $hybridDto[TierConstant::CONFLICTS] = $hybridSummary;
            }
            if (! empty($studentSummary)) {
                $conflicts[TierConstant::USER_CONFLICTS][] = $studentsDto;
            }
            if (! empty($facultySummary)) {
                $conflicts[TierConstant::USER_CONFLICTS][] = $staffDto;
            }
            if (! empty($hybridSummary)) {
                $conflicts[TierConstant::USER_CONFLICTS][] = $hybridDto;
            }
        }
        if ($viewmode == 'csv') {
            $this->usersConflictCSV($studentsDto, $staffDto, $hybridDto);
            $currentDate = time();
            return "roaster-uploads/1-list-user-conflict-details-roaster.csv";
        }
        return $conflicts;
    }

    private function createConflictArray($userConflicts)
    {
        $persons = array();
        foreach ($userConflicts as $userConflict) {
            $user[TierConstant::FIELD_CONFLICTID] = $userConflict->getId();
            if ($userConflict->getFacultyId() == $userConflict->getStudentId()) {
                $user[TierConstant::FIELD_USERTYPE] = TierConstant::FIELD_HYBRID;
                $person = $userConflict->getFacultyId();
            } else {
                if ($userConflict->getFacultyId() != '') {
                    $person = $userConflict->getFacultyId();
                    $user[TierConstant::FIELD_USERTYPE] = TierConstant::FIELD_FACULTY;
                } else {
                    $person = $userConflict->getStudentId();
                    $user[TierConstant::FIELD_USERTYPE] = TierConstant::FIELD_STUDENT;
                }
            }
            $contacts = $person->getContacts()->first();
            $externalId = $person->getExternalId();
            $user[TierConstant::FIELD_EXTERNALID] = $externalId;
            $user[TierConstant::FIELD_FIRSTNAME] = $person->getFirstName();
            $user[TierConstant::FIELD_LASTNAME] = $person->getLastName();
            $user[TierConstant::FIELD_PERSONID] = $person->getId();
            $user[TierConstant::FIELD_EMAIL] = $contacts->getPrimaryEmail();
            if ($userConflict->getOwningOrgTierCode() == 0) {
                $user[TierConstant::IS_HIERARCHY] = false;
                $user[TierConstant::FIELD_ORGID] = $userConflict->getSrcOrgId()->getId();
                $user[TierConstant::FIELD_CAMPUSID] = $userConflict->getSrcOrgId()->getCampusId();
            } else {
                $user[TierConstant::IS_HIERARCHY] = true;
                $user[TierConstant::FIELD_ORGID] = $userConflict->getDstOrgId()->getId();
                $user[TierConstant::FIELD_CAMPUSID] = $userConflict->getDstOrgId()->getCampusId();
            }
            $user['createdDate'] = $userConflict->getCreatedAt();
            $user['mobile'] = $contacts->getPrimaryMobile();
            $isMaster = ($userConflict->getRecordType() == TierConstant::FIELD_MASTER || $userConflict->getRecordType() == TierConstant::FIELD_OTHER) ? true : false;
            $isHome = ($userConflict->getRecordType() == 'home' || $userConflict->getRecordType() == TierConstant::FIELD_OTHER) ? true : false;
            $user['isHome'] = $isHome;
            $user['mergeType'] = $userConflict->getMergeType();
            $user['isMaster'] = $isMaster;
            $persons[$externalId][] = $user;
        }
        return $persons;
    }

    private function getConflictPersonDetails($persons)
    {
        $results = '';
        if (! empty($persons)) {
            foreach ($persons as $person) {
                $userType = array_column($person, TierConstant::FIELD_USERTYPE);
                $externalId = array_column($person, TierConstant::FIELD_EXTERNALID)[0];
                if (count(array_unique($userType)) == 1) {
                    $type = $userType[0];
                    $results[$type][$externalId] = $person;
                } else {
                    $results[TierConstant::FIELD_HYBRID][$externalId] = $person;
                }
            }
        }
        return $results;
    }

    private function showConflicts($users)
    {
        $conflictsDtoRecords = '';
        $conflictsDtoRows = array();
        foreach ($users as $key => $user) {
            $conflictsDtoRows = '';
            $conflictsByCategoryDto = new ConflictsByCategoryDto();
            foreach ($user as $student) {
                $conflictsDto = new ConflictDto();
                $conflictsDto->setConflictId($student[TierConstant::FIELD_CONFLICTID]);
                $conflictsDto->setFirstname($student[TierConstant::FIELD_FIRSTNAME]);
                $conflictsDto->setLastname($student[TierConstant::FIELD_LASTNAME]);
                $conflictsDto->setEmail($student[TierConstant::FIELD_EMAIL]);
                $conflictsDto->setOrgId($student[TierConstant::FIELD_ORGID]);
                $conflictsDto->setPersonId($student[TierConstant::FIELD_PERSONID]);
                $conflictsDto->setCampusId($student[TierConstant::FIELD_CAMPUSID]);
                $conflictsDto->setExternalId($student[TierConstant::FIELD_EXTERNALID]);
                $conflictsDto->setIsHome($student['isHome']);
                $conflictsDto->setMulticampusUser($student[TierConstant::IS_HIERARCHY]);
                $conflictsDto->setIsMaster($student['isMaster']);
                $conflictsDto->setMergeType($student['mergeType']);
                $conflictsDto->setCreatedOn($student['createdDate']);
                $conflictsDto->setRole($student[TierConstant::FIELD_USERTYPE]);
                $conflictsDtoRows[] = $conflictsDto;
            }
            $personDetails[TierConstant::CONFLICT_RECORDS] = $conflictsDtoRows;
            $conflictsDtoRecords[] = $personDetails;
        }
        return $conflictsDtoRecords;
    }

    public function viewConflictUserDetails($conflictId, $autoResolveId)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        
        $conflictDetails = $this->orgConflictRepo->findOneBy(array(
            'id' => $conflictId
        ));
        
        if (empty($conflictDetails)) {
            throw new ValidationException([
                "No records in Conflicts"
            ], "No records in Conflicts", 'No_records_in_Conflicts');
        }
        
        if (! empty($conflictDetails->getFacultyId())) {
            $externalId = $conflictDetails->getFacultyId()->getExternalId();
            $firstname = $conflictDetails->getFacultyId()->getFirstname();
            $lastname = $conflictDetails->getFacultyId()->getLastname();
            $campusId = $conflictDetails->getFacultyId()
                ->getOrganization()
                ->getCampusId();
            $email = $conflictDetails->getFacultyId()
                ->getContacts()[0]
                ->getPrimaryEmail();
        } elseif (! empty($conflictDetails->getStudentId())) {
            $externalId = $conflictDetails->getStudentId()->getExternalId();
            $firstname = $conflictDetails->getStudentId()->getFirstname();
            $lastname = $conflictDetails->getStudentId()->getLastname();
            $campusId = $conflictDetails->getStudentId()
                ->getOrganization()
                ->getCampusId();
            $email = $conflictDetails->getStudentId()
                ->getContacts()[0]
                ->getPrimaryEmail();
        } else {
            $externalId = '';
            $firstname = '';
            $campusId = '';
        }
        $conflictDto = new ConflictDto();
        $conflictDto->setConflictId($conflictDetails->getId());
        $conflictDto->setSourceOrgId($conflictDetails->getSrcOrgId()
            ->getId());
        $conflictDto->setDestinationOrgId($conflictDetails->getDstOrgId()
            ->getId());
        $conflictDto->setCreatedOn($conflictDetails->getCreatedAt());
        $conflictDto->setCampusId($campusId);
        $conflictDto->setFirstname($firstname);
        $conflictDto->setLastname($lastname);
        $conflictDto->setEmail($email);
        $conflictDto->setExternalId($externalId);
        $conflictDto->setAutoResolveId($autoResolveId);
        return $conflictDto;
    }

    public function updateResolveSingleConflict(ConflictDto $conflictDto)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        if ($conflictDto->getResolveType() == 'individual') {
            // Validate External Id
            return $this->resolveIndividualConflict($conflictDto);
        } else {
            return $this->resolveBulkConflict($conflictDto);
        }
    }

    private function resolveIndividualConflict($conflictDto)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $this->personFaculty = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_FACULTY_REPO);
        $this->personStudent = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        $externalId = $this->personRepository->findOneByExternalId($conflictDto->getExternalid());
        $conflictDetails = $this->orgConflictRepo->find($conflictDto->getConflictId());
        if (empty($conflictDetails)) {
            throw new ValidationException([
                UsersConstant::CONFLICT_RECORDS_NOT_FOUND
            ], UsersConstant::CONFLICT_RECORDS_NOT_FOUND, 'No Conflict Records');
        }
        if ($conflictDetails->getOwningOrgTierCode() == 3) {
            $personId = ($conflictDetails->getFacultyId()) ? $conflictDetails->getFacultyId() : $conflictDetails->getStudentId();
            $isDualConflict = $this->orgConflictRepo->isDualConflicts($personId);
            if ($isDualConflict > 1) {
                throw new ValidationException([
                    "This person is in conflict with other campus. Please resolve it before assigning the new id"
                ], "This person is in conflict with other campus. Please resolve it before assigning the new id", 'conflict_with_other_campuses');
            }
        }
        if ($externalId) {
            throw new ValidationException([
                UsersConstant::EXTERNALID_ALREADY_FOUND
            ], UsersConstant::EXTERNALID_ALREADY_FOUND, 'External Id Already Exists');
        }
        $campusId = ($conflictDetails->getOwningOrgTierCode() == 0) ? $conflictDetails->getSrcOrgId()->getId() : $conflictDetails->getDstOrgId()->getId();
        if (! empty($conflictDetails->getFacultyId())) {
            $personId = $conflictDetails->getFacultyId()->getId();
            $personFaculty = $this->personFaculty->findOneBy([
                'person' => $personId,
                'organization' => $campusId
            ]);
            $personFaculty->setStatus(1);
        }
        if (! empty($conflictDetails->getStudentId())) {
            $personId = $conflictDetails->getStudentId()->getId();
            $personStudent = $this->personStudent->findOneBy([
                'person' => $personId,
                'organization' => $campusId
            ]);
            $personStudent->setStatus(1);
        }
        $this->autoResolveOtherCampusConflicts($personId, $conflictDto);
        $conflictDetails->setStatus(TierConstant::MERGED);
        $this->autoResolvedConflicts($conflictDto->getAutoResolveId());
        $person = $this->personRepository->find($personId);
        $person->setExternalId($conflictDto->getExternalId());
        $this->orgConflictRepo->remove($conflictDetails);
        $this->personRepository->flush();
        return $conflictDto;
    }

    private function resolveBulkConflict($conflictDto)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $type = $conflictDto->getResolveType();
        $studentsConflict = '';
        $facultyConflict = '';
        $hybridConflict = '';
        if (isset($conflictDto->getUserConflicts()[0])) {
            switch ($conflictDto->getUserConflicts()[0]->getConflictCategory()) {
                case "students":
                    $studentsConflict = $conflictDto->getUserConflicts()[0]->getConflicts();
                    break;
                case TierConstant::FIELD_STAFF:
                    $facultyConflict = $conflictDto->getUserConflicts()[0]->getConflicts();
                    break;
                case TierConstant::FIELD_HYBRID:
                    $hybridConflict = $conflictDto->getUserConflicts()[0]->getConflicts();
                    break;
                default:
                    $studentsConflict = '';
                    break;
            }
        }
        if (isset($conflictDto->getUserConflicts()[1])) {
            switch ($conflictDto->getUserConflicts()[1]->getConflictCategory()) {
                case TierConstant::FIELD_STAFF:
                    $facultyConflict = $conflictDto->getUserConflicts()[1]->getConflicts();
                    break;
                case TierConstant::FIELD_HYBRID:
                    $hybridConflict = $conflictDto->getUserConflicts()[1]->getConflicts();
                    break;
                default:
                    $facultyConflict = '';
                    $hybridConflict = '';
                    break;
            }
        }
        if (isset($conflictDto->getUserConflicts()[2])) {
            $hybridConflict = $conflictDto->getUserConflicts()[2]->getConflicts();
        }
        $this->resolveConflictsByType($studentsConflict, $facultyConflict, $hybridConflict, $type);
        return '';
    }

    protected function resolveConflictsByType($studentsConflict, $facultyConflict, $hybridConflict, $type)
    {
        if (isset($studentsConflict)) {
            $this->resolveConflicts($studentsConflict, TierConstant::FIELD_STUDENT, $type);
        }
        if (isset($facultyConflict)) {
            $this->resolveConflicts($facultyConflict, TierConstant::FIELD_STAFF, $type);
        }
        if (isset($hybridConflict)) {
            $this->resolveConflicts($hybridConflict, TierConstant::FIELD_HYBRID, $type);
        }
    }

    /**
     * Resolve conflicts from the conflicted data.
     *
     * @param ConflictPersonDetailsDto $conflictData
     * @param string $userType
     * @param string $type
     */
    private function resolveConflicts($conflictData, $userType, $type)
    {
        if (! empty($conflictData)) {
            foreach ($conflictData as $conflict) {
                $conflictRecords = $conflict->getConflictRecords();
                $homeCampus = $this->homeCampus($conflictRecords[0], $conflictRecords[1]);
                $masterId = $this->masterRecord($conflictRecords[0], $conflictRecords[1]);
                $this->saveHomeMaster($conflictRecords[0], $conflictRecords[1]);
                $this->saveMergeType($conflictRecords[0], $conflictRecords[1]);
                if ($type == '' && $masterId) {
                    $sourcePerson = $conflictRecords[0]->getPersonId();
                    $sourceCampus = $conflictRecords[0]->getOrganizationId();
                    $sourceConflictId = $conflictRecords[0]->getConflictId();
                    $targetPerson = $conflictRecords[1]->getPersonId();
                    $destinationCampus = $conflictRecords[1]->getOrganizationId();
                    $destinationConflictId = $conflictRecords[1]->getConflictId();
                    $conflictSourceDetails = $this->orgConflictRepository->find($sourceConflictId);
                    $conflictTargetDetails = $this->orgConflictRepository->find($destinationConflictId);
                    $this->markHome($homeCampus, $sourceCampus, $sourcePerson, $targetPerson);
                    // Setting Master Record
                    $copyTo = ($masterId == $sourcePerson) ? $targetPerson : $sourcePerson;
                    $person = $this->personRepository->find($masterId);
                    // set the record type as master
                    $person->setRecordType('master');
                    $copyPerson = $this->personRepository->find($copyTo);
                    $this->copyContacts($copyPerson, $person);
                    $this->personRepository->flush();
                    $this->sourceDualConflicts($userType, $conflictRecords, $sourcePerson, $sourceCampus, $targetPerson, $destinationCampus);
                    $this->targetDualConflicts($userType, $conflictRecords, $sourcePerson, $sourceCampus, $targetPerson, $destinationCampus);
                    $conflictSourceDetails->setStatus(TierConstant::MERGED);
                    $conflictTargetDetails->setStatus(TierConstant::MERGED);
                    $this->orgConflictRepository->flush();
                    $this->orgConflictRepository->remove($conflictSourceDetails);
                    $this->orgConflictRepository->remove($conflictTargetDetails);
                }
                $this->orgConflictRepository->flush();
            }
        }
    }

    private function copyContacts($copyPerson, $person)
    {
        if ($copyPerson) {
            // DeactivationEmail to Go
            $this->sendDeactivationEmail($copyPerson);
            $copyPerson->setFirstname($person->getFirstname());
            $copyPerson->setLastname($person->getLastname());
            $copyPerson->setUsername($person->getUsername());
            $copyPerson->setTitle($person->getTitle());
            $copyPerson->setExternalId($person->getExternalId());
            $personContact = $person->getContacts()[0];
            $copyContact = $copyPerson->getContacts()[0];
            if ($personContact && $copyContact) {
                if ($personContact->getPrimaryMobile()) {
                    $copyContact->setPrimaryMobile($personContact->getPrimaryMobile());
                } else {
                    $copyContact->setHomePhone($personContact->getHomePhone());
                }
                $copyContact->setPrimaryEmail($personContact->getPrimaryEmail());
            }
            // ActivationEmail to Go
            $this->activationEmail($person);
        }
    }

    /**
     * Sends the Mapworks account deactivation email to a user.
     *
     * @param Person $person
     * @throws SynapseValidationException
     */
    private function sendDeactivationEmail($person)
    {
        $organizationId = $person->getOrganization()->getId();
        $emailKey = "Deactivate_Email";
        $emailTemplate = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
        if ($emailTemplate) {
            $emailTemplate = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplate]);
        }
        else{
            throw new SynapseValidationException("Email template for key $emailKey not found");
        }
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        $tokenValues['Skyfactor_Mapworks_logo'] = "";
        if ($systemUrl) {
            $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . "images/Skyfactor-Mapworks-login.png";
        }

        $supportEmail = $this->ebiConfigRepository->findOneByKey('Coordinator_Support_Helpdesk_Email_Address');
        $tokenValues['Support_Helpdesk_Email_Address'] = $supportEmail->getValue();
        $responseArray = array();
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $email = $person->getUsername();
            $tokenValues['firstname'] = $person->getFirstname();
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
            $subject = $emailTemplate->getSubject();
            $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
            $responseArray['email_detail'] = array(
                'from' => $from,
                'subject' => $subject,
                'bcc' => $bcc,
                'body' => $emailBody,
                'to' => $email,
                'emailKey' => $emailKey,
                'organizationId' => $organizationId
            );
            $emailObject = $this->emailService->sendEmailNotification($responseArray['email_detail']);
            $this->emailService->sendEmail($emailObject);
        }
    }

    /**
     * Sends the mapworks account activation email to the user.
     *
     * @param Person $person
     * @return array
     * @throws SynapseValidationException
     */
    private function activationEmail($person)
    {
        $organizationId = $person->getOrganization()->getId();
        $getUserDetails = $this->personRepository->getUsersByUserIds($person->getId());
        $token = md5($getUserDetails[0]['user_email'] . time() . $getUserDetails[0]['user_email']);
        $person->setActivationToken($token);

        $emailKey = "Activate_Email";
        $emailTemplate = $this->emailTemplateRepository->findOneBy(['emailKey' => $emailKey]);
        if ($emailTemplate) {
            $emailTemplate = $this->emailTemplateLangRepository->findOneBy(['emailTemplate' => $emailTemplate]);
        }
        else{
            throw new SynapseValidationException("Email template for key $emailKey not found");
        }
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        $tokenValues['Skyfactor_Mapworks_logo'] = "";
        if ($systemUrl) {
            $tokenValues['Skyfactor_Mapworks_logo'] = $systemUrl . 'images/Skyfactor-Mapworks-login.png';
        }
        $responseArray = array();

        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $tokenValues = array();
            $expire = SynapseConstant::RESET_PASSWORD_EXPIRY_HOURS;
            $tokenValues['firstname'] = $person->getFirstname();
            $urlPrefix = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_ResetPwd_URL_Prefix']);
            $supportEmail = $this->ebiConfigRepository->findOneBy(['key' => 'Coordinator_Support_Helpdesk_Email_Address']);
            $tokenValues['Support_Helpdesk_Email_Address'] = $supportEmail->getValue();
            $tokenValues['Coordinator_ResetPwd_URL_Prefix'] = $urlPrefix->getValue() . $token;
            $tokenValues['activation_token'] = $urlPrefix->getValue() . $token;
            $tokenValues['Reset_Password_Expiry_Hrs'] = $expire;
            $emailBody = $this->emailService->generateEmailMessage($emailBody, $tokenValues);
            $sendLinkDate = new \DateTime('now');
            $person->setWelcomeEmailSentDate($sendLinkDate);
        }
        $responseArray['email_sent_status'] = true;
        $responseArray['welcome_email_sentDate'] = $sendLinkDate;
        $email = $person->getUsername();
        $responseArray['email_detail'] = array(
            'from' => $emailTemplate->getEmailTemplate()->getFromEmailAddress(),
            'subject' => $emailTemplate->getSubject(),
            'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
            'body' => $emailBody,
            'to' => $email,
            'emailKey' => $emailKey,
            'organizationId' => $organizationId
        );
        $this->personRepository->flush();
        $emailInst = $this->emailService->sendEmailNotification($responseArray['email_detail']);
        $send = $this->emailService->sendEmail($emailInst);
        if ($send) {
            $responseArray['message'] = "Mail sent successfully to $email";
        } else {
            $responseArray['message'] = "Mail email sending failed to $email";
        }
        return $responseArray;
    }
}
