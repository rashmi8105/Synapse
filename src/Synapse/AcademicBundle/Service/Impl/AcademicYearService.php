<?php
namespace Synapse\AcademicBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\Date;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\AcademicBundle\EntityDto\AcademicTermDto;
use Synapse\AcademicBundle\EntityDto\AcademicYearCDto;
use Synapse\AcademicBundle\EntityDto\AcademicYearDto;
use Synapse\AcademicBundle\EntityDto\AcademicYearListResponseDto;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Repository\YearRepository;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonEbiMetaDataRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SurveyBundle\Service\Impl\CohortsService;


/**
 * @DI\Service("academicyear_service")
 */
class AcademicYearService extends AbstractService
{

    const SERVICE_KEY = 'academicyear_service';

    /**
     * An SQL query goes into a bar, walks up to two tables
     * and asks, "May I join you?"
     *
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_YEAR_REPO = "SynapseAcademicBundle:OrgAcademicYear";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const YEAR_REPO = "SynapseAcademicBundle:Year";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGANIZATION_REPO = "SynapseCoreBundle:Organization";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_TERM_REPO = "SynapseAcademicBundle:OrgAcademicTerms";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_FACULTY_REPO = "SynapseAcademicBundle:OrgCourseFaculty";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_STUDENT_REPO = "SynapseAcademicBundle:OrgCourseStudent";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACADEMIC_COURSE_REPO = "SynapseAcademicBundle:OrgCourses";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const META_LIST_REPO = "SynapseCoreBundle:MetadataListValues";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORG_ROLE_REPO = "SynapseCoreBundle:OrganizationRole";

    const PERSON_SERVICE = 'person_service';

    const ACADEMIC_DATE_ERROR = "End-Date should be grater than Start-Date";

    const ACADEMIC_DATE_ERROR_KEY = "End-date_should_be_grater_than_start-date";

    const ORG_NOT_FOUND = "Organization Not Found.";

    const ORG_NOT_FOUND_KEY = "organization_not_found.";

    const ERROR_YEAR_ID = "Academic year should not overlap with other years for same organization.";

    const ERROR_YEAR_ID_KEY = "academic_year_should_not_overlap_with_other_years_for_same_organization";

    const ERROR_INVALID_YEAR_ID = "Academic Year Id does not exist.";

    const ERROR_INVALID_YEAR_ID_KEY = "academic_year_id_does_not_exist.";

    const DATE_FORMAT = 'Y-m-d';

    const COORDINATOR_NOT_FOUND = "Coordinator Not Found.";

    const COORDINATOR_NOT_FOUND_KEY = "coordinator_not_found.";

    const YEAR_NOT_FOUND = "Year Not Found.";

    const YEAR_NOT_FOUND_KEY = "year_not_found.";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBI_META_DATA_REPO = "SynapseCoreBundle:PersonEbiMetadata";

    const ORGANIZATION = 'organization';

    const ORG_ACADEMIC_YEAR = 'orgAcademicYear';

    const COURSE = 'course';

    const YEAR_CACHE = 'year_cache';

    /**
     * @var CohortsService
     */
    private $cohortsService;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListRepository;

    /**
     * @var OrgAcademicTermRepository
     */
    private $orgAcademicTermRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var OrganizationService
     */
    private $orgService;

    /**
     * @var PersonEbiMetaDataRepository
     */
    private $personEbiMetaDatatRepository;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var YearRepository
     */
    private $yearRepository;


    /**
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        // Security
        $this->rbacManager = $this->container->get('tinyrbac.manager');

        // Services
        $this->cohortsService = $this->container->get(CohortsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);


        // Repositories
        $this->metadataListRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personEbiMetaDatatRepository = $this->repositoryResolver->getRepository(PersonEbiMetaDataRepository::REPOSITORY_KEY);
        $this->yearRepository = $this->repositoryResolver->getRepository(YearRepository::REPOSITORY_KEY);
    }

    /**
     * Creates an academic year.
     *
     * @param AcademicYearDto $academicYearDto
     * @param int $loggedInUserId
     * @return AcademicYearDto
     * @throws SynapseValidationException
     */
    public function createAcademicyear(AcademicYearDto $academicYearDto, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($academicYearDto->getOrganization()); // Says getOranization but actually returns the orgId number

        $organization = $this->orgService->find($academicYearDto->getOrganization());
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);
        // Validating loggedInUser is Coordinator
        $this->checkIsCoordinator($academicYearDto->getOrganization(), $loggedInUserId);

