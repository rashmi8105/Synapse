<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\ActivityCategoryLangRepository;
use Synapse\CoreBundle\Repository\ActivityCategoryRepository;
use Synapse\CoreBundle\Repository\ActivityLogRepository;
use Synapse\CoreBundle\Repository\ContactsRepository;
use Synapse\CoreBundle\Repository\ContactsTeamsRepository;
use Synapse\CoreBundle\Repository\ContactTypesLangRepository;
use Synapse\CoreBundle\Repository\ContactTypesRepository;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\ContactsDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;


class ContactsServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateContact()
    {
        $this->specify("Test create contacts", function ($isJob, $activityCategory, $organizationLangDetails, $contactTypes, $haveAccessToCreateContact, $isValidContacts, $personIdStudent, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockActivityRepository = $this->getMock('ActivityRepository', ['find']);
            $mockContactsRepository = $this->getMock('ContactsRepository', ['createContact', 'flush']);
            $mockContactTypesRepository = $this->getMock('ContactTypesRepository', ['find']);
            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', ['findOneBy']);
            $mockMetaDataListRepository = $this->getMock('MetadataListValuesRepository', ['findByListName']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityRepository],
                    [ContactsRepository::REPOSITORY_KEY, $mockContactsRepository],
                    [ContactTypesRepository::REPOSITORY_KEY, $mockContactTypesRepository],
                    [FeatureMasterLangRepository::REPOSITORY_KEY, $mockFeatureMasterLangRepository],
                    [MetadataListValuesRepository::REPOSITORY_KEY, $mockMetaDataListRepository]
                ]);
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockActivityLogService = $this->getMock('ActivityLogService', ['createActivityLog']);
            $mockAlertNotificationsService = $this->getMock('AlertNotificationsService', ['createNotification']);
            $mockFeatureService = $this->getMock('FeatureService', ['verifyFacultyAccessToStudentForFeature']);
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);
            $mockOrganizationService = $this->getMock('OrganizationService', [
                'find', 'getOrganizationDetailsLang']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);
            $mockResque = $this->getMock('resque', ['enqueue']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationsService],
                    [FeatureService::SERVICE_KEY, $mockFeatureService],
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [PersonService::SERVICE_KEY, $mockPersonService],
                    [SynapseConstant::RESQUE_CLASS_KEY, $mockResque]
                ]);

            $mockPersonObject = $this->getPersonInstance();
            $mockPersonService->method('findPerson')->willReturn($mockPersonObject);

            if ($activityCategory) {
                $mockActivityCategory = $this->getMock('ActivityCategory', ['getShortName']);
                $mockActivityRepository->method('find')->willReturn($mockActivityCategory);
            }
            if ($organizationLangDetails) {
                $mockOrganizationLangService = $this->getMock('OrganizationLang', ['getLang', 'getId']);
                $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLangService);
                $mockOrganizationLang = $this->getMock('OrganizationLang', ['getId']);
                $mockOrganizationLangService->method('getLang')->willReturn($mockOrganizationLang);
                $mockOrganizationLang->method('getId')->willReturn(1);
                if ($contactTypes) {
                    $mockContactTypes = $this->getMock('ContactTypes', ['getParentContactTypesId', 'getId']);
                    $mockContactTypesRepository->method('find')->willReturn($mockContactTypes);
                    $mockContactTypes->method('getParentContactTypesId')->willReturn($mockContactTypes);
                    $mockFeatureMasterLang = $this->getMock('FeatureMasterLang', ['getId']);
                    $mockFeatureMasterLangRepository->method('findOneBy')->willReturn($mockFeatureMasterLang);
                    if ($haveAccessToCreateContact) {
                        $mockFeatureService->method('verifyFacultyAccessToStudentForFeature')->willReturn($haveAccessToCreateContact);
                        if ($isValidContacts) {
                            $mockContacts = $this->getContactsInstance();
                            $mockContactsRepository->method('createContact')->willReturn($mockContacts);
                        }
                    }
                }
            }
            $contactsService = new ContactsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $contactsDto = $this->createContactsDTO($personIdStudent);
            try {
                $response = $contactsService->createContact($contactsDto, $isJob);
                $this->assertEquals($response, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // check by passing true for $isJob argument
                [true, 1, 1, 1, 1, 1, '8451', $this->createContactsDTO('8451')],
                // It should be enqueued by job since more than one persons are in the contact.
                [false, 1, 1, 1, 1, 1, '8451,90,898', $this->createContactsDTO('8451,90,898')],
                // Create a contact when reason category is not there
                [false, 0, 1, 1, 1, 1, '365989', 'Reason category not found.'],
                // Create a contact when Organization language is not there
                [false, 1, 0, 1, 1, 1, '365989', 'Organization language not found.'],
                // Contact type is missing when creating contacts - this should throw exception.
                [false, 1, 1, 0, 1, 1, '365989', 'contacts Type Not Found.'],
                // Trying to create contact when there is no permission which should throw an exception
                [false, 1, 1, 1, 0, 1, '365989', 'You do not have permission to create a contact'],
                // Contact is not created due to some invalid values.
                [false, 1, 1, 1, 1, 0, '365989', 'Contact not created.'],
                // Create contact with valid inputs
                [false, 1, 1, 1, 1, 1, '458213', $this->createContactsDTO('458213')],
            ]
        ]);
    }

    public function testEditContacts()
    {
        $this->specify("Test edit contacts", function ($activityCategory, $organizationLangDetails, $contactTypes, $personIdStudent, $expectedResult) {

            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockActivityCategoryRepository = $this->getMock('activityRepository', ['find']);
            $mockActivityLogRepository = $this->getMock('ActivityLogRepository', ['findOneByContacts', 'flush']);
            $mockContactTypesRepository = $this->getMock('contactTypesRepository', ['find']);
            $mockContactTeamsRepository = $this->getMock('contactsTeamsRepository', ['findBy']);
            $mockContactsRepository = $this->getMock('ContactsRepository', ['find', 'flush']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [ActivityLogRepository::REPOSITORY_KEY, $mockActivityLogRepository],
                    [ContactsRepository::REPOSITORY_KEY, $mockContactsRepository],
                    [ContactTypesRepository::REPOSITORY_KEY, $mockContactTypesRepository],
                    [ContactsTeamsRepository::REPOSITORY_KEY, $mockContactTeamsRepository],
                ]);

            $mockContacts = $this->getMock('Contacts', ['setOrganization', 'setContactDate', 'setNote', 'setIsDiscussed', 'setIsHighPriority', 'setIsReveal', 'setIsLeaving', 'setAccessPrivate',
                'setPersonIdStudent', 'setPersonIdFaculty', 'setActivityCategory', 'setContactTypesId', 'setAccessPublic', 'setAccessTeam', 'getId']);
            $mockContactsRepository->method('find')->willReturn($mockContacts);

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));


            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);
            $mockOrganizationService = $this->getMock('OrganizationService', ['find', 'getOrganizationDetailsLang']);
            $mockPersonService = $this->getMock('PersonService', ['findPerson']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [LoggerHelperService::SERVICE_KEY, $mockLoggerHelperService],
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService],
                    [PersonService::SERVICE_KEY, $mockPersonService]
                ]);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockPersonService->method('findPerson')->willReturn($mockPerson);

            $mockOrganizationService->method('find')->willReturn(new Organization());
            if ($activityCategory) {
                $mockActivityCategoryRepository->method('find')->willReturn(new ActivityCategory());
            }
            if ($organizationLangDetails) {

                $mockOrganizationLang = $this->getMock('OrganizationLang', ['getId', 'getLang']);
                $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLang);
                $mockOrganizationLang->method('getId')->willReturn(1);
            }
            if ($contactTypes) {
                $mockContactTypes = $this->getMock('ContactTypes', ['getParentContactTypesId', 'getId']);
                $mockContactTypesRepository->method('find')->willReturn($mockContactTypes);
            }
            $mockContacts->method('getId')->willReturn(1);
            $mockActivityLog = $this->getMock('ActivityLog', ['find', 'setActivityDate']);
            $mockActivityLogRepository->method('findOneByContacts')->willReturn($mockActivityLog);

            $contactsService = new ContactsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $contactsDto = $this->createContactsDTO($personIdStudent);
            try {
                $response = $contactsService->editContacts($contactsDto);
                $this->assertEquals($response, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // edit contacts
                [1, 1, 1, '658475', $this->createContactsDTO('658475')],
                // edit contacts
                [1, 1, 1, '74582,63256,854126', $this->createContactsDTO('74582,63256,854126')],
                // edit contacts - reason category is not there, this should throw an exception
                [0, 1, 1, '365989', 'Reason category not found.'],
                // edit a contact when Organization language is not there, this should throw an exception
                [1, 0, 1, '365989', 'Organization language not found.'],
                // Contact type is missing for edit contacts - this should throw exception.
                [1, 1, 0, '365989', 'Contact type not found.'],
                // Create contact with valid inputs
                [1, 1, 1, '458213', $this->createContactsDTO('458213')],
            ]
        ]);
    }

    public function testDeleteContact()
    {
        $this->specify("Test delete contacts", function ($contactsId, $isContactsAvailable, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);

            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockContactsRepository = $this->getMock('ContactsRepository', ['find', 'flush', 'deleteContact']);
            $mockContactTeamsRepository = $this->getMock('contactsTeamsRepository', ['findBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ContactsRepository::REPOSITORY_KEY, $mockContactsRepository],
                    [ContactsTeamsRepository::REPOSITORY_KEY, $mockContactTeamsRepository]
                ]);

            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents']);
            $mockActivityLogService = $this->getMock('ActivityLogService', ['deleteActivityLogByType']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService]
                ]);

            if ($isContactsAvailable) {
                $mockContacts = $this->getMock('Contacts', ['getPersonIdStudent']);
                $mockPerson = $this->getMock('Person', ['getId']);
                $mockContactsRepository->method('find')->willReturn($mockContacts);
                $mockContacts->method('getPersonIdStudent')->willReturn($mockPerson);
            }
            $contactsService = new ContactsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $contactsService->deleteContact($contactsId);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }
        }, [
            'examples' => [
                // Contact Not found, throw an error.
                [0012, 0, 'contacts Not Found.'],
                // Delete a contact
                [5847, 1, 1],
                // Invalid contact id
                ['invalid', 0, 'contacts Not Found.'],
            ]
        ]);
    }

    public function testViewContact()
    {
        $this->specify("Test view contacts", function ($contactsId, $isContactsAvailable, $hasAccess, $isContactsTypeAvailable, $activityCategoryExist, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);

            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $mockActivityCategoryRepository = $this->getMock('ActivityCategoryRepository', ['find']);
            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLangRepository', ['findOneBy']);
            $mockContactsRepository = $this->getMock('ContactsRepository', ['find', 'flush', 'deleteContact']);
            $mockContactTeamsRepository = $this->getMock('contactsTeamsRepository', ['findBy']);
            $mockContactTypesRepository = $this->getMock('ContactTypesRepository', ['find']);
            $mockContactTypesLangRepository = $this->getMock('ContactTypesLangRepository', ['findOneBy']);
            $mockTeamMembersRepository = $this->getMock('TeamMembersRepository', ['getTeams']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [ActivityCategoryRepository::REPOSITORY_KEY, $mockActivityCategoryRepository],
                    [ActivityCategoryLangRepository::REPOSITORY_KEY, $mockActivityCategoryLangRepository],
                    [ContactsRepository::REPOSITORY_KEY, $mockContactsRepository],
                    [ContactsTeamsRepository::REPOSITORY_KEY, $mockContactTeamsRepository],
                    [ContactTypesRepository::REPOSITORY_KEY, $mockContactTypesRepository],
                    [ContactTypesLangRepository::REPOSITORY_KEY, $mockContactTypesLangRepository],
                    [TeamMembersRepository::REPOSITORY_KEY, $mockTeamMembersRepository]
                ]);

            $mockManagerService = $this->getMock('Manager', ['assertPermissionToEngageWithStudents', 'hasAssetAccess']);
            $mockActivityLogService = $this->getMock('ActivityLogService', ['deleteActivityLogByType']);
            $mockOrganizationService = $this->getMock('orgService', ['getOrganizationDetailsLang']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [Manager::SERVICE_KEY, $mockManagerService],
                    [ActivityLogService::SERVICE_KEY, $mockActivityLogService],
                    [OrganizationService::SERVICE_KEY, $mockOrganizationService]
                ]);
            if ($isContactsAvailable) {
                $mockContacts = $this->getMock('Contacts', ['getPersonIdStudent', 'getAccessPublic', 'getAccessPrivate', 'getId', 'getPersonIdFaculty', 'getOrganization',
                    'getContactTypesId', 'getNote', 'getContactDate', 'getIsHighPriority', 'getIsDiscussed', 'getIsReveal', 'getActivityCategory', 'getIsLeaving', 'getAccessTeam']);
                $mockPerson = $this->getMock('Person', ['getId']);
                $mockPersonFaculty = $this->getMock('Person', ['getId']);
                $mockOrganization = $this->getMock('Organization', ['getId']);
                $mockContactsRepository->method('find')->willReturn($mockContacts);
                $mockContacts->method('getPersonIdStudent')->willReturn($mockPerson);
                $mockContacts->method('getPersonIdFaculty')->willReturn($mockPersonFaculty);
                $mockContacts->method('getOrganization')->willReturn($mockOrganization);

                $mockManagerService->method('hasAssetAccess')->willReturn($hasAccess);
                $mockOrganizationLangService = $this->getMock('OrganizationLang', ['getLang', 'getId']);
                $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLangService);
                $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
                $mockOrganization->method('getId')->willReturn(203);
                $mockLanguageMaster->method('getId')->willReturn(1);
                $mockContacts->method('getId')->willReturn(1);
                $mockPerson->method('getId')->willReturn(34);

                $mockOrganizationLangService->method('getLang')->willReturn($mockLanguageMaster);
                $mockContactTypes = $this->getMock('ContactTypes', ['getParentContactTypesId', 'getId']);
                $mockContacts->method('getContactTypesId')->willReturn($mockContactTypes);
                $mockContactTypesRepository->method('find')->willReturn($isContactsTypeAvailable);
                $contactDate = new \DateTime('11/08/2017');
                $mockContacts->method('getContactDate')->willReturn($contactDate);
                $mockContacts->method('getNote')->willReturn('Contact faculty');

                $mockActivityCategory = $this->getMock('ActivityCategory', ['getId']);
                $mockContacts->method('getActivityCategory')->willReturn($mockActivityCategory);
                $mockActivityCategoryRepository->method('find')->willReturn($activityCategoryExist);
                $mockContactTeamsRepository->method('findBy')->willReturn([]);

                $mockPersonFaculty->method('getId')->willReturn(451263);
                $mockActivityCategory->method('getId')->willReturn(1);
                $mockContactTypes->method('getId')->willReturn(1);
                $mockContacts->method('getIsDiscussed')->willReturn(true);
            }
            $contactsService = new ContactsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            try {
                $response = $contactsService->viewContact($contactsId);
                $this->assertEquals($response, $expectedResult);
            } catch (\Exception $e) {
                $this->assertEquals($expectedResult, $e->getMessage());
            }

        }, [
            'examples' => [
                // Contact Not found, throw an error.
                [0012, 0, 1, 1, 1, 'contacts Not Found.'],
                // User has no access to view the contacts.
                [5847, 1, 0, 1, 1, 'contact'],
                // Invalid contact id
                ['invalid', 0, 1, 1, 1, 'contacts Not Found.'],
                // View the contact where the type is not found.
                [5847, 1, 1, 0, 1, 'Contact type not found.'],
                // Contact where the reason category not found.
                [5847, 1, 1, 1, 0, 'Reason category not found.'],
                // View the valid contacts
                [5847, 1, 1, 1, 1, $this->createContactsDTO(34)]
            ]
        ]);
    }

    /**
     * Create Contacts DTO
     * @param string $personIdStudent
     * @return ContactsDto
     */
    private function createContactsDTO($personIdStudent)
    {
        $contactsDto = new ContactsDto();
        $contactsDto->setContactId(1);
        $contactsDto->setPersonStaffId(451263);
        $contactsDto->setPersonStudentId($personIdStudent);
        $contactsDto->setOrganizationId(203);
        $contactsDto->setLangId(1);
        $contactsDto->setContactTypeId(1);
        $contactsDto->setComment('Contact faculty');
        $contactDate = new \DateTime('11/08/2017');
        $contactsDto->setDateOfContact($contactDate);
        $contactsDto->setHighPriorityConcern(false);
        $contactsDto->setIssueDiscussedWithStudent(true);
        $contactsDto->setIssueRevealedToStudent(false);
        $contactsDto->setReasonCategorySubitemId(1);
        $shareOptionsDto = new ShareOptionsDto();
        $shareOptionsDto->setPublicShare(false);
        $shareOptionsDto->setPrivateShare(false);
        $shareOptionsDto->setTeamsShare(false);
        $shareOptionsDto->setTeamIds([]);
        $shareOptionsDtoResponse[] = $shareOptionsDto;
        $contactsDto->setShareOptions($shareOptionsDtoResponse);
        return $contactsDto;
    }

    /**
     * Create Person Object
     *
     * @param string $externalId
     * @return Person
     */
    private function getPersonInstance($externalId = '451284')
    {
        $person = new Person();
        $person->setExternalId($externalId);
        $organization = new Organization();
        $person->setOrganization($organization);
        return $person;
    }

    /**
     * Create Contacts Object
     *
     * @return Contacts
     */
    private function getContactsInstance()
    {
        $contacts = new Contacts();
        $contacts->setId(1);
        return $contacts;
    }
}