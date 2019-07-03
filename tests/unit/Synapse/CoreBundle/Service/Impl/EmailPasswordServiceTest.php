<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\EmailTemplateRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\RestBundle\Exception\ValidationException;

class EmailPasswordServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testSendEmailWithInvitationLink()
    {
        $this->specify("Test to send the invitation link to faulty emails.", function ($facultyId, $facultyEmail, $isTemplateAvailable, $personRole, $isEmailSend, $systemUrl, $expectedResult, $expectedErrorMessage, $expectedExceptionClass) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array('get'));
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['find', 'flush']);
            $mockEmailTemplateRepository = $this->getMock('EmailTemplateRepository', ['findOneBy']);
            $mockEmailTemplateLangRepositoryRepository = $this->getMock('EmailTemplateLangRepository', ['findOneBy']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [EmailTemplateRepository::REPOSITORY_KEY, $mockEmailTemplateRepository],
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateLangRepositoryRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);

            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization', 'setActivationToken', 'getFirstname', 'setWelcomeEmailSentDate']);
            $mockPersonRepository->method('find')->willReturn($mockPerson);
            $mockOrganization = $this->getMock('Organization', ['getId', 'getCampusId']);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);
            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', ['getBody', 'getSubject', 'getId']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);
            $mockEmailService = $this->getMock('EmailService', ['sendEmailNotification', 'sendEmail']);
            $mockRoleService = $this->getMock('RoleService', ['getRolesForUser']);
            $mockPasswordService = $this->getMock('PasswordService', ['generateTemporaryActivationToken']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [PasswordService::SERVICE_KEY, $mockPasswordService],
                    [RoleService::SERVICE_KEY, $mockRoleService]
                ]);
            $mockRoleService->method('getRolesForUser')->willReturn($personRole);
            $mockEbiConfig = $this->getMock('EbiConfig', ['getId', 'getValue']);
            if ($personRole['coordinator']) {
                $mockEbiConfigRepository->expects($this->at(0))->method('findOneBy')->with(['key' => 'Coordinator_First_Password_Expiry_Hrs'])->willReturn($mockEbiConfig);
                $mockEbiConfigRepository->expects($this->at(1))->method('findOneBy')->with(['key' => 'Coordinator_Activation_URL_Prefix'])->willReturn($mockEbiConfig);
                $mockEbiConfigRepository->expects($this->at(2))->method('findOneBy')->with(['key' => 'Coordinator_Support_Helpdesk_Email_Address'])->willReturn($mockEbiConfig);
            }
            if ($personRole['faculty']) {
                $mockEbiConfigRepository->expects($this->at(0))->method('findOneBy')->with(['key' => 'Staff_First_Password_Expiry_Hrs'])->willReturn($mockEbiConfig);
                $mockEbiConfigRepository->expects($this->at(1))->method('findOneBy')->with(['key' => 'Staff_Activation_URL_Prefix'])->willReturn($mockEbiConfig);
                $mockEbiConfigRepository->expects($this->at(2))->method('findOneBy')->with(['key' => 'Staff_Support_Helpdesk_Email_Address'])->willReturn($mockEbiConfig);
            }

            if ($isTemplateAvailable) {
                $mockEmailTemplateLangRepositoryRepository->method('findOneBy')->willReturn($mockEmailTemplateLang);
            } else {
                $mockEmailTemplateLangRepositoryRepository->method('findOneBy')->willReturn(false);
            }

            if ($systemUrl) {
                $mockEbiConfigService->method('getSystemUrl')->willReturn($systemUrl);
            } else {
                $mockEbiConfigService->method('getSystemUrl')->willThrowException(new SynapseValidationException());
            }
            $mockEmailTemplate = $this->getMock('EmailTemplate', ['getFromEmailAddress', 'getBccRecipientList']);
            $mockEmailTemplateRepository->method('findOneBy')->willReturn($mockEmailTemplate);
            $emailService = new EmailPasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);
            if (!$isEmailSend) {
                $mockEmailService->method('sendEmail')->willThrowException(new ValidationException());
            }

            try {
                $response = $emailService->sendEmailWithInvitationLink($facultyId, $facultyEmail);
                $this->assertEquals($response['email_detail'], $expectedResult['email_detail']);
            } catch (\Exception $exception) {
                $response = $exception->getMessage();
                verify($exception)->isInstanceOf($expectedExceptionClass);
                $this->assertEquals($expectedErrorMessage, $response);
            }
        }, [
            'examples' => [
                // Error in email configuration should throw an exception.
                [
                    4875623,
                    'faculty@example.com',
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [],
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving appropriate configuration for email. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // SendEmail failed for coordinator should throw ValidationException
                [
                    875689,
                    'coordinator@example.com',
                    true,
                    [
                        'coordinator' => ['id' => 875689],
                        'faculty' => []
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'Validation errors found',
                    '\Synapse\RestBundle\Exception\ValidationException'
                ],
                // System URL returns empty should throw exception.
                [
                    4875623,
                    'faculty@example.com',
                    true,
                    [
                        'coordinator' => ['id' => 875689],
                        'faculty' => [],
                    ],
                    true,
                    NULL,
                    NULL,
                    'An error has occurred with Mapworks. Please contact client services.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Error while accessing email template should throw an exception.
                [
                    4785623,
                    'faculty@example.com',
                    false,
                    [
                        'coordinator' => [],
                        'faculty' => ['id' => 4785623]
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving the email template. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException'
                ],
                // SendEmail failed for faculty should throw ValidationException
                [
                    4785623,
                    'faculty@example.com',
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => ['id' => 4785623]
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'Validation errors found',
                    '\Synapse\RestBundle\Exception\ValidationException'
                ],
                // Send email to faculty
                [
                    4785623,
                    'faculty@example.com',
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [
                            'id' => 4785623
                        ]
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    [
                        'email_sent_status' => 1,
                        'welcome_email_sentDate' => new \DateTime('now'),
                        'email_detail' => [
                            'from' => NULL,
                            'subject' => NULL,
                            'bcc' => NULL,
                            'to' => 'faculty@example.com',
                            'emailKey' => 'Welcome_To_Mapworks',
                            'organizationId' => NULL
                        ],
                        'message' => 'Mail sent successfully to faculty@example.com'
                    ],
                    NULL, NULL
                ],
                // Send email to coordinator
                [
                    875689,
                    'coordinator@example.com',
                    true,
                    [
                        'coordinator' => ['id' => 875689],
                        'faculty' => []
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    [
                        'email_sent_status' => 1,
                        'welcome_email_sentDate' => new \DateTime('now'),
                        'email_detail' => [
                            'from' => NULL,
                            'subject' => NULL,
                            'bcc' => NULL,
                            'to' => 'coordinator@example.com',
                            'emailKey' => 'Welcome_To_Mapworks',
                            'organizationId' => NULL
                        ],
                        'message' => 'Mail sent successfully to coordinator@example.com'
                    ],
                    NULL, NULL
                ],
            ]
        ]);
    }

    public function testSendEmailWithResetPasswordLink()
    {
        $this->specify("Test to send  password reset link to faculty members", function ($facultyEmail, $isPersonAvailable, $isTemplateAvailable, $personRole, $isEmailSend, $systemUrl, $expectedResult, $expectedErrorMessage, $expectedExceptionClass) {
            // Initializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));
            $mockContainer = $this->getMock('Container', array('get'));

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);
            $mockPersonRepository = $this->getMock('PersonRepository', ['flush', 'findOneBy']);
            $mockEmailTemplateRepository = $this->getMock('EmailTemplateRepository', ['findOneBy']);
            $mockEmailTemplateLangRepositoryRepository = $this->getMock('EmailTemplateLangRepository', ['findOneBy']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EbiConfigRepository::REPOSITORY_KEY, $mockEbiConfigRepository],
                    [EmailTemplateRepository::REPOSITORY_KEY, $mockEmailTemplateRepository],
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateLangRepositoryRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository]
                ]);
            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization', 'setActivationToken', 'getFirstname', 'setWelcomeEmailSentDate']);
            if ($isPersonAvailable) {
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findOneBy')->willReturn(false);
            }

            $mockOrganization = $this->getMock('Organization', ['getId', 'getCampusId']);
            $mockPerson->method('getOrganization')->willReturn($mockOrganization);
            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', ['getBody', 'getSubject', 'getId']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);
            $mockEmailService = $this->getMock('EmailService', ['sendEmailNotification', 'sendEmail']);
            $mockRoleService = $this->getMock('RoleService', ['getRolesForUser']);
            $mockPasswordService = $this->getMock('PasswordService', ['generateTemporaryActivationToken']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [EbiConfigService::SERVICE_KEY, $mockEbiConfigService],
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [PasswordService::SERVICE_KEY, $mockPasswordService],
                    [RoleService::SERVICE_KEY, $mockRoleService]
                ]);
            $mockRoleService->method('getRolesForUser')->willReturn($personRole);
            $mockEbiConfig = $this->getMock('EbiConfig', ['getId', 'getValue']);

            if ($personRole['coordinator']) {
                if ($personRole['coordinator']['configuration']) {
                    $returnValue = $mockEbiConfig;
                } else {
                    $returnValue = false;
                }
                $mockEbiConfigRepository->expects($this->at(0))->method('findOneBy')->with(['key' => 'Coordinator_Reset_Password_Expiry_Hrs'])->willReturn($returnValue);
                $mockEbiConfigRepository->expects($this->at(1))->method('findOneBy')->with(['key' => 'Coordinator_ResetPwd_URL_Prefix'])->willReturn($returnValue);
                $mockEbiConfigRepository->expects($this->at(2))->method('findOneBy')->with(['key' => 'Coordinator_Support_Helpdesk_Email_Address'])->willReturn($returnValue);
            }

            if ($personRole['faculty']) {
                if ($personRole['faculty']['configuration']) {
                    $returnValue = $mockEbiConfig;
                } else {
                    $returnValue = false;
                }
                $mockEbiConfigRepository->expects($this->at(0))->method('findOneBy')->with(['key' => 'Staff_First_Password_Expiry_Hrs'])->willReturn($returnValue);
                $mockEbiConfigRepository->expects($this->at(1))->method('findOneBy')->with(['key' => 'Staff_ResetPwd_URL_Prefix'])->willReturn($returnValue);
                $mockEbiConfigRepository->expects($this->at(2))->method('findOneBy')->with(['key' => 'Staff_Support_Helpdesk_Email_Address'])->willReturn($returnValue);
            }

            if ($isTemplateAvailable) {
                $mockEmailTemplateLangRepositoryRepository->method('findOneBy')->willReturn($mockEmailTemplateLang);
            } else {
                $mockEmailTemplateLangRepositoryRepository->method('findOneBy')->willReturn(false);
            }
            if ($systemUrl) {
                $mockEbiConfigService->method('getSystemUrl')->willReturn($systemUrl);
            } else {
                $mockEbiConfigService->method('getSystemUrl')->willThrowException(new SynapseValidationException());
            }
            $mockEmailTemplate = $this->getMock('EmailTemplate', ['getFromEmailAddress', 'getBccRecipientList']);
            $mockEmailTemplateRepository->method('findOneBy')->willReturn($mockEmailTemplate);
            $emailService = new EmailPasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);
            if (!$isEmailSend) {
                $mockEmailService->method('sendEmail')->willThrowException(new ValidationException());
            }
            try {
                $response = $emailService->sendEmailWithResetPasswordLink($facultyEmail);
                $this->assertEquals($response['email_detail'], $expectedResult['email_detail']);
            } catch (\Exception $exception) {
                verify($exception)->isInstanceOf($expectedExceptionClass);
                $this->assertEquals($expectedErrorMessage, $exception->getMessage());
            }
        }, [
            'examples' => [
                // Error in email configuration should throw an exception.
                [
                    'faculty@example.com',
                    false,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [],
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving your user information. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Person is neither faculty nor coordinator should throw an exception.
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [],
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'You are not a faculty or coordinator at this institution.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // System URL returns empty should throw exception.
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [
                            'configuration' => true,
                            'id' => 4785623
                        ]
                    ],
                    true,
                    NULL,
                    NULL,
                    'An error has occurred with Mapworks. Please contact client services.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Configuration values are missing for coordinator should throw an exception
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => ['configuration' => false],
                        'faculty' => [],
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving appropriate configuration for email. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Configuration values are missing for faculty should throw an exception
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => ['configuration' => false],
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving appropriate configuration for email. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Error while accessing email template should throw an exception.
                [
                    'faculty@example.com',
                    true,
                    false,
                    [
                        'coordinator' => [],
                        'faculty' => [
                            'configuration' => true,
                            'id' => 4785623
                        ]
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    NULL,
                    'There was an error retrieving the email template. Please contact support if this error continues.',
                    '\Synapse\RestBundle\Exception\RestException',
                ],
                // Send password reset link to faculty where the sendMail has failed which should throw ValidationException.
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [
                            'configuration' => true,
                            'id' => 4785623
                        ]
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    [],
                    'Validation errors found',
                    '\Synapse\RestBundle\Exception\ValidationException'
                ],
                // Send password reset link to coordinator where the sendMail has failed which should throw ValidationException.
                [
                    'coordinator@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [
                            'configuration' => true,
                            'id' => 9989898
                        ],
                        'faculty' => []
                    ],
                    false,
                    'https://mapworks-qa.skyfactor.com/',
                    [],
                    'Validation errors found',
                    '\Synapse\RestBundle\Exception\ValidationException'
                ],
                // Send password reset link to coordinator
                [
                    'coordinator@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [
                            'configuration' => true,
                            'id' => 4785623
                        ],
                        'faculty' => []
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    [
                        'email_sent_status' => 1,
                        'welcome_email_sentDate' => new \DateTime('now'),
                        'email_detail' => [
                            'from' => NULL,
                            'subject' => NULL,
                            'bcc' => NULL,
                            'to' => 'coordinator@example.com',
                            'emailKey' => 'Forgot_Password_Coordinator',
                            'organizationId' => NULL
                        ],
                        'message' => 'Mail sent successfully to coordinator@example.com'
                    ],
                    NULL, NULL
                ],
                // Send password reset link to faculty
                [
                    'faculty@example.com',
                    true,
                    true,
                    [
                        'coordinator' => [],
                        'faculty' => [
                            'configuration' => true,
                            'id' => 7876786
                        ]
                    ],
                    true,
                    'https://mapworks-qa.skyfactor.com/',
                    [
                        'email_sent_status' => 1,
                        'welcome_email_sentDate' => new \DateTime('now'),
                        'email_detail' => [
                            'from' => NULL,
                            'subject' => NULL,
                            'bcc' => NULL,
                            'to' => 'faculty@example.com',
                            'emailKey' => 'Forgot_Password_Staff',
                            'organizationId' => NULL
                        ],
                        'message' => 'Mail sent successfully to faculty@example.com'
                    ],
                    NULL, NULL
                ]
            ]
        ]);
    }
}