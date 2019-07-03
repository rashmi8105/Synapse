<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdateRequest;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDto;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\UploadBundle\Service\Impl\OrgCalcFlagsRiskService;


/**
 * @DI\Service("academicupdatecreate_service")
 */
class AcademicUpdateCreateService extends AcademicUpdateServiceHelper
{

    const SERVICE_KEY = 'academicupdatecreate_service';

    // Member variables

    /**
     * @var array
     */
    private $query;

    /**
     * @var int
     */
    private $totalUpdates = 0;

    //Scaffolding

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    //Services

    /**
     * @var AcademicUpdateService
     */
    private $academicUpdateService;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var alertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrgCalcFlagsRiskService
     */
    private $orgCalcFlagsRiskService;

    /**
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var PersonService
     */
    private $personService;

    //Repositories

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var AcademicUpdateRequestRepository
     */
    private $academicUpdateRequestRepository;

    /**
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCoursesRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgStaticListRepository
     */
    private $orgStaticListRepository;

    /**
     * @var OrgStaticListStudentsRepository
     */
    private $orgStaticListStudentsRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    /**
     * AcademicUpdateCreateService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     * @param Container $container
     * @throws \Exception
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger, $container);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        //Services
        $this->academicUpdateService = $this->container->get(AcademicUpdateService::SERVICE_KEY);
        $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->emailService = $this->container->get(EmailService::SERVICE_KEY);
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgCalcFlagsRiskService = $this->container->get(OrgCalcFlagsRiskService::SERVICE_KEY);
        $this->orgPermissionsetService = $this->container->get(OrgPermissionsetService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        //Repositories
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestRepository::REPOSITORY_KEY);
        $this->ebiConfigRepository = $this->repositoryResolver->getRepository(EbiConfigRepository::REPOSITORY_KEY);
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupRepository = $this->repositoryResolver->getRepository(OrgGroupRepository::REPOSITORY_KEY);
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);
        $this->orgStaticListStudentsRepository = $this->repositoryResolver->getRepository(OrgStaticListStudentsRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Gets the current academic year for an organization.
     *
     * @param int $organizationId
     * @return int
     */
    private function getOrgCurrentAcademicYear($organizationId)
    {

        $date = new \DateTime();
        $yearArray = $this->orgAcademicYearRepository->getCurrentYearId($date->format(SynapseConstant::DATE_YMD_FORMAT), $organizationId);

        if (isset($yearArray[0]['id'])) {
            return $yearArray[0]['id'];
        } else {
            return -1;
        }
    }

