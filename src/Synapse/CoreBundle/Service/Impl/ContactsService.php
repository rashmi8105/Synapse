<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\ContactsTeams;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\job\BulkContactJob;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\ContactsTeamsRepository;
use Synapse\CoreBundle\Repository\ContactTypesLangRepository;
use Synapse\CoreBundle\Repository\ContactTypesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\PermissionConstInterface;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\ActivityLogDto;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Exception\ValidationException;


/**
 * @DI\Service("contacts_service")
 */
class ContactsService extends AbstractService implements PermissionConstInterface
{

    const SERVICE_KEY = 'contacts_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACT_REPO = "SynapseCoreBundle:Contacts";

    const GROUP_ITEMKEY = 'group_item_key';

    const CONTACTS_ID = 'contactsId';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    protected $rbacManager;

    /**
     * @var Resque
     */
    private $resque;

    //Repositories

    /**
     * @var ActivityCategoryLangRepository
     */
    private $activityRepositoryLang;
    /**
     * @var ActivityLogRepository
     */
    private $activityLogRepository;
    /**
     * @var ActivityCategoryRepository
     */
    private $activityRepository;
    /**
     * @var ContactTypesRepository
     */
    private $contactTypesRepository;
    /**
     * @var ContactTypesLangRepository
     */
    private $contactTypesLangRepository;
    /**
     * @var ContactsTeamsRepository
     */
    private $contactsTeamsRepository;
    /**
     * @var ContactsRepository
     */
    private $contactsRepository;
    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;
    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;
    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;
    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;
    /**
     * @var TeamsRepository
     */
    private $teamsRepository;

    //Services

    /**
     * @var ActivityLogService
     */
    private $activityLogService;
    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;
    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;
    /**
     * @var FeatureService
     */
    private $featureService;
    /**
     * @var OrganizationService
     */
    private $orgService;
    /**
     * @var PersonService
     */
    private $personService;
    /**
     * @var RelatedActivitiesService
     */
    private $relatedActivitiesService;
    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;


