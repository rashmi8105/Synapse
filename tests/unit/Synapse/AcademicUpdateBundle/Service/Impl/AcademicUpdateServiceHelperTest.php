<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use Aws\Common\Signature\time;
use Codeception\Specify;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Utility\DataProcessingUtilityService;


class AcademicUpdateServiceHelperTest extends \Codeception\Test\Unit
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

    public function testCheckEmailSendToStudent(){
        $this->specify("Test to Send email Notification to students", function ($notifyStudent, $studentIds, $academicUpdateRequest, $serverArray, $expectedResult) {

            $mockPerson = $this->getMock('Person', [
                'getUsersByUserIds'
            ]);

            $mockPersonRepository = $this->getMock('PersonRepository', [
                'getUsersByUserIds'
            ]);

            $mockPersonRepository->expects($this->any())
                ->method('getUsersByUserIds')
                ->will($this->returnValue(array(1)));

            $mockEbiConfig = $this->getMock('EbiConfig', [
               'findOneBy',
                'getValue'
            ]);

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', [
                'findOneBy',
                'getValue'
            ]);

            $mockEbiConfigRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($mockEbiConfig));

            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', [
                'getId',
                'getEmailTemplateByKey',
                'getBody',
                'getEmailTemplate',
                'getFromEmailAddress',
                'getSubject'
            ]);
            $emailTemplateObjectMock = $this->getMock('EmailTemplateObjectMock',['getFromEmailAddress', 'getBccRecipientList']);
            $emailTemplateObjectMock->method('getFromEmailAddress')->willReturn('test@mailinator.com');

            $mockEmailTemplateLang->method('getEmailTemplate')->willReturn($emailTemplateObjectMock);


            $mockEmailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', [
                'getEmailTemplateByKey',
                'getBody',
            ]);

            $mockEmailTemplateLangRepository->expects($this->any())
                ->method('getEmailTemplateByKey')
                ->will($this->returnValue($mockEmailTemplateLang));

            $mockOrganization = $this->getMock('Organization', [
               'find',
                'getIsLdapSamlEnabled'
            ]);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', [
                'find',
                'getIsLdapSamlEnabled'
            ]);

            $mockOrganizationRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockOrganization));


            $this->mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        PersonRepository::REPOSITORY_KEY,
                        $mockPersonRepository
                    ],
                    [
                        EbiConfigRepository::REPOSITORY_KEY,
                        $mockEbiConfigRepository
                    ],
                    [
                        EmailTemplateLangRepository::REPOSITORY_KEY,
                        $mockEmailTemplateLangRepository
                    ],
                    [
                        OrganizationRepository::REPOSITORY_KEY,
                        $mockOrganizationRepository
                    ]
                ]);

            $mockDateProcessingUtilityService = $this->getMock('DateProcessingUtilityService', ['encrypt']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl','get','generateCompleteUrl']);
            $mockEmailService = $this->getMock('Email', ['generateEmailMessage', 'sendEmailNotification', 'sendEmail']);
            $mockEmailService->expects($this->any())
                ->method('sendEmail')
                ->will($this->returnValue(true));
            $this->mockContainer->method('get')
                ->willReturnMap([
                    [
                        DataProcessingUtilityService::SERVICE_KEY,
                        $mockDateProcessingUtilityService
                    ],
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ],
                    [
                        EmailService::SERVICE_KEY,
                        $mockEmailService
                    ]
                ]);
            
            $academicUpdateServiceHelper = new AcademicUpdateServiceHelper($this->mockRepositoryResolver, $this->mockLogger, $this->mockContainer);
            $results = $academicUpdateServiceHelper->checkEmailSendToStudent($notifyStudent, $studentIds, $academicUpdateRequest, $serverArray);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' =>
            [
                // Example 1: For valid values
                [
                    1,
                    [5049192],
                    $this->getOrganization(),
                    null,
                    true
                ],
                // Example 2: For null as academicUpdateRequest
                [
                    1,
                    [5049192],
                    $this->getOrganization(),
                    null,
                    true
                ],
                // Example 3: For empty as student Id
                [
                    1,
                    [],
                    $this->getOrganization(),
                    null,
                    true
                ]
            ]
        ]);
    }
    private function getOrganization(){
        $organization = new Organization();
        $organization->setIsLdapSamlEnabled(true);
        return $organization;
    }

}