<?php

use Synapse\CoreBundle\Entity\MapworksActionVariable;
use Synapse\CoreBundle\Entity\MapworksActionVariableDescription;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\MapworksActionRepository;
use Synapse\CoreBundle\Repository\MapworksActionVariableRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Repository\OrganizationRepository;


class MapworksActionServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseValidationException
     */
    public function testSendCommunicationBasedOnMapworksAction()
    {

        $this->specify("update Notification View Status", function ($action, $recipientType, $eventType, $recipientId, $activityDescription = null, $activityObject = null, $isReceiveEmails, $isReceiveNotification, $expectedResults) {


            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            $organizationId = 213;
            $mockMapworksActionRepository = $this->getMock('MapworksActionRepository', ['findOneBy']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository',['find']);
            $mockOrganizationRepository->method('find')->willReturn(true);
            $mockPersonRepository = $this->getMock('PersonRepository', ['find']);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [MapworksActionRepository::REPOSITORY_KEY, $mockMapworksActionRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository]
                ]);
            $mapworksActionRepository = $this->getMock('MapworksAction', ['getReceivesNotification', 'getReceivesEmail', 'getEventKey']);
            $personRepository = $this->getMock('Person', ['getUsername']);
            if ($action) {
                $mockMapworksActionRepository->method('findOneBy')->willReturn($mapworksActionRepository);
            }

            $mapworksActionRepository->method('getReceivesEmail')->willReturn($isReceiveEmails);
            $mockPersonRepository->method('find')->willReturn($personRepository);

            $mockEmailService = $this->getMock('EmailService', ['buildEmailResponse', 'sendEmailNotification', 'sendEmail']);
            $mockAlertNotificationService = $this->getMock('AlertNotificationsService', ['createNotification']);

            $mockContainer->method('get')
                ->willReturnMap([
                    [EmailService::SERVICE_KEY, $mockEmailService],
                    [AlertNotificationsService::SERVICE_KEY, $mockAlertNotificationService]
                ]);
            $mockEmailService->method('sendEmail')->willReturn($isReceiveEmails);
            if ($isReceiveNotification) {
                $mapworksActionRepository->method('getReceivesNotification')->willReturn($isReceiveNotification);
                $mockAlertNotificationService->method('createNotification')->willReturn(true);
            }
            $notificationService = new MapworksActionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $notificationService->sendCommunicationBasedOnMapworksAction($organizationId, $action, $recipientType, $eventType, $recipientId, $activityDescription, $activityObject);
            $this->assertEquals($result, $expectedResults);

        }, ['examples' =>
            [
                // Send Email Notification
                ['create', 'current_assignee', 'referral', 98987898, 'Referral created', NULL, true, false, true],

                // Send Notification
                ['create', 'current_assignee', 'referral', 98987898, 'Referral created', NULL, false, true, true],

                // Send both email and alert notification
                ['create', 'current_assignee', 'referral', 98987898, 'Referral created', NULL, true, true, true],

                // Passing empty action will throw exception
                ['', 'current_assignee', 'referral', 22398987, 'Invalid action', NULL, true, false, false],

                // Passing empty recipient type, will throw exception
                ['create', '', 'referral', 22398987, 'Empty recipient type', NULL, true, false, false],

                // Passing empty event type, will throw exception
                ['create', 'current_assignee', '', 22398987, 'Empty event type', NULL, true, false, false],

                // Invalid event type event type, will throw exception
                ['create', 'current_assignee', '6767676', 22398987, 'Invalid event type', NULL, true, false, false],

                // Invalid recipient type, will throw exception
                ['create', '451258p', 'referral', 22398987, 'Invalid recipient type', NULL, true, false, false],

            ]
        ]);
    }

    public function testGetTokenVariablesFromPerson()
    {
        $this->specify("Test get token variables from person", function ($recipientType, $expectedResults) {

            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);
            if($recipientType) {
                $personObject = $this->getPersonObject($recipientType);
            }
            else{
                $personObject = null;
            }

            $mapworksActionService = new MapworksActionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $mapworksActionService->getTokenVariablesFromPerson($recipientType, $personObject);

            $this->assertEquals($result, $expectedResults);

        }, ['examples' =>
            [
                //prepare token variable for student
                ['student', ['$$student_first_name$$' => 'student_first_name',
                    '$$student_last_name$$' => 'student_last_name',
                    '$$student_email_address$$' => 'student_email_address',
                    '$$student_title$$' => 'student_title'
                ]],
                //prepare token variable for coordinator
                ['coordinator', ['$$coordinator_first_name$$' => 'coordinator_first_name',
                    '$$coordinator_last_name$$' => 'coordinator_last_name',
                    '$$coordinator_email_address$$' => 'coordinator_email_address',
                    '$$coordinator_title$$' => 'coordinator_title'
                ]],
                //prepare token variable for faculty
                ['faculty', ['$$faculty_first_name$$' => 'faculty_first_name',
                    '$$faculty_last_name$$' => 'faculty_last_name',
                    '$$faculty_email_address$$' => 'faculty_email_address',
                    '$$faculty_title$$' => 'faculty_title'
                ]],
                //prepare token variable for faculty if recipient type is null
                [null, []]

            ]
        ]);
    }

    public function testBuildCommunicationBodyFromVariables()
    {
        $this->specify("Test build Communication Body From Variables", function ($recipientType, $textBody, $expectedResults) {

            //Core Mocks
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error', 'info']);
            $mockContainer = $this->getMock('Container', ['get']);

            $mockMapworksActionVariableRepository = $this->getMock('mapworksActionVariableRepository', ['findBy']);
            $mockOrganizationRepository = $this->getMock('OrganizationRepository',['find']);
            $mockOrganizationRepository->method('find')->willReturn(true);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [MapworksActionVariableRepository::REPOSITORY_KEY, $mockMapworksActionVariableRepository],
                    [OrganizationRepository::REPOSITORY_KEY, $mockOrganizationRepository]
                ]);

            if ($recipientType) {
                $mapworksAction = $mockMapworksActionVariableRepository->method('findBy')->willReturn($this->getActionVariable($recipientType));

                $tokenValues = ['$$' . $recipientType . '_first_name$$' => $recipientType . '_first_name',
                    '$$' . $recipientType . '_last_name$$' => $recipientType . '_last_name',
                    '$$' . $recipientType . '_email_address$$' => $recipientType . '_email_address',
                    '$$' . $recipientType . '_title$$' => $recipientType . '_title'
                ];
            } else {
                $mapworksAction = $mockMapworksActionVariableRepository->method('findBy')->willReturn([]);
                $tokenValues = [];
            }

            $mapworksActionService = new MapworksActionService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $result = $mapworksActionService->buildCommunicationBodyFromVariables($textBody, $tokenValues, $mapworksAction);

            $this->assertEquals(trim($result), trim($expectedResults));

        }, ['examples' =>
            [
                //build communication body for student template
                ['student', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi $$student_first_name$$,</td></tr>
                                <tr><td>$$student_last_name$$ has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi student_first_name,</td></tr>
                                <tr><td>student_last_name has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>'
                ],
                //build communication body for faculty template
                ['faculty', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi $$faculty_first_name$$,</td></tr>
                                <tr><td>$$faculty_last_name$$ has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi faculty_first_name,</td></tr>
                                <tr><td>faculty_last_name has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>'
                ],
                //build communication body for coordinator template
                ['coordinator', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi $$coordinator_first_name$$,</td></tr>
                                <tr><td>$$coordinator_last_name$$ has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>', '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi coordinator_first_name,</td></tr>
                                <tr><td>coordinator_last_name has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                </body>'
                ],
                //build communication body when recipient type amd mapworks action is null
                [null, '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi $$coordinator_first_name$$,</td></tr>
                                <tr><td>$$coordinator_last_name$$ has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                    </body>',
                    '<body>
                        <table>
                            <tbody>
                                <tr><td>Hi $$coordinator_first_name$$,</td></tr>
                                <tr><td>$$coordinator_last_name$$ has assigned you a new referral in Mapworks. Please sign in to your account to view and take action on this referral.</td></tr>
                                <tr><td>This email confirmation is an auto-generated message. Replies to automated messages are not monitored.</td></tr>
                            </tbody>
                        </table>
                    </body>'
                ]
            ]
        ]);
    }

    private function getActionVariable($type = null)
    {
        $variable = [];
        $variableType = ['$$' . $type . '_first_name$$', '$$' . $type . '_last_name$$', '$$' . $type . '_email_address$$'];
        foreach ($variableType as $type) {
            $mapworksActionVariableDescription = new MapworksActionVariableDescription();
            $mapworksActionVariableDescription->setVariable($type);
            $mapworksVariable =  new MapworksActionVariable();
            $mapworksVariable->setMapworksActionVariableDescription($mapworksActionVariableDescription);
            $variable[] = $mapworksVariable;
        }
        return $variable;
    }

    private function getPersonObject($recipientType)
    {
        $person = new Person();
        $person->setFirstname($recipientType . '_first_name');
        $person->setLastname($recipientType . '_last_name');
        $person->setUsername($recipientType . '_email_address');
        $person->setTitle($recipientType . '_title');

        return $person;
    }
}