        $year = $this->yearRepository->find($academicYearDto->getYearId());

        $this->isObjectExist($year, self::YEAR_NOT_FOUND, self::YEAR_NOT_FOUND_KEY);
        // $start = Helper::getUtcDate($academicYearDto->getStartDate(), $timeZone);
        $start = $academicYearDto->getStartDate();
        $start->setTime(0, 0, 0);
        // $end = Helper::getUtcDate($academicYearDto->getEndDate(), $timeZone);
        $end = $academicYearDto->getEndDate();
        $end->setTime(0, 0, 0);
        $this->validateAcademicDate($start, $end);
        $orgAcademicYear = new OrgAcademicYear();
        $orgAcademicYear->setOrganization($organization);
        $orgAcademicYear->setName($academicYearDto->getName());
        $orgAcademicYear->setYearId($year);
        // Validating overlapping b/w start date and end date for organization
        $orgAcademicYearsDetails = $this->orgAcademicYearRepository->findBy(array(
            'organization' => $academicYearDto->getOrganization()
        ));

        foreach ($orgAcademicYearsDetails as $orgAcademicYearsDetail) {
            $startDateDb = $orgAcademicYearsDetail->getStartDate()->format(self::DATE_FORMAT);
            $endDateDb = $orgAcademicYearsDetail->getEndDate()->format(self::DATE_FORMAT);
            if (($start->format(self::DATE_FORMAT) >= $startDateDb && $start->format(self::DATE_FORMAT) <= $endDateDb) || ($end->format(self::DATE_FORMAT) >= $startDateDb && $end->format(self::DATE_FORMAT) <= $endDateDb) || (($start->format(self::DATE_FORMAT) <= $startDateDb) && ($end->format(self::DATE_FORMAT) >= $endDateDb))) {
                $this->isObjectExist(null, self::ERROR_YEAR_ID, self::ERROR_YEAR_ID_KEY);
            }
        }
        $orgAcademicYear->setStartDate($start);
        $orgAcademicYear->setEndDate($end);

        $validator = $this->container->get('validator');
        $errors = $validator->validate($orgAcademicYear);
        $this->catchError($errors);
        $orgAcademicYearInst = $this->orgAcademicYearRepository->persist($orgAcademicYear, $flush = true);
        $academicYearDto->setId($orgAcademicYearInst->getId());

        //Create Cohort Names for Cohort 1 - 4 for the Academic Year
        for($cohort = 1; $cohort <= 4; $cohort++){
            $this->cohortsService->createOrgCohortName($organization, $orgAcademicYear, $cohort, 'Survey Cohort ' . $cohort);
        }