    /**
     * ContactsService constructor.
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);

        // Repositories
        $this->activityRepositoryLang = $this->repositoryResolver->getRepository(ActivityCategoryLangRepository::REPOSITORY_KEY);
        $this->activityLogRepository = $this->repositoryResolver->getRepository(ActivityLogRepository::REPOSITORY_KEY);
        $this->activityRepository = $this->repositoryResolver->getRepository(ActivityCategoryRepository::REPOSITORY_KEY);
        $this->contactTypesRepository = $this->repositoryResolver->getRepository(ContactTypesRepository::REPOSITORY_KEY);
        $this->contactTypesLangRepository = $this->repositoryResolver->getRepository(ContactTypesLangRepository::REPOSITORY_KEY);
        $this->contactsTeamsRepository = $this->repositoryResolver->getRepository(ContactsTeamsRepository::REPOSITORY_KEY);
        $this->contactsRepository = $this->repositoryResolver->getRepository(ContactsRepository::REPOSITORY_KEY);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository(FeatureMasterLangRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);
        $this->teamsRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);

        // Services
        $this->activityLogService = $this->container->get(ActivityLogService::SERVICE_KEY);
        $this->alertNotificationsService = $this->container->get(AlertNotificationsService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->featureService = $this->container->get(FeatureService::SERVICE_KEY);
        $this->orgService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);
        $this->relatedActivitiesService = $this->container->get(RelatedActivitiesService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);

    }

    /**
     * Creates contacts
     *
     * @param ContactsDto $contactsDto
     * @param bool $isJob
     * @return ContactsDto
     * @throws ValidationException
     */
    public function createContact(ContactsDto $contactsDto, $isJob = false)
    {
        $logContent = $this->loggerHelperService->getLog($contactsDto);
        $this->logger->debug("Creating Contact " . $logContent);

        // Create contacts for multiple students
        $personStudentIds = $contactsDto->getPersonStudentId();
        $personStudentIds = explode(',', $personStudentIds);

        $organizationId = $contactsDto->getOrganizationId();
        $staffId = $contactsDto->getPersonStaffId();

        $this->rbacManager->assertPermissionToEngageWithStudents($personStudentIds, $staffId);

        if (count($personStudentIds) > 1 && $isJob == false) {
            // call job
            $job = new BulkContactJob();

            $jobNumber = uniqid();

            $job->args = array(
                'jobNumber' => $jobNumber,
                'contactDto' => serialize($contactsDto)
            );
            $this->resque->enqueue($job, true);
        } else {
            // as usual call
            $organization = $this->orgService->find($organizationId);
            $shareOptionPermission = $this->getShareOptionPermission($contactsDto, PermissionConstInterface::ASSET_CONTACTS);
            $date = new \DateTime('now');
            $personStaff = $this->personService->findPerson($staffId);
            $activityCategory = $this->activityRepository->find($contactsDto->getReasonCategorySubitemId());
            $this->isActivityCategoryExists($activityCategory);
            $organizationLangDetails = $this->orgService->getOrganizationDetailsLang($personStaff->getOrganization());
            $this->isOrgLangExists($organizationLangDetails);
            $conactTypes = $this->contactTypesRepository->find($contactsDto->getContactTypeId());
            if (!$conactTypes) {
                $this->logger->error("Contacts Service - Find Contact Type - " . "Contact Type Id" . $contactsDto->getContactTypeId() . "Not Found");
                throw new ValidationException([
                    'contacts Type Not Found.'
                ], 'contacts Type Not Found.', 'contacts_type_not_found');
            }

            $contactInteractionParentContactTypeId = $conactTypes->getParentContactTypesId();
            if ($contactInteractionParentContactTypeId) {
                $contactInteraction = $contactInteractionParentContactTypeId->getId();
                $this->isConTypes($conactTypes);

                if ($contactsDto->getDateOfContact())
                    $contactDate = $contactsDto->getDateOfContact();
                else
                    $contactDate = new \DateTime('now');

                $teamShare = $contactsDto->getShareOptions()[0]->getTeamsShare();
                $teamsArray = $contactsDto->getShareOptions()[0]->getTeamIds();
                $lastActivityDate = clone $contactDate;
                $lastActivity = $lastActivityDate->format('m/d/y') . "- Contact";
                $facultyId = $personStaff->getId();
                $reasonText = $activityCategory->getShortName();


                $feature = $this->featureMasterLangRepository->findOneBy(['featureName' => 'Log Contacts']);
                $featureId = $feature->getId();
                foreach ($personStudentIds as $personStudentId) {
                    $contact = new Contacts();
                    $contact->setOrganization($organization);
                    $studentId = $personStudentId;
                    $personStudent = $this->personService->findPerson($studentId);
                    $featureAccess = $this->featureService->verifyFacultyAccessToStudentForFeature($staffId, $organizationId, $studentId, $shareOptionPermission, $featureId);
                    if (!$featureAccess) {
                        if ($isJob) {
                            // Upload jobs fail silently
                            continue;
                        } else {
                            $this->logger->error("Contact Service - Create Contact - Do not have permission to create contact for student -" . $personStudentId);
                            throw new AccessDeniedException('You do not have permission to create a contact');
                        }
                    }

                    // Updating last contact date with the personStudent if contact type is intraction

                    if ($contactInteraction == 1) {
                        $personStudent->setLastContactDate($date);
                    }
                    $contact->setPersonIdStudent($personStudent);
                    $contact->setPersonIdFaculty($personStaff);
                    $contact->setActivityCategory($activityCategory);
                    $contact->setContactTypesId($conactTypes);
                    $contact->setContactDate($contactDate);
                    $contact->setNote($contactsDto->getComment());
                    $contact->setIsDiscussed($contactsDto->getIssueDiscussedWithStudent());
                    $contact->setIsHighPriority($contactsDto->getHighPriorityConcern());
                    $contact->setIsReveal($contactsDto->getIssueRevealedToStudent());
                    $contact->setIsLeaving($contactsDto->getStudentIndicatedToLeave());
                    $contact->setAccessPrivate($contactsDto->getShareOptions()[0]
                        ->getPrivateShare());
                    $contact->setAccessPublic($contactsDto->getShareOptions()[0]
                        ->getPublicShare());
                    $contact->setAccessTeam($teamShare);
                    $contact = $this->contactsRepository->createContact($contact);
                    $this->isContactCreated($contact);
                    if ($teamShare && $contact) {
                        $this->addTeam($contact, $teamsArray);
                    }

                    $timezone = $personStudent->getOrganization()->getTimeZone();
                    $timezone = $this->metadataListValuesRepository->findByListName($timezone);
                    if ($timezone) {
                        $timezone = $timezone[0]->getListValue();
                        Helper::getOrganizationDate($lastActivityDate, $timezone);
                    }
                    $personStudent->setLastActivity($lastActivity);
                    $this->contactsRepository->flush();
                    $contactsDto->setContactId($contact->getId());
                    $contactsDto->setLangId($organizationLangDetails->getLang()->getId());
                    $activityLogDto = new ActivityLogDto();
                    $activityLogDto->setActivityDate($contactDate);
                    $activityLogDto->setActivityType("C");
                    $contactId = $contactsDto->getContactId();
                    $activityLogDto->setContacts($contactId);
                    $activityLogDto->setOrganization($organizationId);
                    $activityLogDto->setPersonIdFaculty($facultyId);
                    $studentId = $personStudent->getId();
                    $activityLogDto->setPersonIdStudent($studentId);
                    $activityLogDto->setReason($reasonText);
                    $this->activityLogService->createActivityLog($activityLogDto);
                    $activityLogId = $contactsDto->getActivityLogId();
                    if (isset($activityLogId)) {
                        $relatedActivitiesDto = new RelatedActivitiesDto();
                        $relatedActivitiesDto->setActivityLog($activityLogId);
                        $relatedActivitiesDto->setContacts($contactsDto->getContactId());
                        $relatedActivitiesDto->setOrganization($organizationId);
                        $this->relatedActivitiesService->createRelatedActivities($relatedActivitiesDto);
                    }
                }
                $this->logger->info(">>>> Created Contact Successfully");

                //After finishing bulk action send notification to logged in person
                if ($isJob) {
                    $this->alertNotificationsService->createNotification('bulk-action-completed', count($personStudentIds) . ' contacts have been created successfully.', $personStaff, null, null, null, null, null, null, null, null, null, null, $contact);
                }
            }
        }
        return $contactsDto;
    }

