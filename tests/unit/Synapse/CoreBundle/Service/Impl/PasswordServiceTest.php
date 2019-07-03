<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\RestBundle\Entity\CreatePasswordDto;
use Synapse\RestBundle\Entity\EmailNotificationDto;
use Synapse\RestBundle\Exception\ValidationException;

class PasswordServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    private $organizationLangArray = [
        0 => [
            'organization_name' => 'Synapse',
            'nick_name' => 'Skyfactor',
            'description' => 'Test',
        ],
        1 => [
            'organization_name' => 'Test Synapse',
            'nick_name' => 'Test Skyfactor',
            'description' => 'Testing',
        ]
    ];

    public function testGetOrganizationLang()
    {
        $this->specify("Test getOrganizationLang", function ($organizationId, $exampleIndex, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            // Mock organization service
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);
            if ($organizationId != null && $organizationId > 0) {
                $organizationLang = $this->getOrganizationLangResponse($this->organizationLangArray, $exampleIndex);
            } else {
                $organizationLang = [];
            }
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($organizationLang);

            $mockContainer->method('get')->willReturnMap([
                [OrganizationService::SERVICE_KEY, $mockOrganizationService]
            ]);

            $passwordService = new PasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $results = $passwordService->getOrganizationLang($organizationId);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [
                // Test01 - Passing valid organization id as 1 will return organization lang details for example index 0th array
                [
                    1,
                    0,
                    $this->getOrganizationLangResponse($this->organizationLangArray, 0)
                ],
                // Test02 - Passing valid organization id as 2 will return organization lang details for example index 1st array
                [
                    2,
                    1,
                    $this->getOrganizationLangResponse($this->organizationLangArray, 1)
                ],
                // Test03 - Passing Invalid organization id will return empty result array
                [
                    -1,
                    0,
                    []
                ],
                // Test04 - Passing organization id as null will return empty result array
                [
                    null,
                    0,
                    []
                ],
            ]
        ]);
    }

    private function getOrganizationLangResponse($organizationLangArray, $exampleIndex)
    {
        $organizationLang = new OrganizationLang();
        $organizationLang->setOrganizationName($organizationLangArray[$exampleIndex]['organization_name']);
        $organizationLang->setNickName($organizationLangArray[$exampleIndex]['nick_name']);
        $organizationLang->setDescription($organizationLangArray[$exampleIndex]['description']);
        return $organizationLang;
    }

    public function testValidateActivationLink()
    {
        $this->specify("Test validateActivationLink", function ($token, $dateTime, $dateTimeInterval, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            // Mock $mockPersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneByActivationToken']);
            $mockPerson = $this->getPersonDetails($dateTime, $dateTimeInterval);
            if ($token == 'abc') {
                $mockPersonRepository->method('findOneByActivationToken')->willReturn($mockPerson);
            } else {
                $mockPersonRepository->method('findOneByActivationToken')->willThrowException(new ValidationException([], $expectedResult));
            }

            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
            ]);
            try {
                $passwordService = new PasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $passwordService->validateActivationLink($token);
                $this->assertEquals($results, $expectedResult);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - Passing invalid token will throw an exception
                [
                    'xyz',
                    null,
                    30,
                    'Activation Token Expired.'
                ],
                // Test02 - Passing valid token and if expiry date time is less than current date time, validate activation link and returns person object
                [
                    'abc',
                    new \DateTime(),
                    30,
                    $this->getPersonDetails(new \DateTime(), 30)
                ],
                // Test03 - Passing valid token and if expiry date time is greater than current date time will throw an exception
                [
                    'abc',
                    new \DateTime(),
                    20,
                    'Activation Token Expired.'
                ],
                // Test04 - Passing token as null|empty will throw an exception
                [
                    null,
                    new \DateTime(),
                    20,
                    'Activation Token Expired.'
                ],
                // Test05 - Passing everything as null will throw an exception
                [
                    null,
                    null,
                    null,
                    'Activation Token Expired.'
                ],
            ]
        ]);
    }

    private function getPersonDetails($expiryDateTime, $dateTimeInterval, $isCreatePassword = false)
    {
        $person = new Person();
        if (isset($expiryDateTime) && $dateTimeInterval >= 30) {
            $expiryDateTime->add(new \DateInterval('P0DT0H30M0S'));
        }
        if (isset($expiryDateTime) && $dateTimeInterval <= 20) {
            $expiryDateTime->sub(new \DateInterval('P0DT0H20M0S'));
        }
        $expiryDateTime = $isCreatePassword ? null : $expiryDateTime;
        $person->setTokenExpiryDate($expiryDateTime);
        $person->setId(1);
        $token = $isCreatePassword ? null : 'abc';
        $person->setActivationToken($token);
        $person->setPassword('Qait@123');
        $person->setFirstname('MR');
        $person->setLastname('KR');
        $person->setOrganization(new Organization());
        $person->setConfidentialityStmtAcceptDate($expiryDateTime);
        return $person;
    }

    public function testCreatePassword()
    {
        $this->specify("Test createPassword", function ($passwordDtoArray, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            // Mock loggerHelperService
            $mockLoggerHelperService = $this->getMock('LoggerHelperService', ['getLog']);
            // Mock PersonRepository
            $mockPersonRepository = $this->getMock('PersonRepository', ['findOneByActivationToken', 'getPersonDetails', 'flush']);
            $personDetailsArray = [0 => ['contacts' => [0 => ['primaryEmail' => 'Test@mailinator.com']]]];
            $mockPersonRepository->method('getPersonDetails')->willReturn($personDetailsArray);
            if ($passwordDtoArray['token'] != 'invalid' && $passwordDtoArray['token'] != null) {
                $mockPersonRepository->method('findOneByActivationToken')->willReturn($this->getPersonDetails(new \DateTime(), 20, true));
            } else {
                $mockPersonRepository->method('findOneByActivationToken')->willThrowException(new ValidationException([], $expectedResult));
            }

            // Mock EncoderFactory
            $mockEncoderFactory = $this->getMock('EncoderFactory', ['getEncoder']);
            $mockPasswordEncoderInterface = $this->getMock('PasswordEncoderInterface', ['encodePassword']);
            $mockPasswordEncoderInterface->method('encodePassword')->willReturn('Qait@123456');
            $mockEncoderFactory->method('getEncoder')->willReturn($mockPasswordEncoderInterface);

            // Mock OrganizationService
            $mockOrganizationService = $this->getMock('OrganizationService', ['getOrganizationDetailsLang']);
            $mockOrganizationLang = $this->getMock('OrganizationLang', ['getId', 'getLang']);
            $mockLanguageMaster = $this->getMock('LanguageMaster', ['getId']);
            $mockLanguageMaster->method('getId')->willReturn(1);
            $mockOrganizationLang->method('getLang')->willReturn($mockLanguageMaster);
            $mockOrganizationService->method('getOrganizationDetailsLang')->willReturn($mockOrganizationLang);

            // Mock OrganizationRoleRepository
            $mockOrganizationRoleRepository = $this->getMock('OrganizationRoleRepository', ['getUserCoordinatorRole']);
            $mockOrganizationRole = $this->getMock('OrganizationRole', ['getId']);
            $mockOrganizationRoleRepository->method('getUserCoordinatorRole')->willReturn($mockOrganizationRole);

            // Mock EbiConfigService
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);
            $mockEbiConfigService->method('getSystemUrl')->willReturn('https://mapworks-qa.skyfactor.com');

            // Mock EmailTemplateLangRepository
            $mockEmailTemplateLangRepository = $this->getMock('EmailTemplateLangRepository', ['getEmailTemplateByKey']);
            $mockEmailTemplateLang = $this->getMock('EmailTemplateLang', ['getId', 'getBody', 'getEmailTemplate', 'getSubject']);
            $mockEmailTemplate = $this->getMock('EmailTemplate', ['getId', 'getFromEmailAddress', 'getBccRecipientList']);
            $mockEmailTemplate->method('getFromEmailAddress')->willReturn('test@mailinator.com');
            $mockEmailTemplate->method('getBccRecipientList')->willReturn('test1@mailinator.com');
            $mockEmailTemplateLang->method('getEmailTemplate')->willReturn($mockEmailTemplate);
            $mockEmailTemplateLang->method('getBody')->willReturn('Test Email');
            $mockEmailTemplateLang->method('getSubject')->willReturn('Reset Password!');
            $mockEmailTemplateLangRepository->method('getEmailTemplateByKey')->willReturn($mockEmailTemplateLang);

            // Mock EbiConfigRepository
            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneByKey']);
            $mockEbiConfig = $this->getMock('EbiConfig', ['getValue']);
            $mockEbiConfig->method('getValue')->willReturn('Test');
            $mockEbiConfigRepository->method('findOneByKey')->willReturn($mockEbiConfig);

            // Mock EmailService
            $mockEmailService = $this->getMock('EmailService', ['generateEmailMessage', 'sendEmailNotification', 'sendEmail']);
            $emailNotificationDto = new EmailNotificationDto();
            $emailNotificationDto->setSubject('Testing');
            $emailNotificationDto->setFromAddress('test@mailinator.com');
            $mockEmailService->method('generateEmailMessage')->willReturn('Welcome to Skyfactor');
            $mockEmailService->method('sendEmailNotification')->willReturn($emailNotificationDto);


            $mockRepositoryResolver->method('getRepository')->willReturnMap([
                [PersonRepository::REPOSITORY_KEY,$mockPersonRepository],
                [OrganizationRoleRepository::REPOSITORY_KEY,$mockOrganizationRoleRepository],
                [EmailTemplateLangRepository::REPOSITORY_KEY,$mockEmailTemplateLangRepository],
                [EbiConfigRepository::REPOSITORY_KEY,$mockEbiConfigRepository],
            ]);

            $mockContainer->method('get')->willReturnMap([
                [LoggerHelperService::SERVICE_KEY,$mockLoggerHelperService],
                ['security.encoder_factory',$mockEncoderFactory],
                [OrganizationService::SERVICE_KEY,$mockOrganizationService],
                [EbiConfigService::SERVICE_KEY,$mockEbiConfigService],
                [EmailService::SERVICE_KEY,$mockEmailService],
            ]);

            try {
                $passwordService = new PasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);
                $results = $passwordService->createPassword($this->getCreatePasswordDto($passwordDtoArray));
                $this->assertEquals($results, $expectedResult);
            } catch (ValidationException $e) {
                $this->assertEquals($e->getMessage(), $expectedResult);
            }

        }, [
            'examples' => [
                // Test01 - if is_confidentiality_accepted as false will throw an exception
                [
                    [
                     'is_confidentiality_accepted' => false,
                     'token' => 'abc',
                     'password' => 'Qait@123'
                    ],
                    'Confidentiality Statement not accepted'
                ],
                // Test02 - if is_confidentiality_accepted as true and passed invalid token will throw an exception
                [
                    [
                        'is_confidentiality_accepted' => true,
                        'token' => 'invalid',
                        'password' => 'Qait@123'
                    ],
                    'Activation Token Expired.'
                ],
                // Test03 - if is_confidentiality_accepted as true and passed valid token will create password
                [
                    [
                        'is_confidentiality_accepted' => true,
                        'token' => 'abc',
                        'password' => 'Qait@123'
                    ],
                    $this->getCreatePasswordDtoResponseArray()
                ],
                // Test04 - if is_confidentiality_accepted as null will throw an exception
                [
                    [
                        'is_confidentiality_accepted' => null,
                        'token' => 'abc',
                        'password' => 'Qait@123'
                    ],
                    'Confidentiality Statement not accepted'
                ],
                // Test05 - if token as null will throw an exception
                [
                    [
                        'is_confidentiality_accepted' => true,
                        'token' => null,
                        'password' => 'Qait@123'
                    ],
                    'Activation Token Expired.'
                ],
                // Test06 - Everything passed as null will throw an exception
                [
                    [
                        'is_confidentiality_accepted' => null,
                        'token' => null,
                        'password' => null
                    ],
                    'Confidentiality Statement not accepted'
                ],
            ]
        ]);
    }

    private function getCreatePasswordDto($passwordDtoArray)
    {
        $createPasswordDto = new CreatePasswordDto();
        $createPasswordDto->setIsConfidentialityAccepted($passwordDtoArray['is_confidentiality_accepted']);
        $createPasswordDto->setToken($passwordDtoArray['token']);
        $createPasswordDto->setPassword($passwordDtoArray['password']);
        return $createPasswordDto;
    }

    private function getCreatePasswordDtoResponseArray(){

        $createPasswordDtoResponseArray['email_detail'] = [
            'from' => 'test@mailinator.com',
            'subject' => 'Reset Password!',
            'bcc' => 'test1@mailinator.com',
            'body' => 'Welcome to Skyfactor',
            'to' => 'Test@mailinator.com',
            'emailKey' => 'Sucessful_Password_Reset_Coordinator',
            'organizationId' => '',
        ];

        $createPasswordDtoResponseArray['signin_status'] = true;
        $createPasswordDtoResponseArray['person_id'] = 1;
        $createPasswordDtoResponseArray['person_first_name'] = 'MR';
        $createPasswordDtoResponseArray['person_last_name'] = 'KR';
        $createPasswordDtoResponseArray['person_type'] = 'Coordinator';
        return $createPasswordDtoResponseArray;
    }

    public function testGenerateTemporaryActivationToken()
    {
        $this->specify("Test generateTemporaryActivationToken", function ($salt, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            $passwordService = new PasswordService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $results = $passwordService->generateTemporaryActivationToken($salt);
            $this->assertEquals($results, $expectedResult);
        }, [
            'examples' => [
                // Test01 - Passing salt value will return temporary activation token
                [
                    "Test",
                    $this->getActivationTokenResponse('Test')
                ],
                // Test02 - Passing salt value as null will return temporary activation token
                [
                    null,
                    $this->getActivationTokenResponse(null)
                ],
                // Test03 - Passing empty salt value will return temporary activation token
                [
                    '',
                    $this->getActivationTokenResponse('')
                ]
            ]
        ]);
    }

    private function getActivationTokenResponse($salt)
    {
        if (is_null($salt)) {
            $token = md5(time());
        } else {
            $token = md5($salt . time() . $salt);
        }
        return $token;
    }
}