<?php
namespace Synapse\CampusResourceBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CampusResourceBundle\Entity\OrgAnnouncements;
use Synapse\CampusResourceBundle\Entity\OrgAnnouncementsLang;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDeleteDto;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementDto;
use Synapse\CampusResourceBundle\EntityDto\CampusAnnouncementList;
use Synapse\CampusResourceBundle\EntityDto\SystemMessage;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementLangRepository;
use Synapse\CampusResourceBundle\Repository\OrgCampusAnnouncementRepository;
use Synapse\CampusResourceBundle\Service\CampusAnnouncementServiceInterface;
use Synapse\CampusResourceBundle\Util\Constants\CampusAnnouncementConstants;
use Synapse\CoreBundle\Entity\AlertNotifications;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AlertNotificationsRepository;
use Synapse\CoreBundle\Repository\LanguageMasterRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("campusannouncement_service")
 */
class CampusAnnouncementService extends AbstractService
{

    const SERVICE_KEY = 'campusannouncement_service';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    // Services

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationService
     */
    private $orgService;

    // Repositories

    /**
     * @var AlertNotificationsRepository
     */
    private $alertNotificationsRepository;

    /**
     * @var LanguageMasterRepository
     */
    private $langMasterRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCampusAnnouncementLangRepository
     */
    private $orgAnnouncementLangRepository;

    /**
     * @var OrgCampusAnnouncementRepository
     */
    private $orgAnnouncementRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var OrganizationRoleRepository
     */
    private $orgRoleRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *Campus Announcement Service Constructor
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

        //Scaffolding
        $this->container = $container;