    private function addTeam($contact, $teamsArray)
    {
        $contactsTeams = '';
        foreach ($teamsArray as $team) {
            if ($team->getIsTeamSelected()) {
                $contactsTeam = new ContactsTeams();
                $team = $this->teamsRepository->find($team->getId());
                $this->isTeamExists($team);
                $contactsTeam->setContactsId($contact);
                $contactsTeam->setTeamsId($team);
                $contactsTeams = $this->contactsTeamsRepository->createContactsTeams($contactsTeam);
            }
        }
        return $contactsTeams;
    }

    public function getContactTypes()
    {
        $contactTypesList = array();
        $contactTypes = array();
        $contactsSubitems = array();
        $cTypeGroupList = $this->contactTypesRepository->getContactTypeGroupList();
        $allContactChild = $this->contactTypesRepository->getContactTypeSubItemList();

        if (count($cTypeGroupList > 0)) {
            $childArray = $this->createParentChild($cTypeGroupList, $allContactChild);
            foreach ($cTypeGroupList as $group) {
                $contactTypes[self::GROUP_ITEMKEY] = $group[self::GROUP_ITEMKEY];
                $contactTypes['group_item_value'] = $group['group_item_value'];
                if (isset($childArray[$group[self::GROUP_ITEMKEY]])) {
                    $contactsSubitems = $childArray[$group[self::GROUP_ITEMKEY]];
                }
                $contactTypes['subitems'] = $contactsSubitems;
                $contactTypesList['contact_type_groups'][] = $contactTypes;
            }
        }
        $this->logger->info(">>>>Get Contact Types");
        return $contactTypesList;
    }