        return $academicYearDto;
    }

    /**
     * Get academic years for the organization.
     *
     * @param Date $startDate
     * @param Date $endDate
     * @throws ValidationException
     */
    private function validateAcademicDate($startDate, $endDate)
    {
        if ($endDate <= $startDate) {
            $this->isObjectExist(NULL, self::ACADEMIC_DATE_ERROR, self::ACADEMIC_DATE_ERROR_KEY);
        }
    }


    /**
     * Gets academic years for an organization by its id.
     *
     * @param int $organizationId
     * @param boolean|null $excludeFutureYears - If true list only current and past academic years.
     * @param boolean|false $excludePastYears - If true list only current and future academic years.
     * @param boolean|true $isInternal
     * @return AcademicYearListResponseDto
     * @throws SynapseValidationException
     */
    public function listAcademicYears($organizationId, $excludeFutureYears, $excludePastYears = false, $isInternal = true)
    {
        // Validating organization
        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found');
        }

        if ($isInternal) {
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }
        $organizationAcademicYearsWithTermsArray = $this->orgAcademicYearRepository->getAllAcademicYearsWithTerms($organizationId);
        $academicYearListResponseDto = new AcademicYearListResponseDto();
        if ($isInternal) {
            $academicYearListResponseDto->setOrganizationId($organizationId);
        }
        $academicYearArray = [];
        $organizationAcademicYearArray = [];
        $organizationAcademicTermArray = [];
        $organizationAcademicTermArrayDetails = [];

        foreach ($organizationAcademicYearsWithTermsArray as $organizationAcademicYearsWithTerms) {

            if (!array_key_exists($organizationAcademicYearsWithTerms['year_id'], $organizationAcademicYearArray)) {
                $organizationAcademicYearArray[$organizationAcademicYearsWithTerms['year_id']] = $organizationAcademicYearsWithTerms;
            }

            if (isset($organizationAcademicYearsWithTerms['term_id'])) {
                if (!in_array($organizationAcademicYearsWithTerms['term_id'], $organizationAcademicTermArray)) {
                    $organizationAcademicTermArray[] = $organizationAcademicYearsWithTerms['term_id'];
                    $organizationAcademicTermArrayDetails[$organizationAcademicYearsWithTerms['year_id']][$organizationAcademicYearsWithTerms['term_id']] = $organizationAcademicYearsWithTerms;
                }
            }
        }

        $academicYearTermsArray = [];
        foreach ($organizationAcademicYearArray as $organizationAcademicYear) {
            $organizationAcademicYear['terms'] = $organizationAcademicTermArrayDetails[$organizationAcademicYear['year_id']];
            $academicYearTermsArray [] = $organizationAcademicYear;
        }

        foreach ($academicYearTermsArray as $academicYearTerms) {
            $orgAcademicYearId = $academicYearTerms['id'];
            $academicYearTense = $academicYearTerms['year_status'];
            if (!($excludeFutureYears && $academicYearTense == 'future') && !($excludePastYears && $academicYearTense == 'past')) {
                $academicYearDto = new AcademicYearDto();
                if ($isInternal) {
                    $academicYearDto->setId($orgAcademicYearId);
                    // The isNotAssociated function actually returns false if the year is associated with something which would prevent its deletion.
                    $canDelete = $this->isNotAssociated($orgAcademicYearId);
                    $academicYearDto->setCanDelete($canDelete);
                } else {
                    $academicTermDtoArray = [];
                    foreach ($academicYearTerms['terms'] as $academicTerm) {

                        $academicTermDto = new AcademicTermDto();
                        $academicTermDto->setName($academicTerm['term_name']);
                        $academicTermDto->setTermId($academicTerm['term_code']);
                        $termStartDate = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($academicTerm['term_start_date'], $organizationId);
                        $termEndDate = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($academicTerm['term_end_date'], $organizationId);
                        $academicTermDto->setStartDate($termStartDate);
                        $academicTermDto->setEndDate($termEndDate);
                        $academicTermDtoArray[] = $academicTermDto;
                    }
                    $academicYearDto->setAcademicTerms($academicTermDtoArray);
                }
                $academicYearDto->setName($academicYearTerms['year_name']);
                $academicYearDto->setYearId($academicYearTerms['year_id']);
                $yearStartDate = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($academicYearTerms['year_start_date'], $organizationId);
                $yearEndDate = $this->dateUtilityService->adjustOrganizationDateTimeStringToUtcDateTimeObject($academicYearTerms['year_end_date'], $organizationId);
                $academicYearDto->setStartDate($yearStartDate);
                $academicYearDto->setEndDate($yearEndDate);
                if ($academicYearTense == 'current') {
                    $academicYearDto->setIsCurrentYear(true);
                } else {
                    $academicYearDto->setIsCurrentYear(false);
                }
                $academicYearArray[] = $academicYearDto;
            }
        }
        $academicYearListResponseDto->setAcademicYears($academicYearArray);
        return $academicYearListResponseDto;
    }


    /**
     * Gets academic years for an organization by its id.
     *
     * @param int $organizationId
     * @param string $yearId
     * @param int $loggedInUserId
     * @return AcademicYearDto
     * @throws AccessDeniedException|SynapseValidationException
     */
    public function getAcademicYear($organizationId, $yearId, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $this->rbacManager->checkAccessToOrganizationUsingPersonId($loggedInUserId);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_YEAR_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(self::ORGANIZATION_REPO);
        $organization = $this->orgRepository->find($organizationId);
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);

        if (strtolower($yearId) == 'current') {
            $date = new \DateTime('now');
            $currentDate = $date->setTime(0, 0, 0);
            $orgAcademicYear = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDate, $organizationId);
            $academicYearRes = $this->setCurrentAcademicYearRes($orgAcademicYear, $organizationId);
        } else {
            $academicYears = $this->orgAcademicYearRepository->findOneBy(array(
                self::ORGANIZATION => $organizationId,
                'id' => $yearId
            ));
            $this->isObjectExist($academicYears, self::ERROR_INVALID_YEAR_ID, self::ERROR_INVALID_YEAR_ID_KEY);
            $timezone = $organization->getTimezone();
            $timezone = $this->repositoryResolver->getRepository(self::META_LIST_REPO)->findByListName($timezone);
            if ($timezone) {
                $timeZone = $timezone[0]->getListValue();
            }
            $academicYearRes = new AcademicYearDto();
            // Setting the Response
            $isDeleted = $this->isNotAssociated($academicYears);
            $academicYearRes->setId($academicYears->getId());
            $academicYearRes->setOrganization($academicYears->getOrganization()
                ->getId());
            $academicYearRes->setName($academicYears->getName());
            $academicYearRes->setYearId($academicYears->getYearId()
                ->getId());
            // Helper::setOrganizationDate($academicYears->getStartDate(), $timeZone);
            $academicYearRes->setStartDate($academicYears->getStartDate());
            // Helper::setOrganizationDate($academicYears->getEndDate(), $timeZone);
            $academicYearRes->setEndDate($academicYears->getEndDate());
            $academicYearRes->setCanDelete($isDeleted);
        }
        return $academicYearRes;
    }

    /**
     * Gets academic years for an organization by its id.
     *
     * @param AcademicYearDto $academicYearDto
     * @param int $loggedInUserId
     * @return AcademicYearDto
     * @throws AccessDeniedException|ValidationException
     */
    public function editAcademicYear($academicYearDto, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($academicYearDto->getOrganization()); // Says getOranization but actually returns the orgId number

        $organization = $this->orgService->find($academicYearDto->getOrganization());
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);

        $this->checkIsCoordinator($academicYearDto->getOrganization(), $loggedInUserId);
        $academicYear = $this->orgAcademicYearRepository->findOneBy(array(
            self::ORGANIZATION => $academicYearDto->getOrganization(),
            'id' => $academicYearDto->getId()
        ));
        $this->isObjectExist($academicYear, self::ERROR_INVALID_YEAR_ID, self::ERROR_INVALID_YEAR_ID_KEY);

        // if term,course,student,personEbiMetaData and faculty associated with academic year then only name field will be editable else all fields
        if ( $this->isNotAssociated($academicYear)) {
            $academicYear->setName($academicYearDto->getName());
        } else {
            // Call setters to set the Academic Year property values
            $academicYear->setName($academicYearDto->getName());
            $year = $this->yearRepository->find($academicYearDto->getYearId());
            $this->isObjectExist($year, self::YEAR_NOT_FOUND, self::YEAR_NOT_FOUND_KEY);
            $start = $academicYearDto->getStartDate();
            $start->setTime(0, 0, 0);
            $end = $academicYearDto->getEndDate();
            $end->setTime(0, 0, 0);
            $this->validateAcademicDate($start, $end);
            $academicYear->setOrganization($organization);
            $academicYear->setName($academicYearDto->getName());
            $academicYear->setYearId($year);

            // Validating overlapping b/w start date and end date for organization
            $orgAcademicYearsDetails = $this->orgAcademicYearRepository->getOrgAcademicYearsDetails($academicYearDto->getOrganization(), $academicYearDto->getId());
            foreach ($orgAcademicYearsDetails as $orgAcademicYearsDetail) {
                $startDateDb = $orgAcademicYearsDetail['startDate']->format(self::DATE_FORMAT);
                $endDateDb = $orgAcademicYearsDetail['endDate']->format(self::DATE_FORMAT);
                if (($start->format(self::DATE_FORMAT) >= $startDateDb && $start->format(self::DATE_FORMAT) <= $endDateDb) || ($end->format(self::DATE_FORMAT) >= $startDateDb && $end->format(self::DATE_FORMAT) <= $endDateDb) || (($start->format(self::DATE_FORMAT) <= $startDateDb) && ($end->format(self::DATE_FORMAT) >= $endDateDb))) {
                    $this->isObjectExist(null, self::ERROR_YEAR_ID, self::ERROR_YEAR_ID_KEY);
                }
            }
            $academicYear->setStartDate($start);
            $academicYear->setEndDate($end);
        }
        $this->orgAcademicYearRepository->flush();
        return $academicYearDto;
    }

    /**
     * Gets academic years for an organization by its id.
     *
     * @param int $organizationId
     * @param int $id
     * @param int $loggedInUserId
     * @return int
     * @throws AccessDeniedException|ValidationException
     */
    public function deleteAcademicYear($organizationId, $id, $loggedInUserId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_YEAR_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(self::ORGANIZATION_REPO);
        $organization = $this->orgService->find($organizationId);
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);

        $this->checkIsCoordinator($organizationId, $loggedInUserId);
        $academicYear = $this->orgAcademicYearRepository->findOneBy(array(
            self::ORGANIZATION => $organizationId,
            'id' => $id
        ));
        $this->isObjectExist($academicYear, self::ERROR_INVALID_YEAR_ID, self::ERROR_INVALID_YEAR_ID_KEY);

        $this->orgAcademicTermRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_TERM_REPO);
        $this->OrgCourseFacultyRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_FACULTY_REPO);
        $this->OrgCourseStudentRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_STUDENT_REPO);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_COURSE_REPO);

        $term = $this->orgAcademicTermRepository->findBy([
            'orgAcademicYearId' => $academicYear
        ]);

        $course = $this->orgCoursesRepository->findBy([
            self::ORG_ACADEMIC_YEAR => $academicYear
        ]);

        $faculty = $this->OrgCourseFacultyRepository->findBy([
            self::COURSE => $course
        ]);

        $student = $this->OrgCourseStudentRepository->findBy([
            self::COURSE => $course
        ]);

        /*
         * if term,course,student, personEbiMetaData and faculty associated with academic year then error will be shown else records will be deleted
         */

        $isDeleted = $this->isNotAssociated($academicYear);
        if (! $isDeleted) {
            throw new ValidationException([
                'Course or faculty/student data has been associated with this academic year.'
            ], 'Course or faculty/student data has been associated with this academic year.', 'Course_or_faculty/student_data_has_been_associated_with_this_academic_year');
        }

        //Delete Cohort Names if corresponding OrgAcademicYear is deleted
        for($cohort = 1; $cohort <= 4; $cohort++){
            $this->cohortsService->deleteOrgCohortName($organization, $academicYear, $cohort);
        }

        $this->orgAcademicYearRepository->deleteAcademicYear($academicYear);
        $this->orgAcademicYearRepository->flush();
        $academicYearId = $academicYear->getId();
        return $academicYearId;
    }

    /**
     * Throws ValidationException if object does not exist.
     *
     * @param object $object
     * @param string $message
     * @param int $key
     * @throws ValidationException
     */
    private function isObjectExist($object, $message, $key)
    {
        if (! ($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Catches error(s) and creates an error string with each error and its errorMessage.
     *
     * @param array $errors
     * @throws ValidationException
     */
    private function catchError($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {

                $errorsString = $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'duplicate_Error');
        }
    }

    /**
     * Gets academic years for an organization by its id.
     *
     * @param int $organizationId
     * @return array
     * @throws AccessDeniedException
     */
    public function listYear($organizationId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(self::ACADEMIC_YEAR_REPO);
        $this->yearRepository = $this->repositoryResolver->getRepository(self::YEAR_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(self::ORGANIZATION_REPO);
        $cache = $this->container->get('synapse_redis_cache');
        $organization = $this->orgService->find($organizationId);
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);
        $academicYearId = $this->orgAcademicYearRepository->findBy(array(
            self::ORGANIZATION => $organizationId
        ));
        $yearId = $cache->fetch(self::YEAR_CACHE);
        if (!$yearId) {
            $yearId = $this->yearRepository->listYearIds();
            $cache->save(self::YEAR_CACHE, $yearId, 7200);
        }

        $academicYearList = array();
        foreach ($academicYearId as $academicYear) {
            $academicYearList[] = $academicYear->getYearId()->getId();
        }
        $yearIdList = array();
        $yearIdArr = array();
        foreach ($yearId as $year) {

            if (! in_array($year['id'], $academicYearList)) {
                $yearIdArr['id'] = $year['id'];
                $yearIdList['year_id'][] = $yearIdArr;
            }
        }
        return $yearIdList;
    }

    /**
     *  This function will return false when the year has association with either of term, course, student or profile metadata,
     *  true if there is no association.
     *
     * @param int $academicYearId
     * @return boolean
     */
    private function isNotAssociated($academicYearId)
    {
        $term = $this->orgAcademicTermRepository->findOneBy(['orgAcademicYearId' => $academicYearId]);
        if($term){
            return false;
        }
        $course = $this->orgCoursesRepository->findOneBy(['orgAcademicYear' => $academicYearId]);
        if($course){
            return false;
        }
        $personYearObj = $this->orgPersonStudentYearRepository->findOneBy(['orgAcademicYear' => $academicYearId]);
        if($personYearObj){
            return false;
        }
        $personEbiMetaData = $this->personEbiMetaDatatRepository->findOneBy(['orgAcademicYear'=> $academicYearId]);
        if($personEbiMetaData) {
            return false;
        }
        return true;
    }

    /**
     *  Checks if a user is a coordinator.
     *
     * @param int $organizationId
     * @param int $loggedInUserId
     * @throws AccessDeniedException
     */
    public function checkIsCoordinator($organizationId, $loggedInUserId)
    {
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(self::ORG_ROLE_REPO);
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($organizationId, $loggedInUserId);
        if (! $isCoordinator) {
            throw new AccessDeniedException();
        }
    }

    /**
     *  Gets the currentAcademicYearResponse for an organization.
     *
     * @param int $orgAcademicYear
     * @param int $organizationId
     * @return AcademicYearCDto
     */
    private function setCurrentAcademicYearRes($orgAcademicYear, $organizationId)
    {
        $academicYearResponse = new AcademicYearCDto();
        $academicYearResponse->setId($orgAcademicYear[0]['id']);
        $academicYearResponse->setOrganization($organizationId);
        $academicYearResponse->setName($orgAcademicYear[0]['year_name']);
        $academicYearResponse->setYearId($orgAcademicYear[0]['yearId']);

        $organization = $this->orgService->find($organizationId);
        $this->isObjectExist($organization, self::ORG_NOT_FOUND, self::ORG_NOT_FOUND_KEY);
        $timezone = $organization->getTimezone();
        $timezone = $this->repositoryResolver->getRepository(self::META_LIST_REPO)->findByListName($timezone);
        if ($timezone) {
        	$timeZone = $timezone[0]->getListValue();
        }

        $startDate = new \DateTime($orgAcademicYear[0]['startDate'],new \DateTimeZone($timeZone));
        $endDate = new \DateTime($orgAcademicYear[0]['endDate'],new \DateTimeZone($timeZone));
        $academicYearResponse->setStartDate($startDate);
        $academicYearResponse->setEndDate($endDate);

        return $academicYearResponse;
    }


    /**
     * Determines whether the given $orgAcademicYearId represents a past, current, or future academic year.
     *
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @return string  - 'past' or 'current' or 'future'
     */
    public function determinePastCurrentOrFutureYearForOrganization($organizationId, $orgAcademicYearId)
    {
        $currentDate = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Y-m-d');
        $yearTense = $this->orgAcademicYearRepository->determinePastCurrentOrFutureYear($orgAcademicYearId, $currentDate);
        return $yearTense;
    }


    /**
     * Finds the current academic year for the given organization.
     * Returns an array with both the org_academic_year_id and the year_id, with appropriately named keys.
     * If the organization has no current academic year, returns an empty array.
     *
     * @param int $organizationId
     * @return array
     */
    public function findCurrentAcademicYearForOrganization($organizationId)
    {
        $currentDate = $this->dateUtilityService->getCurrentFormattedDateTimeForOrganization($organizationId, 'Y-m-d');
        $currentOrgAcademicYear = $this->orgAcademicYearRepository->getCurrentYearId($currentDate, $organizationId);
        if ($currentOrgAcademicYear) {
            $yearData = [
                'org_academic_year_id' => $currentOrgAcademicYear[0]['id'],
                'year_id' => $currentOrgAcademicYear[0]['yearId']
            ];
        } else {
            $yearData = [];
        }

        return $yearData;
    }


    /**
     * Returns the current org_academic_year_id for the given organization.
     * If there is no current year, returns null.
     *
     * @param int $organizationId
     * @param boolean $throwException - if true, throws a SynapseValidationException instead of returning a null value.
     * @return int|null
     * @throws SynapseValidationException
     */
    public function getCurrentOrgAcademicYearId($organizationId, $throwException = false)
    {
        $currentAcademicYear = $this->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        } else {
            if ($throwException) {
                throw new SynapseValidationException('There is no currently active academic year.');
            } else {
                $orgAcademicYearId = null;
            }
        }

        return $orgAcademicYearId;
    }


    /**
     * Checks that the given $orgAcademicYearId exists and belongs to the given organization.
     * Throws a SynapseValidationException if not.
     *
     * @param int $orgAcademicYearId
     * @param int $organizationId
     * @throws SynapseValidationException
     */
    public function validateAcademicYear($orgAcademicYearId, $organizationId)
    {
        $orgAcademicYearObject = $this->orgAcademicYearRepository->find($orgAcademicYearId);
        if (empty($orgAcademicYearObject)) {
            throw new SynapseValidationException('The academic year selected does not exist.');
        } else {
            $organizationIdAssociatedWithYear = $orgAcademicYearObject->getOrganization()->getId();
            if ($organizationIdAssociatedWithYear != $organizationId) {
                throw new SynapseValidationException('The academic year selected does not belong to the organization.');
            }
        }
    }

    /**
     * Get the current academic year and the two following academic years if they exist.
     * (This list may not be successively chronological ie. could return 201617's org academic year id, 201920's org academic year id, 202122's org academic year id )
     *
     * @param int $organizationId
     * @param int $academicYearId
     * @param int $numberOfRecords
     * @return array
     * @throws SynapseValidationException
     */
    public function getSelectedAcademicYearIdAndFollowingAcademicYearIds($organizationId, $academicYearId, $numberOfRecords = 3)
    {
        $orgAcademicYearInstance = $this->orgAcademicYearRepository->find($academicYearId);

        if (!$orgAcademicYearInstance) {
            throw new SynapseValidationException("Invalid Academic Year");
        }

        $startDate = $orgAcademicYearInstance->getStartDate()->format('Y-m-d');
        return $this->orgAcademicYearRepository->findFutureYears($organizationId, $startDate, $numberOfRecords);
    }

    /**
     * Returns the current year_id for the given organization.
     * If there is no current year, returns null.
     *
     * @param int $organizationId
     * @param bool $throwException - if true, throws a SynapseValidationException instead of returning a null value.
     * @return int|null
     * @throws SynapseValidationException
     */
    public function getCurrentOrganizationAcademicYearYearID($organizationId, $throwException = false)
    {
        $currentAcademicYear = $this->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['year_id'])) {
            $currentAcademicYearYearID = $currentAcademicYear['year_id'];
        } else {
            if ($throwException) {
                throw new SynapseValidationException('There is no currently active academic year.');
            } else {
                $currentAcademicYearYearID = null;
            }
        }

        return $currentAcademicYearYearID;
    }
}