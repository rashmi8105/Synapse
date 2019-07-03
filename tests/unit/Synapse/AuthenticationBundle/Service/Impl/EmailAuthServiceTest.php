<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use Codeception\Specify;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\EbiConfig;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\EmailConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Service\Impl\OrganizationService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Util\UtilServiceHelper;
use Synapse\RestBundle\Exception\ValidationException;

class EmailAuthServiceTest extends \Codeception\Test\Unit
{
    use Specify;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRepositoryResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    protected function _before()
    {
        $this->mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
        $this->mockLogger = $this->getMock('Logger', ['debug', 'error']);
        $this->mockContainer = $this->getMock('Container', ['get']);
    }
    
    public function testSendStudentLoginLinkEmail(){
        $this->specify("Test to send login link to students", function ($username, $expectedResult) {

            $mockPerson = $this->getMock('Person', [
                'findOneBy',
                'getId',
                'getOrganization',
                'getUsername',
                'getFirstname'
            ]);

            $mockPersonRepository = $this->getMock('PersonRepository', [
                'findOneBy',
                'getId',
                'getOrganization',
                'getUsername',
                'getFirstname'
            ]);

            $personObjectMock = $this->getMock('PersonObjectMock', ['getId']);
            $personObjectMock->method('getId')->willReturn(1);

            $mockPerson->method('getOrganization')->willReturn($personObjectMock);

            $mockPersonRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockPerson));

            $mockOrgPersonStudent = $this->getMock('OrgPersonStudent', [
                'findOneBy'
            ]);

            $mockOrgPersonStudentRepository = $this->getMock('OrgPersonStudentRepository', [
                'findOneBy'
            ]);

            $mockOrgPersonStudentRepository->expects($this->once())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrgPersonStudent));

            $mockOrganizationLang = $this->getMock('OrganizationLang', [
               'findOneBy',
                'getLang'
            ]);

            $mockOrganizationLangRepository = $this->getMock('OrganizationLangRepository', [
                'findOneBy',
                'getLang'
            ]);

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            $organizationObjectMock = $this->getMock('OrganizationObjectMock', [
                'getId'
            ]);
            $organizationObjectMock->method('getId')->willReturn(1);

            $mockOrganizationLang->method('getLang')->willReturn($organizationObjectMock);

            $mockOrganizationLangRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockOrganizationLang));

            $mockEbiConfig = new EbiConfig();
            $mockEbiConfig->setValue('https://mapworks-qa-api.skyfactor.com/');
            $mockEbiConfigRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockEbiConfig));

            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', [
                'getEmailTemplateByKey',
                'getBody',
                'getEmailTemplate',
                'getSubject'
            ]);

            $emailTemplateObjectMock = $this->getMock('EmailTemplateObjectMock',['getFromEmailAddress', 'getBccRecipientList']);
            $emailTemplateObjectMock->method('getBccRecipientList')->willReturn('test@mailinator.com');

            $mockEmailTemplateLang->method('getEmailTemplate')->willReturn($emailTemplateObjectMock);

            $mockEmailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', [
                'getEmailTemplateByKey',
                'getBody',
                'getEmailTemplate',
                'getSubject'
            ]);

            $mockEmailTemplateLangRepository->expects($this->any())
                ->method('getEmailTemplateByKey')
                ->will($this->returnValue($mockEmailTemplateLang));

            $mockAccessToken = $this->getMock('AccessToken', [
                'getAccessToken'
            ]);

            $mockAccessTokenRepository = $this->getMock('AccessTokenRepository', [
                'getAccessToken'
            ]);

            $mockAccessTokenRepository->expects($this->any())
                ->method('getAccessToken')
                ->will($this->returnValue($mockAccessToken));

            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        OrgPersonStudentRepository::REPOSITORY_KEY,
                        $mockOrgPersonStudentRepository
                    ],
                    [
                        OrganizationlangRepository::REPOSITORY_KEY,
                        $mockOrganizationLangRepository
                    ],
                    [
                        EmailTemplateLangRepository::REPOSITORY_KEY,
                        $mockEmailTemplateLangRepository
                    ],
                    [
                        AccessTokenRepository::REPOSITORY_KEY,
                        $mockAccessTokenRepository
                    ],
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ]
                ]);

            $mockUserManagementService = $this->getMock('UserManagementService', [
                'isStudentActive'
            ]);

            $mockUserManagementService->expects($this->any())
                ->method('isStudentActive')
                ->will($this->returnValue(1));

            $mockOrganizationService = $this->getMock('Organization', [
                'getOrganizationDetailsLang'
            ]);
            $mockDateProcessingUtilityService = $this->getMock('DateProcessingUtilityService', ['encrypt']);
            $mockEmailService = $this->getMock('Email', ['generateEmailMessage', 'sendEmailNotification', 'sendEmail']);
            if ($username) {
                $mockEmailService->expects($this->any())
                    ->method('sendEmail')
                    ->will($this->returnValue(true));
            }

            $this->mockContainer->method('get')
                ->willReturnMap([
                    [
                        UserManagementService::SERVICE_KEY,
                        $mockUserManagementService
                    ],
                    [
                        OrganizationService::SERVICE_KEY,
                        $mockOrganizationService
                    ],
                    [
                        DataProcessingUtilityService::SERVICE_KEY,
                        $mockDateProcessingUtilityService
                    ],
                    [
                        EmailService::SERVICE_KEY,
                        $mockEmailService
                    ]
                ]);

           $emailAuthService = new EmailAuthService($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $result = $emailAuthService->sendStudentLoginLinkEmail($username);
            $this->assertEquals($result, $expectedResult);
        }, [
            'examples' => [
                // Example 1: For valid email id
                [
                    'MapworksBetaUser04992038@mailinator.com',
                    true
                ],
                // Example 2: For empty email id
                [
                    '',
                    null
                ]
            ]
        ]);
    }
}