        // Services
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);

        // Repositories
        $this->alertNotificationsRepository = $this->repositoryResolver->getRepository(AlertNotificationsRepository::REPOSITORY_KEY);
        $this->langMasterRepository = $this->repositoryResolver->getRepository(LanguageMasterRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgAnnouncementLangRepository = $this->repositoryResolver->getRepository(OrgCampusAnnouncementLangRepository::REPOSITORY_KEY);
        $this->orgAnnouncementRepository = $this->repositoryResolver->getRepository(OrgCampusAnnouncementRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Creates Campus Announcements.
     *
     * @param CampusAnnouncementDto $campusAnnouncementDto
     * @param int $loggedInUser
     * @return CampusAnnouncementDto
     */
    public function createCampusAnnouncement(CampusAnnouncementDto $campusAnnouncementDto, $loggedInUser)
    {
        $logContent = $this->loggerHelperService->getLog($campusAnnouncementDto);
        $this->logger->debug("Create Campus Announcement -loggedInUser -  " . $logContent);

        $startDate = $campusAnnouncementDto->getStartDateTime()->setTimeZone(new \DateTimeZone('UTC'));
        $endDate = $campusAnnouncementDto->getEndDateTime()->setTimeZone(new \DateTimeZone('UTC'));

        $organization = $this->orgService->find($campusAnnouncementDto->getOrganizationId());
        $this->isObjectExist(CampusAnnouncementConstants::CRAETE_CAMPUS_MSG, $organization, CampusAnnouncementConstants::ORG_NOT_FOUND, CampusAnnouncementConstants::ORG_NOT_FOUND_KEY);
        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($campusAnnouncementDto->getOrganizationId(), $loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::CRAETE_CAMPUS_MSG, $isCoordinator, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND_KEY);
        $person = $this->personRepository->findOneById($campusAnnouncementDto->getPersonId());
        $this->isObjectExist(CampusAnnouncementConstants::CRAETE_CAMPUS_MSG, $person, CampusAnnouncementConstants::PERSON_NOT_FOUND, CampusAnnouncementConstants::PERSON_NOT_FOUND_KEY);
        $languageMasterEntity = $this->langMasterRepository->findOneById($campusAnnouncementDto->getLangId());
        $this->isObjectExist(CampusAnnouncementConstants::CRAETE_CAMPUS_MSG, $languageMasterEntity, CampusAnnouncementConstants::LANG_NOT_FOUND, CampusAnnouncementConstants::LANG_NOT_FOUND_KEY);
        
        $this->validateCampusAnnouncementDate($startDate, $endDate);
        $orgAnnouncements = new OrgAnnouncements();
        $orgAnnouncements->setOrganization($organization);
        $orgAnnouncements->setCreatorPersonId($person);
        $orgAnnouncements->setDisplayType($campusAnnouncementDto->getMessageType());
        $orgAnnouncements->setStartDatetime($startDate);
        $orgAnnouncements->setStopDatetime($endDate);
        $orgAnnouncements->setMessageDuration($campusAnnouncementDto->getMessageDuration());
        $orgAnnouncementsInst = $this->orgAnnouncementRepository->persist($orgAnnouncements, $flush = true);
        $orgAnnouncementsLang = new OrgAnnouncementsLang();
        $orgAnnouncementsLang->setOrgAnnouncements($orgAnnouncementsInst);
        $orgAnnouncementsLang->setLang($languageMasterEntity);
        $orgAnnouncementsLang->setMessage($campusAnnouncementDto->getMessage());
        $this->orgAnnouncementLangRepository->persist($orgAnnouncementsLang, $flush = true);
        $campusAnnouncementDto->setId($orgAnnouncementsInst->getId());
        $this->logger->info("Create Campus Announcement is completed");
        return $campusAnnouncementDto;
    }

    private function validateCampusAnnouncementDate($startDate, $endDate)
    {
        if ($endDate <= $startDate) {
            return $this->isObjectExist("Validate Campus Announcement Date", NULL, CampusAnnouncementConstants::ACADEMIC_DATE_ERROR, CampusAnnouncementConstants::ACADEMIC_DATE_ERROR_KEY);
        }
    }

    public function listCampusAnnouncements($type, $loggedInUser, $orgId)
    {
        $this->logger->debug("List Campus Announcements - type - " . $type . CampusAnnouncementConstants::LOGGEDINUSER . "orgId-" . $orgId);
        $responseArray = array();
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ROLE_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::PERSON_REPO);
        $this->orgAnnouncementLangRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ANNOUNCEMENT_LANG_REPO);
        $this->langMasterRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::LANG_MASTER_REPO);
        $this->alertRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:AlertNotifications');
        $organization = $this->orgService->find($orgId);
        $this->isObjectExist(CampusAnnouncementConstants::LIST_CAMPUS, $organization, CampusAnnouncementConstants::ORG_NOT_FOUND, CampusAnnouncementConstants::ORG_NOT_FOUND_KEY);
        $person = $this->personRepository->findOneById($loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::LIST_CAMPUS, $person, CampusAnnouncementConstants::PERSON_NOT_FOUND, CampusAnnouncementConstants::PERSON_NOT_FOUND_KEY);
        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($orgId, $loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::CRAETE_CAMPUS_MSG, $isCoordinator, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND_KEY);
       
        $currentDateTime = new \DateTime('now');
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonFaculty");
        $orgAnnouncementsList = $this->orgAnnouncementLangRepository->listCampusAnnouncements($type, $orgId, $loggedInUser->getId(), $currentDateTime);
        $orgAnnouncementsArr = [];
        $campusAnnouncementListDto = new CampusAnnouncementList();
        if (! empty($orgAnnouncementsList)) {
            $campusAnnouncementListDto->setOrganizationId($orgId);
            $campusAnnouncementListDto->setPersonId($loggedInUser->getId());
            foreach ($orgAnnouncementsList as $orgAnnouncements) {
                $systemMessageDto = new SystemMessage();
                $systemMessageDto->setId($orgAnnouncements[CampusAnnouncementConstants::ID]);
                $startDate = $orgAnnouncements[CampusAnnouncementConstants::START_DATE_TIME];
                $endDate = $orgAnnouncements[CampusAnnouncementConstants::END_DATE_TIME];
                //Helper::setOrganizationDate($startDate, $timeZonestr);
                //Helper::setOrganizationDate($endDate, $timeZonestr);
                $systemMessageDto->setStartDateTime($startDate);
                $systemMessageDto->setEndDateTime($endDate);
                    $systemMessageDto->setMessage($orgAnnouncements[CampusAnnouncementConstants::MESSAGE]);
                    $systemMessageDto->setMessageType($orgAnnouncements[CampusAnnouncementConstants::MESSAGE_TYPE]);
                    $messageDuration = $orgAnnouncements[CampusAnnouncementConstants::MESSAGE_DURATION]?$orgAnnouncements[CampusAnnouncementConstants::MESSAGE_DURATION]:'';
                    $systemMessageDto->setMessageDuration($messageDuration);
                    $orgAnnouncementsArr[] = $systemMessageDto;
            }
            $campusAnnouncementListDto->setSystemMessage($orgAnnouncementsArr);
            $this->logger->info("List Campus Announcements is completed");
        }
        return $campusAnnouncementListDto;
    }

    /**
     * @param CampusAnnouncementDto $campusAnnouncementDto
     * @param Person $loggedInUser
     * @return CampusAnnouncementDto
     */
    public function editCampusAnnouncement($campusAnnouncementDto, $loggedInUser)
    {
        $logContent = $this->loggerHelperService->getLog($campusAnnouncementDto);
        $this->logger->debug("Editing Campus Announcement -loggedInUser -  " . $logContent);
        
        $start = $campusAnnouncementDto->getStartDateTime()->setTimeZone(new \DateTimeZone("UTC"));
        $end = $campusAnnouncementDto->getEndDateTime()->setTimeZone(new \DateTimeZone("UTC"));

        $organization = $this->orgService->find($campusAnnouncementDto->getOrganizationId());
        $this->isObjectExist("Edit Campus Announcement", $organization, "Organization Not Found.", "organization_not_found.");
        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($campusAnnouncementDto->getOrganizationId(), $loggedInUser);
        $this->isObjectExist("Edit Campus Announcement", $isCoordinator, "Coordinator Not Found.", "coordinator_not_found.");
        $person = $this->personRepository->findOneById($campusAnnouncementDto->getPersonId());
        $this->isObjectExist("Edit Campus Announcement", $person, "Person not found.", "person_not_found.");
        $lang = $this->langMasterRepository->findOneById($campusAnnouncementDto->getLangId());
        $this->isObjectExist("Edit Campus Announcement", $lang, "Language not found", "language_not_found");
        $orgAnnouncements = $this->orgAnnouncementRepository->findOneBy(array(
            "orgId" => $campusAnnouncementDto->getOrganizationId(),
            "id" => $campusAnnouncementDto->getId(),
            "creatorPersonId" => $campusAnnouncementDto->getPersonId()
        ));
        $this->isObjectExist("Edit Campus Announcement", $orgAnnouncements, "Campus announcement not found", "campus_announcement_not_found");
        
        $this->validateCampusAnnouncementDate($start, $end);
        $orgAnnouncements->setOrganization($organization);
        $orgAnnouncements->setCreatorPersonId($person);
        $orgAnnouncements->setDisplayType($campusAnnouncementDto->getMessageType());

        // Currently running alert start date time can not be edited
        $currentDateTime = new \DateTime('now');
        if ($currentDateTime <= $orgAnnouncements->getStopDatetime() && $currentDateTime >= $orgAnnouncements->getStartDatetime()) { 
            if($orgAnnouncements->getStartDatetime() == $start){
              }
                else{
                  $this->isObjectExist("Edit Campus Announcement",null,"Currently-running alert start date time can not be edited","Currently_running_alert_start_date_time_can_not_be_edited");
                }
         }
        else{
        $orgAnnouncements->setStartDatetime($start);
        }
        $orgAnnouncements->setStopDatetime($end);
        $orgAnnouncements->setMessageDuration($campusAnnouncementDto->getMessageDuration());
        $orgAnnouncementsLang = $this->orgAnnouncementLangRepository->findOneBy(array(
            'orgAnnouncements' => $campusAnnouncementDto->getId()
        ));
        $orgAnnouncementsLang->setOrgAnnouncements($orgAnnouncements);
        $orgAnnouncementsLang->setLang($lang);
        $orgAnnouncementsLang->setMessage($campusAnnouncementDto->getMessage());
        $this->orgAnnouncementRepository->flush();
        $this->logger->info("Edit Campus Announcement is completed");
        return $campusAnnouncementDto;
    }

    public function deleteCampusAnnouncement($id, $loggedInUser, $orgId)
    {
        $this->logger->debug("Delete Campus Announcement id-" . $id . CampusAnnouncementConstants::LOGGEDINUSER . CampusAnnouncementConstants::ORGID_OBJ . $orgId);
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ROLE_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::PERSON_REPO);
        $this->orgAnnouncementRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ANNOUNCEMENT_REPO);
        $this->orgAnnouncementLangRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ANNOUNCEMENT_LANG_REPO);
        $this->langMasterRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::LANG_MASTER_REPO);
        $organization = $this->orgService->find($orgId);
        $this->isObjectExist(CampusAnnouncementConstants::DELETE_CAMPUS_MSG, $organization, CampusAnnouncementConstants::ORG_NOT_FOUND, CampusAnnouncementConstants::ORG_NOT_FOUND_KEY);
        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($orgId, $loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::DELETE_CAMPUS_MSG, $isCoordinator, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND_KEY);
        $person = $this->personRepository->findOneById($loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::DELETE_CAMPUS_MSG, $person, CampusAnnouncementConstants::PERSON_NOT_FOUND, CampusAnnouncementConstants::PERSON_NOT_FOUND_KEY);
        $orgAnnouncements = $this->orgAnnouncementRepository->findOneBy(array(
            CampusAnnouncementConstants::ID => $id
        ));
        $this->isObjectExist(CampusAnnouncementConstants::DELETE_CAMPUS_MSG, $orgAnnouncements, CampusAnnouncementConstants::CAMPUS_ANNOUNCEMENT_NOT_FOUND, CampusAnnouncementConstants::CAMPUS_ANNOUNCEMENT_NOT_FOUND_KEY);
        $currentDateTime = new \DateTime('now');
        if ($currentDateTime <= $orgAnnouncements->getStopDatetime() && $currentDateTime >= $orgAnnouncements->getStartDatetime()) {
            $orgAnnouncements->setStopDatetime($currentDateTime);
            $this->orgAnnouncementRepository->flush();
        } else {
            $this->orgAnnouncementRepository->deleteCampusAnnouncement($orgAnnouncements);
            $this->orgAnnouncementRepository->flush();
            $this->logger->info("Delete Campus Announcement is completed");
        }
    }

    public function getCampusAnnouncement($id, $loggedInUser, $orgId)
    {
        $this->logger->debug("Campus Announcement - Get Campus Announcement id-" . $id . CampusAnnouncementConstants::LOGGEDINUSER . CampusAnnouncementConstants::ORGID_OBJ . $orgId);
        $responseArray = array();
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ROLE_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::PERSON_REPO);
        $this->orgAnnouncementRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ANNOUNCEMENT_REPO);
        $this->orgAnnouncementLangRepository = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::ORG_ANNOUNCEMENT_LANG_REPO);
        $organization = $this->orgService->find($orgId);
        $this->isObjectExist(CampusAnnouncementConstants::GET_CAMPUS, $organization, CampusAnnouncementConstants::ORG_NOT_FOUND, CampusAnnouncementConstants::ORG_NOT_FOUND_KEY);
        // Validating loggedInUser is Coordinator
        $isCoordinator = $this->orgRoleRepository->getUserCoordinatorRole($orgId, $loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::GET_CAMPUS, $isCoordinator, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND, CampusAnnouncementConstants::COORDINATOR_NOT_FOUND_KEY);
        $person = $this->personRepository->findOneById($loggedInUser);
        $this->isObjectExist(CampusAnnouncementConstants::GET_CAMPUS, $person, CampusAnnouncementConstants::PERSON_NOT_FOUND, CampusAnnouncementConstants::PERSON_NOT_FOUND_KEY);
        $timezone = $organization->getTimezone();
        $timezone = $this->repositoryResolver->getRepository(CampusAnnouncementConstants::META_LIST_REPO)->findByListName($timezone);
        if ($timezone) {
            $timeZonestr = $timezone[0]->getListValue();
        }
        $orgAnnouncement = $this->orgAnnouncementRepository->findOneById($id);
        $this->isObjectExist(CampusAnnouncementConstants::GET_CAMPUS, $orgAnnouncement, CampusAnnouncementConstants::CAMPUS_ANNOUNCEMENT_NOT_FOUND, CampusAnnouncementConstants::CAMPUS_ANNOUNCEMENT_NOT_FOUND_KEY);
        $orgAnnouncementsList = $this->orgAnnouncementLangRepository->getCampusAnnouncement($id, $orgId, $loggedInUser->getId());
        $orgAnnouncementsArr = [];
        $campusAnnouncementListDto = new CampusAnnouncementList();
        $campusAnnouncementListDto->setOrganizationId($orgId);
        $campusAnnouncementListDto->setPersonId($loggedInUser->getId());
        foreach ($orgAnnouncementsList as $orgAnnouncements) {
            $systemMessageDto = new SystemMessage();
            // Setting the Response
            $systemMessageDto->setId($orgAnnouncements[CampusAnnouncementConstants::ID]);
            $startDate = $orgAnnouncements[CampusAnnouncementConstants::START_DATE_TIME];
            $endDate = $orgAnnouncements[CampusAnnouncementConstants::END_DATE_TIME];
            
            $systemMessageDto->setStartDateTime($startDate);
            $systemMessageDto->setEndDateTime($endDate);
            $systemMessageDto->setMessage($orgAnnouncements[CampusAnnouncementConstants::MESSAGE]);
            $systemMessageDto->setMessageType($orgAnnouncements[CampusAnnouncementConstants::MESSAGE_TYPE]);
            $messageDuration = $orgAnnouncements[CampusAnnouncementConstants::MESSAGE_DURATION]?$orgAnnouncements[CampusAnnouncementConstants::MESSAGE_DURATION]:'';
            $systemMessageDto->setMessageDuration($messageDuration);
            $orgAnnouncementsArr[] = $systemMessageDto;
        }
        $campusAnnouncementListDto->setSystemMessage($orgAnnouncementsArr);
        $this->logger->info("Get Campus Announcement is completed");
        return $campusAnnouncementListDto;
    }

    /**
     * Marks a organization announcement as seen for the logged in user. A organization announcement is considered "seen"
     * when it has a read and seen notification in the alert notifications table.
     *
     * TODO:: Fix the way system message notifications are created and marked as seen.
     *
     * @param Person $loggedInUser
     * @param $orgAnnouncementsId
     * @return AlertNotifications
     */
    public function markOrgAnnouncementAsRead($loggedInUser, $orgAnnouncementsId, $displayType)
    {
        if ($loggedInUser) {
            $organization = $loggedInUser->getOrganization();
            $orgAnnouncements = $this->orgAnnouncementRepository->find($orgAnnouncementsId, new SynapseValidationException('Organization announcement was not found'));
            $alertNotifications = new AlertNotifications();
            $alertNotifications->setOrganization($organization);
            $alertNotifications->setPerson($loggedInUser);
            $alertNotifications->setOrgAnnouncements($orgAnnouncements);
            $alertNotifications->setIsRead(true);
            $alertNotifications->setIsSeen(true);
            $alertNotifications->setEvent($displayType);
            $this->alertNotificationsRepository->persist($alertNotifications, $flush = true);
        } else {
            throw new SynapseValidationException("Logged in user not found");
        }

        return $alertNotifications;
    }

    /**
     * @param $str
     * @param $object
     * @param $message
     * @param $key
     *
     * @deprecated This needs to be removed.
     */
    private function isObjectExist($str, $object, $message, $key)
    {
        if (! ($object)) {
            $this->logger->error(" Campus Resource Bundle - Campus Announcement Service - isObjectExist - " . $str . '-------->' . $message . '---->' . $key);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Get all banner type campus announcements
     *
     * @param int $personId
     * @param int $orgId
     * @return CampusAnnouncementList
     */
    public function listBannerOrgAnnouncements($personId, $orgId)
    {
        $currentDateTime = new \DateTime();

        $orgAnnouncementsArray = [];
        $orgAnnouncementsList = $this->orgAnnouncementLangRepository->listBannerOrgAnnouncements($orgId, $personId, $currentDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));
        $campusAnnouncementListDto = new CampusAnnouncementList();
        $campusAnnouncementListDto->setOrganizationId($orgId);
        $campusAnnouncementListDto->setPersonId($personId);
        foreach ($orgAnnouncementsList as $orgAnnouncements) {
            $systemMessageDto = new SystemMessage();
            $systemMessageDto->setId($orgAnnouncements['org_announcements_id']);
            $systemMessageDto->setStartDateTime($orgAnnouncements['start_datetime']);
            $systemMessageDto->setEndDateTime($orgAnnouncements['stop_datetime']);
            $systemMessageDto->setMessage($orgAnnouncements['message']);
            $systemMessageDto->setMessageType($orgAnnouncements['display_type']);
            $systemMessageDto->setMessageDuration($orgAnnouncements['message_duration']);
            $orgAnnouncementsArray[] = $systemMessageDto;
        }
        $campusAnnouncementListDto->setSystemMessage($orgAnnouncementsArray);

        return $campusAnnouncementListDto;
    }
}