    /**
     * To create parent child relation in array
     *
     * @param unknown $parents
     * @param unknown $allChild
     * @return Array
     */
    private function createParentChild($parents, $allChild)
    {
        $parentChildArray = array();
        $childs = array();
        foreach ($parents as $parent) {
            foreach ($allChild as $child) {
                if ($parent[self::GROUP_ITEMKEY] == $child['parent']) {
                    $childs['subitem_key'] = $child['subitem_key'];
                    $childs['subitem_value'] = $child['subitem_value'];
                    $parentChildArray[$parent[self::GROUP_ITEMKEY]][] = $childs;
                }
            }
        }

        return $parentChildArray;
    }

    /**
     * Edits a contact
     *
     * @param ContactsDto $contactsDto
     * @return ContactsDto
     */
    public function editContacts(ContactsDto $contactsDto)
    {

        $logContent = $this->loggerHelperService->getLog($contactsDto);
        $this->logger->debug("Editing Contact " . $logContent);
        $personStudentId = $contactsDto->getPersonStudentId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$personStudentId]);

        // Editing Contacts
        $contactsDto->getDateOfContact()->setTimeZone(new \DateTimeZone("UTC"));
        $contact = $this->contactsRepository->find($contactsDto->getContactId());

        $organization = $this->orgService->find($contactsDto->getOrganizationId());
        $contact->setOrganization($organization);

        $personStudent = $this->personService->findPerson($contactsDto->getPersonStudentId());
        $contact->setPersonIdStudent($personStudent);

        $personStaff = $this->personService->findPerson($contactsDto->getPersonStaffId());
        $contact->setPersonIdFaculty($personStaff);

        $activityCategory = $this->activityRepository->find($contactsDto->getReasonCategorySubitemId());
        $this->isActivityCategoryExists($activityCategory);

        $orgLang = $this->orgService->getOrganizationDetailsLang($personStaff->getOrganization());
        $this->isOrgLangExists($orgLang);

        $contact->setActivityCategory($activityCategory);
        $conTypes = $this->contactTypesRepository->find($contactsDto->getContactTypeId());
        $this->isConTypes($conTypes);

        $contact->setContactTypesId($conTypes);

        $contact->setContactDate($contactsDto->getDateOfContact());

        $contact->setNote($contactsDto->getComment());

        $contact->setIsDiscussed($contactsDto->getIssueDiscussedWithStudent());
        $contact->setIsHighPriority($contactsDto->getHighPriorityConcern());
        $contact->setIsReveal($contactsDto->getIssueRevealedToStudent());
        $contact->setIsLeaving($contactsDto->getStudentIndicatedToLeave());

        $contact->setAccessPrivate($contactsDto->getShareOptions()[0]
            ->getPrivateShare());
        $contact->setAccessPublic($contactsDto->getShareOptions()[0]
            ->getPublicShare());
        $teamShare = $contactsDto->getShareOptions()[0]->getTeamsShare();

        $contact->setAccessTeam($teamShare);

        $teamsArray = $contactsDto->getShareOptions()[0]->getTeamIds();

        $contactsTeam = $this->contactsTeamsRepository->findBy([
            self::CONTACTS_ID => $contactsDto->getContactId()
        ]);
        if (isset($contactsTeam)) {
            foreach ($contactsTeam as $contactTeam) {
                $this->contactsTeamsRepository->deleteContactsTeam($contactTeam);
            }
        }
        if ($teamShare && $contact) {
            $this->addTeam($contact, $teamsArray);
        }
        $this->contactsRepository->flush();

        // Code to update the activity log date when the date of contact date is modified
        $activityLogObj = $this->activityLogRepository->findOneByContacts($contactsDto->getContactId());
        $activityLogObj->setActivityDate($contactsDto->getDateOfContact());
        $this->activityLogRepository->flush();


        $contactsDto->setContactId($contact->getId());
        $contactsDto->setLangId($orgLang->getId());
        $this->logger->info(">>>>Contacts Edited");
        return $contactsDto;
    }

    /**
     * Delete contact
     * @param integer $id
     */
    public function deleteContact($id)
    {
        $this->logger->debug(">>>> Delete Contact" . $id);
        $contacts = $this->findContact($id);
        $personStudentId = $contacts->getPersonIdStudent()->getId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$personStudentId]);

        $contactTeamsInstance = $this->contactsTeamsRepository->findBy(array(
            self::CONTACTS_ID => $contacts
        ));

        if (!is_null($contactTeamsInstance) && count($contactTeamsInstance) > 0) {
            foreach ($contactTeamsInstance as $contactsTeams) {
                $this->contactsTeamsRepository->deleteContactsTeam($contactsTeams);
            }
        }

        $this->contactsRepository->deleteContact($contacts);
        $this->activityLogService->deleteActivityLogByType($id, 'C');

        $this->contactsRepository->flush();
        $this->logger->info(">>>> Deleted Contact");
    }

    public function findContact($id)
    {
        if (is_object($id)) {
            $contactId = $id->getId();
        } else {
            $contactId = $id;
        }
        $this->logger->debug(">>>>Finding Contact" . $contactId);
        $this->contactsRepository = $this->repositoryResolver->getRepository(self::CONTACT_REPO);
        $contacts = $this->contactsRepository->find($contactId);
        if (!$contacts) {
            $this->logger->error("Contacts Service - Find Contact - " . "Contact Id" . $contactId . "Not Found");
            throw new ValidationException([
                'contacts Not Found.'
            ], 'contacts Not Found.', 'contacts_not_found');
        }
        $this->logger->info(">>>>Finding Contact");
        return $contacts;
    }

    /**
     * Get contacts by contact id
     *
     * @param int $id
     * @return ContactsDto
     */
    public function viewContact($id)
    {
        $this->logger->debug(">>>>View Contact" . $id);

        $contacts = $this->findContact($id);

        $studentId = $contacts->getPersonIdStudent()->getId();

        // check for non participant student
        $this->rbacManager->assertPermissionToEngageWithStudents([$studentId]);

        // find the access level for this particular contact id, then check for permission for that only
        if ($contacts->getAccessPublic()) {
            $checkAssetAccess = self::PERM_CONTACTS_PUBLIC_VIEW;
        } elseif ($contacts->getAccessPrivate()) {
            $checkAssetAccess = self::PERM_CONTACTS_PRIVATE_VIEW;
        } else {
            $checkAssetAccess = self::PERM_CONTACTS_TEAMS_VIEW;
        }

        // check if contact has is team accessed
        if (!$this->rbacManager->hasAssetAccess([$checkAssetAccess], $contacts)) {
            $this->logger->error("Contacts Service - View Contact - Do not have permission to view this contact - " . $id);
            throw new AccessDeniedException(self::CONTACT_VIEW_EXCEPTION);
        }

        $contactsDto = new ContactsDto();
        $contactsDto->setContactId($contacts->getId());

        $facultyId = $contacts->getPersonIdFaculty()->getId();
        $contactsDto->setPersonStaffId($facultyId);
        $contactsDto->setPersonStudentId($contacts->getPersonIdStudent()
            ->getId());
        $contactsDto->setOrganizationId($contacts->getOrganization()
            ->getId());
        $organizationLang = $this->getOrganizationLang($contactsDto->getOrganizationId());
        $orgLangId = $organizationLang->getLang()->getId();
        $contactsDto->setLangId($orgLangId);
        $contactsDto->setContactTypeId($contacts->getContactTypesId()
            ->getId());
        $conTypes = $this->contactTypesRepository->find($contacts->getContactTypesId());
        $this->isConTypes($conTypes);
        $this->isOrgLangExists($organizationLang);
        $contactTypeLang = $this->contactTypesLangRepository->findOneBy(array(
            'contactTypesId' => $conTypes
        ));
        if ($contactTypeLang) {
            $contactsDto->setContactTypeText($contactTypeLang->getDescription());
        }
        $contactsDto->setComment($contacts->getNote());
        $contactsDto->setDateOfContact($contacts->getContactDate());
        $contactsDto->setHighPriorityConcern($contacts->getIsHighPriority());
        $contactsDto->setIssueDiscussedWithStudent($contacts->getIsDiscussed());
        $contactsDto->setIssueRevealedToStudent($contacts->getIsReveal());
        $contactsDto->setReasonCategorySubitemId($contacts->getActivityCategory()
            ->getId());
        $activityCategory = $this->activityRepository->find($contactsDto->getReasonCategorySubitemId());
        $this->isActivityCategoryExists($activityCategory);
        $actCatName = $this->activityRepositoryLang->findOneBy(array(
            'activityCategoryId' => $activityCategory,
            'language' => $organizationLang->getLang()
        ));
        if ($actCatName) {
            $contactsDto->setReasonCategorySubitem($actCatName->getDescription());
        }
        $contactsDto->setStudentIndicatedToLeave($contacts->getIsLeaving());
        $teamDtoData = array();
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare($contacts->getAccessPublic());
        $shareOptionsDto->setPrivateShare($contacts->getAccessPrivate());
        $shareOptionsDto->setTeamsShare($contacts->getAccessTeam());

        $contactsTeam = $this->contactsTeamsRepository->findBy([
            self::CONTACTS_ID => $contacts->getId()
        ]);
        $contactTeamIds = array();
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare($contacts->getAccessPublic());
        $shareOptionsDto->setPrivateShare($contacts->getAccessPrivate());
        $teamShare = $contacts->getAccessTeam();
        $shareOptionsDto->setTeamsShare($teamShare);
        foreach ($contactsTeam as $contactTeam) {
            $contactTeamIds[] = $contactTeam->getTeamsId()->getId();
        }
        $teams = $this->teamMembersRepository->getTeams($facultyId);
        if ($teamShare && !empty($teams)) {
            foreach ($teams as $team) {
                $teamId = $team['team_id'];
                $teamDto = new TeamIdsDto();
                $teamDto->setId($teamId);
                $teamDto->setTeamName($team['team_name']);
                if (in_array($teamId, $contactTeamIds)) {
                    $teamDto->setIsTeamSelected(true);
                } else {
                    $teamDto->setIsTeamSelected(false);
                }
                $teamDtoData[] = $teamDto;
            }
        }
        $shareOptionsDto->setTeamIds($teamDtoData);
        $shareOptionsDtoResponse[] = $shareOptionsDto;
        $contactsDto->setShareOptions($shareOptionsDtoResponse);
        $this->logger->info(">>>>View Contact");
        return $contactsDto;
    }

    public function getOrganizationLang($orgId)
    {
        $this->logger->debug(">>>>Get Organization Lang" . $orgId);
        return $this->orgService->getOrganizationDetailsLang($orgId);
    }

    private function isActivityCategoryExists($activityCategory)
    {
        if (!$activityCategory) {
            throw new ValidationException([
                'Reason category not found.'
            ], 'Reason category not found.', 'reason_category_not_found');
        }
        return $activityCategory;
    }

    private function isOrgLangExists($orgLang)
    {
        if (!$orgLang) {
            throw new ValidationException([
                'Organization language not found.'
            ], 'Organization language not found.', 'organization_language_not_found.');
        }
        return $orgLang;
    }

    private function isConTypes($conTypes)
    {
        if (!$conTypes) {
            throw new ValidationException([
                'Contact type not found.'
            ], 'Contact type not found.', 'contact_type_not_found');
        }
        return $conTypes;
    }

    private function isTeamExists($team)
    {
        if (!$team) {
            throw new ValidationException([
                'Team not found.'
            ], 'Team not found.', 'team_not_found');
        }
    }

    private function isContactCreated($contact)
    {
        if (!$contact) {
            throw new ValidationException([
                'Contact not created.'
            ], 'Contact not created.', 'contact_not_created.');
        }
    }
}