    /**
     * Saves an existing academic update.
     *
     * @param AcademicUpdateDto $academicUpdateDto
     * @param Person $loggedInPerson
     * @return boolean
     * @throws SynapseValidationException | ValidationException
     */
    public function saveAcademicUpdateUnderRequest(AcademicUpdateDto $academicUpdateDto, $loggedInPerson)
    {
        $loggedInPersonId = $loggedInPerson->getId();
        $organization = $loggedInPerson->getOrganization();
        $organizationId = $organization->getId();
        $requestDetails = $academicUpdateDto->getRequestDetails();

        //Set the current date time, get whether or not students should be notified,
        $currentDateTime = new \DateTime();
        $notifyStudent = $organization->getSendToStudent();

        //For all of the year / term / course combinations
        foreach ($requestDetails as $requestDetail) {

            //Get the student's academic update details within that course
            $updateDetails = $requestDetail->getstudentDetails();
            //For each student within that course
            foreach ($updateDetails as $updateDetail) {
                // If send_to_student flag is true in organization and student_send flag is also true in academic update request json
                // then only send email to student
                if ($notifyStudent == $updateDetail->getStudentSend()) {
                    $notifyStudent = $updateDetail->getStudentSend();
                } else {
                    $notifyStudent = false;
                }
                //Get the academic update ID of the request to be updated
                $academicUpdateId = $updateDetail->getAcademicUpdateId();
                //student ID of the updated student
                $studentId = $updateDetail->getStudentId();

                $studentObject = $this->personRepository->find(
                    $studentId,
                    new SynapseValidationException("Student ID {$studentId} is not valid at the organization.")
                );

                // validate academic update
                $academicUpdate = $this->academicUpdateRepository->find(
                    $academicUpdateId,
                    new SynapseValidationException("Academic Update ID {$academicUpdateId} is not valid at the organization.")
                );

                //CourseId of the academic update to be updated
                $academicUpdateOrgCourses = $academicUpdate->getOrgCourses();
                $courseId = $academicUpdateOrgCourses->getId();
                $courseName = $academicUpdateOrgCourses->getCourseName();

                //AcademicUpdateRequest Entity of associated request
                $academicUpdateRequest = $academicUpdate->getAcademicUpdateRequest();

                //If the academic update request exists, set the id value. Otherwise, set the variable to NULL.
                //TODO: Figure out why adhoc updates have some links to requests.
                if ($academicUpdateRequest) {
                    $academicUpdateRequestId = $academicUpdateRequest->getId();
                } else {
                    $academicUpdateRequestId = null;
                }

                //Get any academic updates and requests associated with the course, organization, and student. Add the academic update and associated request to the array returned from the repository function.
                $academicUpdatesWithinAssociatedRequests = $this->academicUpdateRequestRepository->getAcademicUpdatesInOpenRequestsForStudent($courseId, $organizationId, $studentId, $currentDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT), $loggedInPersonId);
                $academicUpdatesWithinAssociatedRequests[] = ['academic_update_id' => $academicUpdateId, 'academic_update_request_id' => $academicUpdateRequestId];
                // making sure that the array is unique , else it would end up in sending multiple mails.
                $academicUpdatesWithinAssociatedRequests = array_unique($academicUpdatesWithinAssociatedRequests, SORT_REGULAR);

                $dataProcessingExceptionHandler = new DataProcessingExceptionHandler();
                //For each update/request pair
                foreach ($academicUpdatesWithinAssociatedRequests as $academicUpdateWithinAssociatedRequest) {
                    //Set the ID values of each to a variable
                    $academicUpdateId = $academicUpdateWithinAssociatedRequest['academic_update_id'];
                    $academicUpdateRequestId = $academicUpdateWithinAssociatedRequest['academic_update_request_id'];

                    //Get the entities associated with those IDs
                    $academicUpdate = $this->academicUpdateRepository->find(
                        $academicUpdateId,
                        new SynapseValidationException("Academic Update ID {$academicUpdateId} is not valid at the organization.")
                    );

                    if ($academicUpdateRequestId) {
                        $academicUpdateRequest = $this->academicUpdateRequestRepository->find(
                            $academicUpdateRequestId,
                            new SynapseValidationException("Academic Update Request ID {$academicUpdateRequestId} is not valid at the organization.")
                        );
                    } else {
                        $academicUpdateRequest = null;
                    }


                    //Check to see if the logged in user has access to update the academic update or request.
                    //TODO: Make sure if there's a case of this failing to update academic updates that it does not fail silently. Right now it will fail silently.
                    $hasUpdateAccess = $this->canUserUpdateAcademicUpdate($academicUpdateId, $loggedInPersonId, $academicUpdateRequest);

                    //If the logged in user has update access
                    if ($hasUpdateAccess) {
                        //Update the academic update.
                        $this->setAcademicUpdate($academicUpdate, $updateDetail, $currentDateTime, $loggedInPerson);

                        // Validate entity
                        $this->entityValidationService->validateDoctrineEntity($academicUpdate, $dataProcessingExceptionHandler, "Default", false);

                        $academicUpdateEntityError = $dataProcessingExceptionHandler->getPlainErrors();
                        if (count($academicUpdateEntityError) > 0) {
                            throw new SynapseValidationException($academicUpdateEntityError);
                        }

                        //Flush the academic update repository
                        $this->academicUpdateRepository->flush();
                    } else {
                        $studentName = $studentObject->getFirstname() . " " . $studentObject->getLastname();
                        throw new AccessDeniedException("You are not the faculty assigned to the academic update on student {$studentName} and course {$courseName}.");
                    }
                }
            }
        }

