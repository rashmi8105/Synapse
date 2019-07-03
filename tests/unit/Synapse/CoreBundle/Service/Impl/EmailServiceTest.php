<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\EmailTemplate;
use Synapse\CoreBundle\Entity\MapworksAction;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;


class EmailServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testVerifyThatThePersonLoggedInIsThePersonSendingTheEmail()
    {
        $this->specify("Test to make sure that the person logged in is the person sending the email", function ($expectedResult, $loggedInPerson, $sentEmailDTO) {

            // Inititializing repository to be mocked
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));

            $mockOrgPermissionsetFeaturesRepository = $this->getMock('OrgPermissionsetFeatures', array(
                'getFeaturePermissions'
            ));
            $mockOrgGroupFacultyRepository = $this->getMock('OrgGroupFaculty', array(
                'getPermissionsByFacultyStudent'
            ));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        'SynapseCoreBundle:OrgGroupFaculty',
                        $mockOrgGroupFacultyRepository
                    ],
                    [
                        'SynapseCoreBundle:OrgPermissionsetFeatures',
                        $mockOrgPermissionsetFeaturesRepository
                    ]]);
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));


            $mockContainer = $this->getMock('Container', array('get'));


            $mockOrganizationObject = $this->getMock('Organization', array('getId'));
            $mockOrganizationObject->expects($this->any())->method('getId')->willReturn($loggedInPerson['organization']['id']);

            $mockPersonObject = $this->getMock('Person', array('getOrganization', 'getId', 'getUsername'));
            $mockPersonObject->expects($this->any())->method('getId')->willReturn($loggedInPerson['id']);
            $mockPersonObject->expects($this->any())->method('getUsername')->willReturn($loggedInPerson['username']);
            $mockPersonObject->expects($this->any())->method('getOrganization')->willReturn($mockOrganizationObject);

            $mockEmailDtoObject = $this->getMock('EmailDto', array('getPersonStaffId', 'getPersonStaffName', 'getOrganizationId'));
            $mockEmailDtoObject->expects($this->any())->method('getPersonStaffId')->willReturn($sentEmailDTO['personStaffId']);
            $mockEmailDtoObject->expects($this->any())->method('getPersonStaffName')->willReturn($sentEmailDTO['personStaffName']);
            $mockEmailDtoObject->expects($this->any())->method('getOrganizationId')->willReturn($sentEmailDTO['organizationId']);


            $emailService = new EmailService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $response = 'Allowed';
            try {
                $emailService->verifyThatThePersonLoggedInIsThePersonSendingTheEmail($mockPersonObject, $mockEmailDtoObject);
            } catch (\exception $e) {
                $response = $e->getMessage();
            }

            $this->assertEquals($expectedResult, $response);

        }, [
            'examples' => [

                // A cat trying to send an email
                [
                    // default string
                    'Allowed',
                    // fake person object
                    [
                        'id' => 1,
                        'username' => 'meow@ImaCat.org',
                        'organization' => [
                            'id' => 2
                        ]
                    ],
                    // fake email dto
                    [
                        'personStaffId' => 1,
                        'personStaffName' => 'meow@ImaCat.org',
                        'organizationId' => 2
                    ]
                ],
                // the cat trying to send an email as a dog to the dog university
                [
                    // expected message response
                    'You do not have permission to create email as someone else',
                    // fake person
                    [
                        'id' => 1,
                        'username' => 'meow@ImaCat.org',
                        'organization' => [
                            'id' => 2
                        ]
                    ],
                    // fake email dto
                    [
                        'personStaffId' => 2,
                        'personStaffName' => 'woof@ImaDog.org',
                        'organizationId' => 3
                    ]
                ],

                // The cat is trying to send the email; with the reply_to section containing an dog's email
                // the personStaffName is allowed to not be the username of the person sending the email
                [
                    'Allowed',
                    [
                        'id' => 1,
                        'username' => 'meow@ImaCat.org',
                        'organization' => [
                            'id' => 2
                        ]
                    ],
                    [
                        'personStaffId' => 1,
                        'personStaffName' => 'woof@ImaDog.org',
                        'organizationId' => 2
                    ]
                ],
                // A cat is trying to send the email to the dog university
                [
                    'You do not have permission to send an email to a different organization',
                    [
                        'id' => 1,
                        'username' => 'meow@ImaCat.org',
                        'organization' => [
                            'id' => 2
                        ]
                    ],
                    [
                        'personStaffId' => 1,
                        'personStaffName' => 'meow@ImaCat.org',
                        'organizationId' => 3
                    ]
                ]
            ]
        ]);
    }

    public function testGenerateEmailMessage()
    {
        $this->specify("Generate email message using email templates and values", function ($message, $tokenValues, $expectedResults) {


            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $emailObj = new EmailService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $emailObj->generateEmailMessage($message, $tokenValues);
            $this->assertEquals($result, $expectedResults);

        }, [
            'examples' => [
                    [
                        '<!DOCTYPE html>
                        <html>
                            <head>
                                <title></title>
                            </head>
                            <body>
                                <p>Please submit your academic updates for this request:</p>
                                <table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
                                    <tr>
                                        <td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse">
                                            <p style="font-weight: bold; font-size: 14px; color:#fff;">
                                                <a href="$$updateviewurl$$">View and complete this academic update request on Mapworks </a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
                                            <p style="font-size:14px; font-weight: bold;   margin: 0px !important;">$$requestname$$ (due <span>$$duedate$$</span>)</p>
                                            <p style="font-size:14px;   margin: 0px !important;">$$description$$</p>
                                            <p style="font-size:14px;   margin: 0px !important;">Requestor: <span>$$requestor$$</span>&nbsp;<span>$$requestor_email$$</span></p>
                                            <p>
                                                <span style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: </span>
                                                <span>$$studentupdate$$</span>
                                                <span  style="float:right;">
                                                    <a style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;" href="$$updateviewurl$$">Update</a>
                                                </span>
                                            </p>

                                        </td>
                                    </tr>
                                </table>
                                <p style="width:40%; margin-bottom:20px;">$$optional_message$$</p>
                            </body>
                        </html>',
                            [
                                'requestor' => "coordinator5 last",
                                'requestor_email' => 'coordinator5@mailinator.com',
                                'requestname' => 'test21',
                                'studentupdate' => 5,
                                'optional_message' => 'test<br />
                                test<br />
                                test<br />
                                ',
                                'description' => "",
                                'updateviewurl' => 'https://mapworks-uat.skyfactor.com/#/academic-updates/update/347',
                                'duedate' => '07/13/2016'

                            ]
                        ,
                        '<!DOCTYPE html>
                        <html>
                            <head>
                                <title></title>
                            </head>
                            <body>
                                <p>Please submit your academic updates for this request:</p>
                                <table class="table table-bordered" style="border: 1px solid #428BCA !important; border-collapse:collapse; width:40%; margin-top:20px; margin-bottom:20px;">
                                    <tr>
                                        <td style=" padding:5px; background-color: #4F9BD9; border: 1px solid #428BCA !important; border-collapse:collapse">
                                            <p style="font-weight: bold; font-size: 14px; color:#fff;">
                                                <a href="https://mapworks-uat.skyfactor.com/#/academic-updates/update/347">View and complete this academic update request on Mapworks </a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #D4EEFF; border: 1px solid #4F9BD9 !important; border-collapse:collapse; padding:15px 5px 15px 5px; vertical-align: middle;">
                                            <p style="font-size:14px; font-weight: bold;   margin: 0px !important;">test21 (due <span>07/13/2016</span>)</p>
                                            <p style="font-size:14px;   margin: 0px !important;"></p>
                                            <p style="font-size:14px;   margin: 0px !important;">Requestor: <span>coordinator5 last</span>&nbsp;<span>coordinator5@mailinator.com</span></p>
                                            <p>
                                                <span style="display:inline-block; float:left; font-size:14px;   margin: 0px !important;">Student Updates: </span>
                                                <span>5</span>
                                                <span  style="float:right;">
                                                    <a style="width: 65px; height:20px; background-color:#ccc; text-align: center; display:inline-block; float: right; border: 1px solid #ccc; margin: 2px; text-decoration: none; color:#000;" href="https://mapworks-uat.skyfactor.com/#/academic-updates/update/347">Update</a>
                                                </span>
                                            </p>

                                        </td>
                                    </tr>
                                </table>
                                <p style="width:40%; margin-bottom:20px;">test<br />
                                test<br />
                                test<br />
                                </p>
                            </body>
                        </html>'
                    ]
            ]
        ]);
    }

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testBuildEmailResponse()
    {
        $this->specify("Test to construct email response ", function ($recipientEmailAddress, $tokenValues, $mapworksAction, $fromAddress, $bccAddress, $subject, $expectedResults) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockEmailTemplateLangRepository = $this->getMock('emailTemplateLangRepository', ['findOneBy']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [EmailTemplateLangRepository::REPOSITORY_KEY, $mockEmailTemplateLangRepository]
                ]);
            $organizationId = 203;
            $emailTemplateLangRepository = $this->getMock('emailTemplateLang', ['getId', 'getBody', 'getEmailTemplate', 'getSubject']);
            $mockEmailTemplateLangRepository->method('findOneBy')->willReturn($emailTemplateLangRepository);

            $emailTemplate = $this->getMock('EmailTemplate', ['getBccRecipientList', 'getFromEmailAddress']);
            $emailTemplateLangRepository->method('getEmailTemplate')->willReturn($emailTemplate);

            $mapworksActionService = $this->getMock('MapworksActionService', ['buildCommunicationBodyFromVariables']);
            $mockContainer->method('get')
                ->willReturnMap([
                    [MapworksActionService::SERVICE_KEY, $mapworksActionService]
                ]);

            $emailTemplate->method('getFromEmailAddress')->willReturn($fromAddress);
            $emailTemplate->method('getBccRecipientList')->willReturn($bccAddress);
            $emailTemplateLangRepository->method('getSubject')->willReturn($subject);


            $emailService = new EmailService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $emailService->buildEmailResponse($organizationId, $recipientEmailAddress, $tokenValues, $mapworksAction);
            $this->assertEquals($result, $expectedResults);
        }, [
            'examples' => [

                // Valid Email template
                [
                    'recipient@mapworks.com',
                    [
                        '$$FACULTY_NAME$$' => 'name'
                    ],
                    $this->createMockMapworksAction(1),
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'admin@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'admin@mapworks.com',
                    ]
                ],
                // Passing token value for $$coordinator_email_address$$
                [
                    'recipient@mapworks.com',
                    [
                        '$$FACULTY_NAME$$' => 'name',
                        '$$coordinator_email_address$$' => 'coordinator@mapworks.com'
                    ],
                    $this->createMockMapworksAction(0),
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'coordinator@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'coordinator@mapworks.com',
                    ]
                ],

                // Invalid Email Template throws SynapseValidationException
                [
                    'recipient@mapworks.com',
                    [
                        '$$FACULTY_NAME$$' => 'name'
                    ],
                    $this->createMockMapworksAction(0),
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'admin@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'admin@mapworks.com',
                    ]
                ],

                // Passing in valid parameter
                [
                    'recipient@mapworks.com',
                    [
                        '$$FACULTY_NAME$$' => 'name',
                        '$$coordinator_email_address$$' => 'coordinator@mapworks.com'
                    ],
                    'Mapworks Action', // Invalid value is passed
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'coordinator@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'coordinator@mapworks.com',
                    ]
                ],

                // Invalid Token
                [
                    'recipient@mapworks.com',
                    "test",
                    $this->createMockMapworksAction(0),
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'coordinator@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'coordinator@mapworks.com',
                    ]
                ],
                // to make sure fromEmailAddress is not written over by coordinator email address
                [
                    'recipient@mapworks.com',
                    [
                        '$$FACULTY_NAME$$' => 'name',
                        '$$coordinator_email_address$$' => 'coordinator@mapworks.com'
                    ],
                    $this->createMockMapworksAction(1),
                    'admin@mapworks.com',
                    'bcc@mapworks.com',
                    'Email Template',
                    [
                        'from' => 'admin@mapworks.com',
                        'subject' => 'Email Template',
                        'bcc' => 'bcc@mapworks.com',
                        'body' => NULL,
                        'to' => 'recipient@mapworks.com',
                        'organizationId' => 203,
                        'emailKey' => NULL,
                        'replyTo' => 'coordinator@mapworks.com',
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $emailTemplateId
     * @return MapworksAction
     */
    private function createMockMapworksAction($emailTemplateId)
    {
        $mapworksAction = new MapworksAction();
        if ($emailTemplateId) {
            $emailTemplate = new EmailTemplate();
        } else {
            $emailTemplate = NULL;
        }
        $mapworksAction->setEmailTemplate($emailTemplate);
        return $mapworksAction;
    }

}

