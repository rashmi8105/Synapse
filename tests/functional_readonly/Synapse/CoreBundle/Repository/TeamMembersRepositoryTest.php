<?php

class TeamMembersRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var \Synapse\CoreBundle\Repository\TeamMembersRepository
     */
    private $teamMembersRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->teamMembersRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:TeamMembers');
    }


    public function testGetActivityCountsOfMyTeamByActivityType()
    {

        $this->specify("Verify the functionality of the method getMyTeamActivityCountsByType, it should get a count of the specified type",
            function ($activityType, $activityCodes, $fromDate, $toDate, $personId, $organizationId,
                      $expectedTeamsInteractionsCountTeam1, $expectedTeamsInteractionsCountTeam2, $expectedTeamIds) {

                $academicStartDate = '2015-08-13';
                $academicEndDate = '2016-07-31';
                $currentAcademicYearId = 166;
                $results = $this->teamMembersRepository->getActivityCountsOfMyTeamByActivityType($activityType, $activityCodes, $fromDate, $toDate, $personId, $organizationId, $academicStartDate, $academicEndDate, '', $currentAcademicYearId);
                $resultCount = count($results);
                if ($resultCount > 1) {
                    verify($results[0]['team_activities_count'])->equals($expectedTeamsInteractionsCountTeam1);
                    verify($results[1]['team_activities_count'])->equals($expectedTeamsInteractionsCountTeam2);
                } else if ($resultCount == 1) {
                    verify($results[0]['team_activities_count'])->equals($expectedTeamsInteractionsCountTeam1);
                } else {
                    verify($results)->equals($expectedTeamsInteractionsCountTeam1);
                }

                foreach ($results as $result) {
                    $check = in_array($result['team_id'], $expectedTeamIds);
                    verify($check)->equals(true);
                }
            }, [
                "examples" => [
                    //Logins
                    [
                        'login',
                        ['L'],
                        '2015-08-15 00:00:00',
                        '2016-05-06 23:59:59',
                        4614025,
                        196,
                        [],
                        558,
                        [4226, 4238]
                    ],
                    //Open Referrals
                    [
                        'open-referral',
                        ['R'],
                        '2015-08-15 00:00:00',
                        '2016-05-06 23:59:59',
                        4614025,
                        196,
                        [],
                        null,
                        [4226]
                    ],
                    //Interactions
                    [
                        'interaction',
                        ["R", "N", "C", "A"],
                        '2015-08-15 00:00:00',
                        '2016-05-06 23:59:59',
                        4614025,
                        196,
                        [],
                        24,
                        [4226, 4238]
                    ],
                    //Too Restrictive a Date
                    [

                        'interaction',
                        ["R", "N", "C", "A",],
                        '2015-08-10 00:00:00',
                        '2015-08-10 00:00:59',
                        4614025,
                        196,
                        [],
                        null,
                        []
                    ],
                    //Nonexistent
                    [
                        'interaction',
                        ["R", "N", "C", "A"],
                        '2015-08-15 00:00:00',
                        '2016-05-06 23:59:59',
                        4891241,
                        196,
                        [],
                        null,
                        []
                    ],
                    //No Individual Permission (Has Aggregate Only)
                    [
                        'interaction',
                        ["R", "N", "C", "A"],
                        '2015-07-01 00:00:00',
                        '2016-05-06 23:59:59',
                        94432,
                        180,
                        [],
                        null,
                        []
                    ]
                ]
            ]);
    }


    public function testGetActivityDetailsOfMyTeam()
    {


        $this->specify("Verify the functionality of the method getActivityDetailsOfMyTeam", function ($activityType, $activityCodes, $fromDate, $toDate,
                                                                                                      $pageNumber, $recordsPerPage, $teamMemberIds, $sortBy, $expectedResults) {

            $academicStartDate = '2015-08-13';
            $academicEndDate = '2016-07-31';
            $personId = 4614025;
            $organizationId = 196;
            $currentAcademicYearId = 166;

            $results = $this->teamMembersRepository->getActivityDetailsOfMyTeam($activityType, $activityCodes, $personId, $organizationId, $fromDate, $toDate,
                $academicStartDate, $academicEndDate, $teamMemberIds, $pageNumber,
                $recordsPerPage, $sortBy, '', $currentAcademicYearId);
            verify($results)->equals($expectedResults);

        }, [
            "examples" => [
                //Interaction - Too Restrictive Debate
                ['interaction', ['R', 'N', 'C', 'A'],
                    '2015-08-15 00:00:00',
                    '2015-08-16 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '',
                    []],
                //Open Referrals
                ['open-referral', ['R'],
                    '2015-08-15 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '',
                    [],
                ],
                //Interaction
                ['interaction', ['R', 'N', 'C', 'A'],
                    '2015-08-15 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '',
                    []],
                // Login
                ['login', ['L'],
                    '2015-08-15 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '',
                    [
                        [
                            'activity_date' => '2016-05-06 17:28:11',
                            'team_member_external_id' => 4614025,
                            'team_member_id' => 4614025,
                            'team_member_firstname' => 'Ainsley',
                            'team_member_lastname' => 'Lewis',
                            'primary_email' => 'MapworksBetaUser04614025@mailinator.com',
                            'student_id' => null,
                            'student_firstname' => null,
                            'student_lastname' => null,
                            'student_external_id' => null,
                            'student_email' => null,
                            'activity_code' => 'L',
                            'referrals_id' => null,
                            'appointments_id' => null,
                            'note_id' => null,
                            'contacts_id' => null,
                            'reason_text' => 'Login'
                        ],

                        [
                            'activity_date' => '2016-05-05 15:00:50',
                            'team_member_external_id' => 4614025,
                            'team_member_id' => 4614025,
                            'team_member_firstname' => 'Ainsley',
                            'team_member_lastname' => 'Lewis',
                            'primary_email' => 'MapworksBetaUser04614025@mailinator.com',
                            'student_id' => null,
                            'student_firstname' => null,
                            'student_lastname' => null,
                            'student_external_id' => null,
                            'student_email' => null,
                            'activity_code' => 'L',
                            'referrals_id' => null,
                            'appointments_id' => null,
                            'note_id' => null,
                            'contacts_id' => null,
                            'reason_text' => 'Login'
                        ],

                        [
                            'activity_date' => '2016-05-05 14:59:18',
                            'team_member_external_id' => 4614715,
                            'team_member_id' => 4614715,
                            'team_member_firstname' => 'Gabriel',
                            'team_member_lastname' => 'Berger',
                            'primary_email' => 'MapworksBetaUser04614715@mailinator.com',
                            'student_id' => null,
                            'student_firstname' => null,
                            'student_lastname' => null,
                            'student_external_id' => null,
                            'student_email' => null,
                            'activity_code' => 'L',
                            'referrals_id' => null,
                            'appointments_id' => null,
                            'note_id' => null,
                            'contacts_id' => null,
                            'reason_text' => 'Login'
                        ],

                        [
                            'activity_date' => '2016-04-27 15:11:00',
                            'team_member_external_id' => 4627090,
                            'team_member_id' => 4627090,
                            'team_member_firstname' => 'Kade',
                            'team_member_lastname' => 'Powell',
                            'primary_email' => 'MapworksBetaUser04627090@mailinator.com',
                            'student_id' => null,
                            'student_firstname' => null,
                            'student_lastname' => null,
                            'student_external_id' => null,
                            'student_email' => null,
                            'activity_code' => 'L',
                            'referrals_id' => null,
                            'appointments_id' => null,
                            'note_id' => null,
                            'contacts_id' => null,
                            'reason_text' => 'Login'
                        ],

                        [
                            'activity_date' => '2016-04-26 19:34:35',
                            'team_member_external_id' => 4614025,
                            'team_member_id' => 4614025,
                            'team_member_firstname' => 'Ainsley',
                            'team_member_lastname' => 'Lewis',
                            'primary_email' => 'MapworksBetaUser04614025@mailinator.com',
                            'student_id' => null,
                            'student_firstname' => null,
                            'student_lastname' => null,
                            'student_external_id' => null,
                            'student_email' => null,
                            'activity_code' => 'L',
                            'referrals_id' => null,
                            'appointments_id' => null,
                            'note_id' => null,
                            'contacts_id' => null,
                            'reason_text' => 'Login'
                        ]
                    ]
                ],
                //interaction NO individual and Aggregate Access
                ['interaction', ['R', 'N', 'C', 'A'],
                    '2015-07-01 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [94432, 138236, 94468, 94434, 4622308],
                    '',
                    []],
                //Sorting by date DESC
                ['open-referral', ['R'],
                    '2015-08-15 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '-date',
                    []],
                //Sorting by date asc
                ['open-referral', ['R'],
                    '2015-08-15 00:00:00',
                    '2016-05-06 23:59:59',
                    1,
                    5,
                    [4627090, 4614025, 4627087, 4614715, 4627096, 4627057],
                    '+date',
                    []
                ],
            ]
        ]);
    }


}