        $this->academicUpdateRepository->flush();
        $this->updateAcademicUpdateDataFile($organizationId);
        return true;
    }

    /**
     * Closes the academic update request if all participating students' academic updates have been closed.
     *
     * @param boolean $notifyStudent
     * @param array $studentIds
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param int $organizationId
     */
    public function checkToCloseAcademicUpdateRequest($notifyStudent, $studentIds, $academicUpdateRequest, $organizationId)
    {
        $this->checkEmailSendToStudent($notifyStudent, $studentIds, $academicUpdateRequest->getOrg());

        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId, true);
        $academicUpdateRequestId = $academicUpdateRequest->getId();
        $requestStatusCounts = $this->academicUpdateRequestRepository->getAcademicUpdateStatusCountsByRequest($academicUpdateRequestId, $orgAcademicYearId);

        if (!array_key_exists('open', $requestStatusCounts) && !array_key_exists('saved', $requestStatusCounts)) {
            $this->totalUpdates = $requestStatusCounts['closed'];
            $faculties = $this->academicUpdateRepository->getAssingedFacultyInfoByRequest($academicUpdateRequest);

            $facultyIds = array_column($faculties, 'personid');
            if ($academicUpdateRequest) {
                $academicUpdateRequest->setStatus('closed');
                $this->academicUpdateRequestRepository->persist($academicUpdateRequest);
                $this->academicUpdateRequestRepository->flush();
                $this->sendClosedEmail($facultyIds, $academicUpdateRequest, $academicUpdateRequest->getPerson()->getId(), $organizationId);

            }
        }
    }


    /**
     * Check if the logged in user can update the academic update.
     *
     * @param int $academicUpdateId
     * @param int $loggedInPersonId
     * @param AcademicUpdateRequest | boolean $academicUpdateRequest
     * @return boolean
     */
    public function canUserUpdateAcademicUpdate($academicUpdateId, $loggedInPersonId, $academicUpdateRequest = true)
    {
        $isAssigned = $this->academicUpdateRepository->getAssignedFacultiesByAcademicUpdate($academicUpdateId, $loggedInPersonId);

        if (! $isAssigned && $academicUpdateRequest) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sends Closed Email notification for Academic Update Request.
     *
     * @param array $facultyIds
     * @param AcademicUpdateRequest $academicUpdateRequest
     * @param int $loggedInUserId
     * @param int $organizationId
     */
    private function sendClosedEmail($facultyIds, $academicUpdateRequest, $loggedInUserId, $organizationId)
    {
        $personDetails = $this->personRepository->getUsersByUserIds($facultyIds);
        $emailKey = 'Academic_Update_Request_Staff_Closed';
        $coordinatorDetails = $this->personRepository->getUsersByUserIds([
            $loggedInUserId
        ]);

        $coordinatorDetails = $coordinatorDetails[0];

        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);
        $updateViewUrl = "";
        if ($systemUrl) {
            $updateViewUrl = $systemUrl . AcademicUpdateConstant::AU_VIEW_CLOSE_URL;
        }
        
        $ebiLang = $this->ebiConfigRepository->findOneByKey('Ebi_Lang');
        $emailTemplate = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailKey, $ebiLang->getValue());
        $emailBodyMessageTemplate = $emailTemplate->getBody();

        $tokenValues['requestor'] = $coordinatorDetails['user_firstname'] . " " . $coordinatorDetails['user_lastname'];
        $tokenValues['requestor_email'] = $coordinatorDetails['user_email'];
        $tokenValues['requestname'] = $academicUpdateRequest->getName();
        $tokenValues['studentupdate'] = $this->totalUpdates;

        if (is_null($academicUpdateRequest->getEmailOptionalMsg())) {
            $tokenValues['optional_message'] = "";
        } else {
            $tokenValues['optional_message'] = $academicUpdateRequest->getEmailOptionalMsg();
        }

        $tokenValues['description'] = $academicUpdateRequest->getDescription();
        $tokenValues['updateviewurl'] = $updateViewUrl . $academicUpdateRequest->getId();
        $tokenValues['duedate'] = $academicUpdateRequest->getDueDate()->format(SynapseConstant::DATE_FORMAT);
        $subject = "Closed : ";
        $facultiesCount = array_count_values($facultyIds);
        $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
        $responseArray = [];
        foreach ($personDetails as $personDetail) {
            $tokenValues['studentupdate'] = $facultiesCount[$personDetail['user_id']];
            $emailBody = $this->emailService->generateEmailMessage($emailBodyMessageTemplate, $tokenValues);
            $responseArray['email_detail'] = array(
                'from' => $from,
                'subject' => $subject . $academicUpdateRequest->getSubject(),
                'bcc' => $emailTemplate->getEmailTemplate()->getBccRecipientList(),
                'body' => $emailBody,
                'emailKey' => $emailKey,
                'to' => trim($personDetail['user_email']),
                'organizationId' => $organizationId
            );
            $emailInstance = $this->emailService->sendEmailNotification($responseArray['email_detail']);

            $this->emailService->sendEmail($emailInstance);
        }
    }

    /**
     * Updates the downloadable data file for an academic update.
     *
     * @param int $organizationId
     */
    public function updateAcademicUpdateDataFile($organizationId)
    {
        $resque = $this->container->get('bcc_resque.resque');
        $createObject = 'Synapse\UploadBundle\Job\UpdateAcademicUpdateDataFile';

        $job = new $createObject();

        $job->args = array(

            'organizationId' => $organizationId
        );
        $resque->enqueue($job, true);
    }

    /**
     * Preparing query and fetching students on the basis of profile item selection for academic term and year.
     *
     * @param string $type
     * @param int $organizationId
     * @param array $profileIsps
     * @return array
     */
    public function getAllStudentsByProfileItemType($type, $organizationId, $profileIsps)
    {
        $yearId = $this->getOrgCurrentacademicYear($organizationId);
        $personOrgMetadata = $this->repositoryResolver->getRepository('SynapseCoreBundle:PersonOrgMetadata');
        if ($type == 'org') {
            $sql = "select distinct md.person_id FROM person_org_metadata md 
            JOIN person p ON p.id=md.person_id
             left join org_metadata as om
                on om.id = md.org_metadata_id 
            where p.organization_id = $organizationId AND  ";
        } else {
            $sql = "select distinct md.person_id FROM person_ebi_metadata md
                JOIN person p ON p.id=md.person_id
                left join ebi_metadata as em
                ON em.id  = md.ebi_metadata_id where p.organization_id = $organizationId AND ";
        }

        $tableAlias = "md";

        foreach ($profileIsps as $profileIsp) {

            if ($type == "org") {
                $typeFieldName = $type . AcademicUpdateConstant::LBL_METADATA_ID_QUERY;
                $termFieldName = "org_academic_periods_id";

                $orgMetaRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgMetadata");
                $orgMetadata = $orgMetaRepo->findOneBy(array(
                    'id' => $profileIsp['id']
                ));
                $profileMetaData = $orgMetadata;
            } else {
                $typeFieldName = $type . AcademicUpdateConstant::LBL_METADATA_ID_QUERY;
                $termFieldName = "org_academic_terms_id";

                $ebiMetataRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiMetadata");
                $ebiMetadata = $ebiMetataRepo->findOneBy(array(
                    'id' => $profileIsp['id'],
                    'definitionType' => 'E'
                ));
                $profileMetaData = $ebiMetadata;
            }

            if ($profileMetaData) {

                $scope = $profileMetaData->getScope();
                if ($scope == "Y") {
                    $profileItemCond = "AND " . $tableAlias . ".org_academic_year_id = " . $yearId;
                    $this->getQueryConditions($sql, $profileIsp, $profileItemCond, $typeFieldName);
                } elseif ($scope == "T") {
                    if (isset($profileIsp['terms']) && count($profileIsp['terms']) > 0) {
                        $termIds = [];
                        foreach ($profileIsp['terms'] as $term) {

                            $termIds[] = $term['term_id'];
                        }

                        $profileItemCond = "AND " . $tableAlias . "." . $termFieldName . " IN (" . implode(",", $termIds) . " )";
                        $this->getQueryConditions($sql, $profileIsp, $profileItemCond, $typeFieldName);

                    } else {

                        $profileItemCond = "AND " . $tableAlias . "." . $termFieldName . " = -1";
                        $this->getQueryConditions($sql, $profileIsp, $profileItemCond, $typeFieldName);
                    }
                } else {
                    $profileItemCond = '';
                    $this->getQueryConditions($sql, $profileIsp, $profileItemCond, $typeFieldName);
                }
            }
        }
        foreach ($this->query as $queryPart) {
            $inArr[] = " person.id IN ($queryPart)";
        }
        $criteria = implode(" AND ", $inArr);
        $orgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($organizationId);
        $finalQuery = "SELECT
                            person.id AS person_id
                        FROM
                            person
                                JOIN
                            org_person_student_year opsy
                                    ON opsy.organization_id = person.organization_id
                                                AND opsy.person_id = person.id
                                                AND opsy.org_academic_year_id = $orgAcademicYearId
                        WHERE
                            person.deleted_at IS NULL
                            AND opsy.deleted_at IS NULL
                            AND $criteria";
        
        $students = $personOrgMetadata->getResultset($finalQuery);
        return $students;
    }

    /**
     * Creating sql condition by item data types and setting it to class level variable for final query.
     * 
     * @param string $sql
     * @param array $profileIsp
     * @param string $profileItemCond
     * @param string $typeFieldName
     */
    private function getQueryConditions($sql, $profileIsp, $profileItemCond, $typeFieldName)
    {
        $itemDataType = $profileIsp['item_data_type'];
        if ($itemDataType == 'S') {
            $ispCatArray = array_column($profileIsp['category_type'], 'value');
            $inString = "'" . implode("','", $ispCatArray) . "'";
            $this->query[] = $sql . " (" . $typeFieldName . " = " . $profileIsp['id'] . " AND metadata_value IN (" . $inString . ")  $profileItemCond ) ";
        } elseif ($itemDataType == 'D') {
            $this->query[] = $sql . " (" . $typeFieldName . " = " . $profileIsp['id'] . " AND STR_TO_DATE(metadata_value, \"%m/%d/%Y\") >= '" . $profileIsp['start_date'] . "' AND STR_TO_DATE(metadata_value, \"%m/%d/%Y\") <= '" . $profileIsp['end_date'] . "' $profileItemCond ) ";
        } elseif ($itemDataType == 'N') {
            if ($profileIsp['is_single']) {
                $this->query[] = $sql . " (" . $typeFieldName . " = " . $profileIsp['id'] . " AND metadata_value = '" . $profileIsp['single_value'] . "'  $profileItemCond ) ";
            } else {
                $this->query[] = $sql . " (" . $typeFieldName . " = " . $profileIsp['id'] . " AND metadata_value >= " . $profileIsp['min_digits'] . " AND metadata_value <= " . $profileIsp['max_digits'] . " $profileItemCond) ";
            }
        }
    }


}