<?php

namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\RestBundle\Entity\EmailDto;
use Synapse\RestBundle\Entity\ShareOptionsDto;

class EmailActivityServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testCreateEmail()
    {
        $this->specify("Test to create email activity", function ($email, $expectedResult) {
            // Inititializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockOrganizationRepository = $this->getMock('organizationRepository', array('find'));

            $mockOrganization = $this->getMock('Organization', ["getId"]);

            $mockOrganizationRepository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($mockOrganization));

            $mockAcademicYear = $this->getMock('OrgAcademicYear', array(
                'findCurrentAcademicYearForOrganization'
            ));

            $mockAcademicYearRepository = $this->getMock('OrgAcademicYearRepository', array(
                'findCurrentAcademicYearForOrganization'
            ));

            $mockAcademicYearRepository->expects($this->any())
                ->method('findCurrentAcademicYearForOrganization')
                ->will($this->returnValue($mockAcademicYear));

            $mockPerson = $this->getMock('Person', array(
                'find',
                'getOrganization',
                'getId',
                'setLastActivity',
                'getUsername',
                'setLastContactDate'
            ));

            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'find'
            ));

            $mockPersonRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockPerson));

            $mockPerson->expects($this->any())
                ->method('getOrganization')
                ->will($this->returnValue($mockOrganization));

            $mockPerson->expects($this->any())->method('setLastContactDate')->willReturn('');

            $mockOrganization->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(2));

            $mockActivity = $this->getMock('ActivityCategory', array(
                'find',
                'getShortName'
            ));

            $mockActivityRepository = $this->getMock('ActivityCategoryRepository', array(
                'find'
            ));

            $mockActivityRepository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($mockActivity));

            $mockFeatureMasterLang = $this->getMock('FeatureMasterLang', array(
                'findOneBy',
                'getId'
            ));

            $mockFeatureMasterLangRepository = $this->getMock('FeatureMasterLangRepository', array(
                'findOneBy'
            ));

            $mockFeatureMasterLangRepository->expects($this->once())
                ->method('findOneBy')
                ->will($this->returnValue($mockFeatureMasterLang));

            $mockEmail = $this->getMock('Email', array(
                'createEmail',
                'flush',
                'getId'
            ));

            $mockEmailRepository = $this->getMock('EmailRepository', array(
                'createEmail',
                'flush',
                'getId'
            ));

            $mockEmailRepository->expects($this->once())
                ->method('createEmail')
                ->will($this->returnValue($mockEmail));

            $mockEmailTemplate = $this->getMock('EmailTemplate', array(
                'findOneBy'
            ));

            $mockEmailTemplateRepository = $this->getMock('EmailTemplateRepository', array(
                'findOneBy'
            ));

            $mockEmailTemplateRepository->expects($this->once())
                ->method('findOneBy')
                ->will($this->returnValue($mockEmailTemplate));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:Organization',
                        $mockOrganizationRepository
                    ],
                    [
                        'SynapseAcademicBundle:OrgAcademicYear',
                        $mockAcademicYearRepository
                    ],
                    [
                        'SynapseCoreBundle:Person',
                        $mockPersonRepository
                    ],
                    [
                        'SynapseCoreBundle:ActivityCategory',
                        $mockActivityRepository
                    ],
                    [
                        'SynapseCoreBundle:FeatureMasterLang',
                        $mockFeatureMasterLangRepository
                    ],
                    [
                        'SynapseCoreBundle:Email',
                        $mockEmailRepository
                    ],
                    [
                        'SynapseCoreBundle:EmailTemplate',
                        $mockEmailTemplateRepository
                    ]
                ]);
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockRbacManager = $this->getMock('Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac', array(
                'assertPermissionToEngageWithStudents',
            ));

            $mockFeatureService = $this->getMock('feature_service', array(
                'verifyFacultyAccessToStudentForFeature',
                'assertPermissionToEngageWithStudents'
            ));

            $mockDateUtilityService = $this->getMock('date_utility_service', array(
                'getTimezoneAdjustedCurrentDateTimeForOrganization'
            ));

            $mockActivityLog = $this->getMock('activitylog_service', array(
                'createActivityLog'
            ));

            $mockActivityLogService = $this->getMock('activitylog_service', array(
                'createActivityLog'
            ));

            $mockAcademicYear = $this->getMock('email_service', array(
                'findCurrentAcademicYearForOrganization'
            ));

            $mockAcademicYearService = $this->getMock('AcademicYearService', array(
                'findCurrentAcademicYearForOrganization'
            ));

            $mockLoggerHelperService = $this->getMock('loggerhelper_service', array(
                'getLog'
            ));

            $mockOrgService = $this->getMock('org_service', array(
                'find',
                'getOrganizationDetailsLang'
            ));

            $mockOrganizationService = $this->getMock('org_service', array(
                'find',
                'getOrganizationDetailsLang'
            ));

            $mockFindPerson = $this->getMock('person_service', array(
                'findPerson',
                'getOrganization',
                'getId',
                'setLastActivity'
            ));

            $mockPersonService = $this->getMock('person_service', array(
                'findPerson'
            ));

            $mockRelatedActivitiesService = $this->getMock('relatedactivities_service', array(
                'createRelatedActivities'
            ));

            $mockContainer->method('get')
                ->willReturnMap([
                    [
                        'feature_service',
                        $mockFeatureService
                    ],
                    ['tinyrbac.manager',
                        $mockRbacManager
                    ],
                    ['date_utility_service',
                        $mockDateUtilityService
                    ],
                    ['activitylog_service',
                        $mockActivityLogService
                    ],
                    ['email_service',
                        $mockAcademicYearService
                    ],
                    ['loggerhelper_service',
                        $mockLoggerHelperService
                    ],
                    ['org_service',
                        $mockOrganizationService
                    ],
                    ['person_service',
                        $mockPersonService
                    ],
                    ['relatedactivities_service',
                        $mockRelatedActivitiesService
                    ]
                ]);

            $mockRbacManager->expects($this->once())
                ->method('assertPermissionToEngageWithStudents')
                ->will($this->returnValue(true));

            $mockFeatureService->expects($this->once())
                ->method('verifyFacultyAccessToStudentForFeature')
                ->will($this->returnValue(true));

            $mockOrganizationService->expects($this->any())
                ->method('getOrganizationDetailsLang')
                ->will($this->returnValue(true));

            $mockDateUtilityService->expects($this->once())
                ->method('getTimezoneAdjustedCurrentDateTimeForOrganization')
                ->will($this->returnValue(new \DateTime("01/17/2017")));

            $mockActivityLogService->expects($this->once())
                ->method('createActivityLog')
                ->will($this->returnValue($mockActivityLog));

            $mockAcademicYearService->expects($this->any())
                ->method('findCurrentAcademicYearForOrganization')
                ->will($this->returnValue($mockAcademicYear));

            $mockLoggerHelperService->expects($this->any())
                ->method('getLog')
                ->will($this->returnValue("Creating Email "));

            $mockOrganizationService->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockOrgService));

            $mockPersonService->expects($this->any())
                ->method('findPerson')
                ->will($this->returnValue($mockFindPerson));

            $mockPersonService->expects($this->any())
                ->method('getOrganization')
                ->will($this->returnValue(2));

            $mockRelatedActivitiesService->expects($this->any())
                ->method('createRelatedActivities')
                ->will($this->returnValue($mockRelatedActivitiesService));

            $emailActivity = new EmailActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $createEmail = $emailActivity->createEmail($email, $isJob = false);
            $this->assertEquals($createEmail->getPersonStudentId(), $expectedResult['personStudentId']);
            $this->assertEquals($createEmail->getEmail(), $expectedResult['email']);
            $this->assertEquals($createEmail->getOrganizationId(), $expectedResult['organizationId']);
        }, [
            'examples' => [
                [
                    $this->getEmailDto(
                        array(
                            'organizationId' => 2,
                            'emailId' => '',
                            'personStudentId' => 100,
                            'personStaffId' => 4891006,
                            'email' => 'niteshqa@mailinator.com',
                            'reasonCategorySubitemId' => 19,
                            'reasonCategorySubitem' => '',
                            'emailBccList' => '',
                            'emailSubject' => 'test',
                            'emailBody' => '',
                            'shareOptions' => [
                                [
                                    'privateShare' => '',
                                    'publicShare' => 1,
                                    'teamsShare' => '',
                                    'teamIds' => ''
                                ]
                            ],
                            'activityLogId' => ''
                        )
                    ),
                    array(
                        'organizationId' => 2,
                        'emailId' => '',
                        'personStudentId' => 100,
                        'personStaffId' => 4891006,
                        'email' => 'niteshqa@mailinator.com',
                        'reasonCategorySubitemId' => 19,
                        'reasonCategorySubitem' => '',
                        'emailBccList' => '',
                        'emailSubject' => 'test',
                        'emailBody' => '',
                        'shareOptions' => [
                            [
                                'privateShare' => '',
                                'publicShare' => 1,
                                'teamsShare' => '',
                                'teamIds' => ''
                            ]
                        ],
                        'activityLogId' => ''
                    )
                ],
                [
                    $this->getEmailDto(
                        array(
                            'organizationId' => 2,
                            'emailId' => '',
                            'personStudentId' => 200,
                            'personStaffId' => 4811203,
                            'email' => 'niteshqa@mailinator.com',
                            'reasonCategorySubitemId' => 15,
                            'reasonCategorySubitem' => '',
                            'emailBccList' => '',
                            'emailSubject' => 'test',
                            'emailBody' => '',
                            'shareOptions' => [
                                [
                                    'privateShare' => '',
                                    'publicShare' => 1,
                                    'teamsShare' => '',
                                    'teamIds' => ''
                                ]
                            ],
                            'activityLogId' => ''
                        )
                    ),
                    array(
                        'organizationId' => 2,
                        'emailId' => '',
                        'personStudentId' => 200,
                        'personStaffId' => 4811203,
                        'email' => 'niteshqa@mailinator.com',
                        'reasonCategorySubitemId' => 15,
                        'reasonCategorySubitem' => '',
                        'emailBccList' => '',
                        'emailSubject' => 'test',
                        'emailBody' => '',
                        'shareOptions' => [
                            [
                                'privateShare' => '',
                                'publicShare' => '',
                                'teamsShare' => '',
                                'teamIds' => ''
                            ]
                        ],
                        'activityLogId' => ''
                    )
                ]
            ]
        ]);
    }

    private function getEmailDto($emailData)
    {
        $emailDto = new EmailDto();
        $emailDto->setOrganizationId($emailData['organizationId']);
        $emailDto->setPersonStudentId($emailData['personStudentId']);
        $emailDto->setPersonStaffId($emailData['personStaffId']);
        $emailDto->setReasonCategorySubitemId($emailData['reasonCategorySubitemId']);
        $emailDto->setActivityLogId($emailData['activityLogId']);
        $emailDto->setEmailBccList($emailData['emailBccList']);
        $emailDto->setEmailSubject($emailData['emailSubject']);
        $emailDto->setEmailBody($emailData['emailBody']);
        $emailDto->setShareOptions(
            [
                $this->getShareOption($emailData['shareOptions'][0])

            ]
        );
        $emailDto->setEmail('niteshqa@mailinator.com');
        return $emailDto;
    }

    private function getShareOption($shareOptions)
    {
        $shareOptionsDto = new  ShareOptionsDto();
        $shareOptionsDto->setPrivateShare($shareOptions['privateShare']);
        $shareOptionsDto->setPublicShare($shareOptions['publicShare']);
        $shareOptionsDto->setTeamsShare($shareOptions['teamsShare']);
        $shareOptionsDto->setTeamIds($shareOptions['teamIds']);
        return $shareOptionsDto;
    }

    public function testViewEmail()
    {
        $this->specify("Test to view email activity", function ($email, $organizationId) {

            $mockOrganization = $this->getMock('Organization', ["getId"]);

            $mockOrganizationRepository = $this->getMock('Organization', array(
                'getId'
            ));

            $mockOrganization->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($organizationId));

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockPerson = $this->getMock('Person', array(
                'find',
                'getPersonIdStudent',
                'getId',
                'getFirstname',
                'getLastname'
            ));

            $mockPersonRepository = $this->getMock('PersonRepository', array(
                'find'
            ));

            $mockPersonRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockPerson));

            $mockEmail = $this->getMock('Email', array(
                'find',
                'getPersonIdStudent',
                'getOrganization',
                'getPersonIdFaculty',
                'getActivityCategory',
                'getEmailBccList',
                'getEmailSubject',
                'getEmailBody',
                'getAccessPublic',
                'getAccessPrivate',
                'getAccessTeam'
            ));

            $mockEmailRepository = $this->getMock('Email', array(
                'find',
                'getPersonIdStudent',
                'getOrganization',
                'getPersonIdFaculty',
                'getActivityCategory',
                'getEmailBccList',
                'getEmailSubject',
                'getEmailBody',
                'getAccessPublic',
                'getAccessPrivate',
                'getAccessTeam'
            ));

            $mockEmailRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockEmail));

            $mockEmail->expects($this->any())
                ->method('getPersonIdStudent')
                ->will($this->returnValue($mockPerson));

            $mockEmail->expects($this->any())
                ->method('getOrganization')
                ->will($this->returnValue($mockOrganization));

            $mockEmail->expects($this->any())
                ->method('getPersonIdFaculty')
                ->will($this->returnValue($mockPerson));

            $mockEmail->expects($this->any())
                ->method('getActivityCategory')
                ->will($this->returnValue($mockPerson));

            $mockEmail->expects($this->any())
                ->method('getEmailSubject')
                ->will($this->returnValue('test'));

            $mockEmail->expects($this->any())
                ->method('getEmailBody')
                ->will($this->returnValue('test'));

            $mockOrganizationLang = $this->getMock('OrganizationLang', array(
                'findOneBy',
                'getLang'
            ));

            $mockOrganizationLangRepository = $this->getMock('OrganizationLang', array(
                'findOneBy',
                'getLang'
            ));

            $mockOrganizationLangRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrganizationLang));

            $mockLanguageMaster = $this->getMock('LanguageMaster', array(
                'getId'
            ));

            $mockLanguageMasterRepository = $this->getMock('LanguageMaster', array(
                'getId'
            ));

            $mockOrganizationLang->expects($this->any())
                ->method('getLang')
                ->will($this->returnValue($mockLanguageMaster));

            $mockLanguageMaster->expects($this->any())
                ->method('getId')
                ->will($this->returnValue(1));

            $mockActivity = $this->getMock('ActivityCategory', array(
                'find'
            ));

            $mockActivityRepository = $this->getMock('ActivityCategory', array(
                'find'
            ));

            $mockActivityRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockActivity));

            $mockActivityCategoryLang = $this->getMock('ActivityCategoryLang', array(
                'findOneBy',
                'getDescription'
            ));

            $mockActivityCategoryLangRepository = $this->getMock('ActivityCategoryLang', array(
                'findOneBy',
                'getDescription'
            ));

            $mockActivityCategoryLangRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockActivityCategoryLang));

            $mockEmailTeams = $this->getMock('EmailTeams', array(
                'findBy'
            ));

            $mockEmailTeamsRepository = $this->getMock('EmailTeams', array(
                'findBy'
            ));

            $mockEmailTeamsRepository->expects($this->any())
                ->method('findBy')
                ->will($this->returnValue($mockEmailTeams));

            $mockTeamMembers = $this->getMock('TeamMembers', array(
                'getTeams'
            ));

            $mockTeamMembersRepository = $this->getMock('TeamMembers', array(
                'getTeams'
            ));

            $mockTeamMembersRepository->expects($this->any())
                ->method('getTeams')
                ->will($this->returnValue($mockTeamMembers));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:Email',
                        $mockEmailRepository
                    ],
                    [
                        'SynapseCoreBundle:Person',
                        $mockPersonRepository
                    ],
                    [
                        'SynapseCoreBundle:OrganizationLang',
                        $mockOrganizationLangRepository
                    ],
                    [
                        'SynapseCoreBundle:LanguageMaster',
                        $mockLanguageMasterRepository
                    ],
                    [
                        'SynapseCoreBundle:ActivityCategory',
                        $mockActivityRepository
                    ],
                    [
                        'SynapseCoreBundle:ActivityCategoryLang',
                        $mockActivityCategoryLangRepository
                    ],
                    [
                        'SynapseCoreBundle:EmailTeams',
                        $mockEmailTeamsRepository
                    ],
                    [
                        'SynapseCoreBundle:TeamMembers',
                        $mockTeamMembersRepository
                    ],
                    [
                        'SynapseCoreBundle:Organization',
                        $mockOrganizationRepository
                    ]

                ]);

            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));

            $mockRbacManager = $this->getMock('Synapse\CoreBundle\Security\Authorization\TinyRbac\Rbac', array(
                'assertPermissionToEngageWithStudents',
                'hasAssetAccess'
            ));

            $mockContainer->method('get')
                ->willReturnMap([
                    ['tinyrbac.manager',
                        $mockRbacManager
                    ]
                ]);

            $mockRbacManager->expects($this->any())
                ->method('hasAssetAccess')
                ->will($this->returnValue(true));

            $emailActivity = new EmailActivityService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $viewEmail = $emailActivity->viewEmail($email);
            $this->assertInstanceOf('Synapse\RestBundle\Entity\EmailDto', $viewEmail);
            $this->assertEquals($email, $viewEmail->getEmailId());
            $this->assertEquals($organizationId, $viewEmail->getOrganizationId());
        }, [
            'examples' => [
                [
                    187507,
                    1
                ],
                [
                    187508,
                    2
                ],
                [
                    187509,
                    3
                ]
            ]
        ]);
    }

}