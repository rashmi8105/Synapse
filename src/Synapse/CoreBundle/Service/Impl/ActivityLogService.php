<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\ActivityLog;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Service\ActivityLogServiceInterface;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("activitylog_service")
 */
class ActivityLogService extends AbstractService implements ActivityLogServiceInterface
{

    const SERVICE_KEY = 'activitylog_service';

    private $activityLogRepository;

    private $appointmentsRepository;

    private $contactsRepository;

    private $noteRepository;

    private $referralRepository;

    private $personRepository;

    private $organizationRepository;

    /**
     *
     * @var container
     */
    private $container;

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITYLOG_REPO = "SynapseCoreBundle:ActivityLog";

    const FIELD_ACTIVITYTYPE = "activityType";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const RELATEDACTIVITIES_REPO = 'SynapseCoreBundle:RelatedActivities';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger, $container);
        $this->container = $container;
    }

    private function createContact($activityLogDto, $activityLogObj)
    {
        $this->contactsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Contacts");
        if (! is_null($activityLogDto->getContacts())) {
            $contacts = $this->contactsRepository->findById($activityLogDto->getContacts());
            if ($contacts) {
                $activityLogObj->setContacts($contacts[0]);
            }
        }
    }
    
    private function createEmail($activityLogDto, $activityLogObj)
    {
        $this->emailRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Email");
        if (! is_null($activityLogDto->getEmail())) {
            $email = $this->emailRepository->findById($activityLogDto->getEmail());
            if ($email) {
                $activityLogObj->setEmail($email[0]);
            }
        }
    }

    private function createNote($activityLogDto, $activityLogObj)
    {
        $this->noteRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Note");
        
        if (! is_null($activityLogDto->getNote())) {
            $note = $this->noteRepository->findById($activityLogDto->getNote());
            if ($note) {
                $activityLogObj->setNote($note[0]);
            }
        }
    }

    /**
     *
     * @param ActivityLogDto $activityLogDto            
     * @return ActivityLog
     */
    public function createActivityLog(ActivityLogDto $activityLogDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($activityLogDto);
        $this->logger->debug("Creating Activity Log " . $logContent);
        
        $this->activityLogRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:ActivityLog");
        $this->appointmentsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Appointments");
        $this->contactsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Contacts");
        $this->noteRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Note");
        $this->referralRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Referrals");
        $this->personRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $activityLogObj = new ActivityLog();
        $activityLogObj->setActivityDate($activityLogDto->getActivityDate());
        $activityLogObj->setActivityType($activityLogDto->getActivityType());
        
        if (! is_null($activityLogDto->getAppointments())) {
            $appointment = $this->appointmentsRepository->findById($activityLogDto->getAppointments());
            if ($appointment) {
                $activityLogObj->setAppointments($appointment[0]);
            }
        }
        
        $this->createContact($activityLogDto, $activityLogObj);
        $this->createNote($activityLogDto, $activityLogObj);
        $this->createEmail($activityLogDto, $activityLogObj);
        
        if (! is_null($activityLogDto->getOrganization())) {
            $org = $this->organizationRepository->findById($activityLogDto->getOrganization());
            if ($org) {
                $activityLogObj->setOrganization($org[0]);
            }
        }
        
        $faculty = $this->personRepository->findById($activityLogDto->getPersonIdFaculty());
        if ($faculty) {
            $activityLogObj->setPersonIdFaculty($faculty[0]);
        }
        
        $student = $this->personRepository->findById($activityLogDto->getPersonIdStudent());
        if ($student) {
            $activityLogObj->setPersonIdStudent($student[0]);
        }
        
        if (! is_null($activityLogDto->getReason())) {
            $reason = $activityLogDto->getReason();
            $activityLogObj->setReason($reason);
        }
        if (! is_null($activityLogDto->getReferrals())) {
            $referral = $this->referralRepository->findById($activityLogDto->getReferrals());
            if ($referral) {
                $activityLogObj->setReferrals($referral[0]);
            }
        }
        
        $activityLog = $this->activityLogRepository->createActivityLog($activityLogObj);
        $this->activityLogRepository->flush();
        $this->logger->info(">>>> Created Activity Log");
        return $activityLog;
    }

    /**
     * delete activity log entry from log table
     * It will call from other methods
     * So auto commit is false here
     *
     * @param int $actvityId            
     * @param string $acticityType
     *            Retrun null if success, throw validationException if Activity Type not found
     */
    public function deleteActivityLogByType($actvityId, $acticityType, $student_id = null)
    {
        $this->logger->debug(">>>> Delete Activity Log By Type" . "activityId" . $actvityId . "activityType" . $acticityType);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(self::ACTIVITYLOG_REPO);
        $this->relatedActivitiesRepository = $this->repositoryResolver->getRepository(self::RELATEDACTIVITIES_REPO);
        $activityLogs = null;
        $relatedActivities = null;
        switch ($acticityType) {
            case 'C':
                $activityLogs = $this->activityLogRepository->findBy([
                    self::FIELD_ACTIVITYTYPE => $acticityType,
                    'contacts' => $actvityId
                ]);
                $relatedActivities = $this->relatedActivitiesRepository->findByContacts($actvityId);
                break;
            case 'R':
                $activityLogs = $this->activityLogRepository->findBy([
                    self::FIELD_ACTIVITYTYPE => $acticityType,
                    'referrals' => $actvityId
                ]);
                $relatedActivities = $this->relatedActivitiesRepository->findByReferral($actvityId);
                break;
            case 'N':
                $activityLogs = $this->activityLogRepository->findBy([
                    self::FIELD_ACTIVITYTYPE => $acticityType,
                    'note' => $actvityId
                ]);
                $relatedActivities = $this->relatedActivitiesRepository->findByNote($actvityId);
                break;
            case 'A':
                if ($student_id) {
                    $activityLogs = $this->activityLogRepository->findBy([
                        self::FIELD_ACTIVITYTYPE => $acticityType,
                        'appointments' => $actvityId,
                        'personIdStudent' => $student_id
                    ]);
                } else {
                    $activityLogs = $this->activityLogRepository->findBy([
                        self::FIELD_ACTIVITYTYPE => $acticityType,
                        'appointments' => $actvityId
                    ]);
                    $relatedActivities = $this->relatedActivitiesRepository->findByAppointment($actvityId);
                }
                
                break;
            case 'E':
                $activityLogs = $this->activityLogRepository->findBy([
                    self::FIELD_ACTIVITYTYPE => $acticityType,
                    'email' => $actvityId
                    ]);
                $relatedActivities = $this->relatedActivitiesRepository->findByEmail($actvityId);
                break;
            default:
                throw new ValidationException([
                    "Activity Type Not Found"
                ], "Activity Type Not Found", "activitytype_not Found");
        }
        
        if ($relatedActivities && (count($relatedActivities) > 0)) {
            foreach ($relatedActivities as $relatedActivitie) {
                $this->relatedActivitiesRepository->remove($relatedActivitie);
            }
            $this->relatedActivitiesRepository->flush();
        }
        
        if ($activityLogs && count($activityLogs) > 0) {
            foreach ($activityLogs as $activityLog) {
                $this->activityLogRepository->remove($activityLog);
            }
        }
    }
}
