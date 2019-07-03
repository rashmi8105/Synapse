<?php

use Synapse\CoreBundle\Repository\TeamsRepository;

class TeamsRepositoryTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Repository\TeamsRepository
     */
    private $teamsRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->teamsRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);
    }

    public function testGetTeams()
    {
        $this->specify("Verify the functionality of the method getTeams", function ($organizationId, $expectedResults) {
            $results = $this->teamsRepository->getTeams($organizationId);
            verify($results)->equals($expectedResults);
        },
            [
                "examples" =>
                [
                    //team details for organization 62 - 3 team leaders
                    [
                        62,
                        [
                            [
                                'team_id' => 4128,
                                'team_name' => 'Test Team 00004128',
                                'modified_at' => '2015-08-10 18:35:23',
                                'team_no_leaders' => 3,
                                'team_no_members' => 0,
                            ]
                        ]
                    ],
                    //team details for organization 42, team 1085, 1087, 1794, 1796 has no team members
                    [
                        42,
                        [
                            [
                                'team_id' => 1085,
                                'team_name' => 'Test Team 00001085',
                                'modified_at' => null,
                                'team_no_leaders' => 0,
                                'team_no_members' => 0,
                            ],
                            [
                                'team_id' => 1087,
                                'team_name' => 'Test Team 00001087',
                                'modified_at' => null,
                                'team_no_leaders' => 0,
                                'team_no_members' => 0,
                            ],
                            [
                                'team_id' => 1794,
                                'team_name' => 'Test Team 00001794',
                                'modified_at' => null,
                                'team_no_leaders' => 0,
                                'team_no_members' => 0,
                            ],
                            [
                                'team_id' => 1796,
                                'team_name' => 'Test Team 00001796',
                                'modified_at' => null,
                                'team_no_leaders' => 0,
                                'team_no_members' => 0,
                            ]
                        ]
                    ],
                    //invalid organization , returns empty array
                    [
                        622,
                        []
                    ],
                    //team details for organization 99 - 6 team members, no leader
                    [
                        99,
                        [
                            [
                                'team_id' => 139,
                                'team_name' => 'Test Team 00000139',
                                'modified_at' => '2016-01-08 03:12:40',
                                'team_no_leaders' => 0,
                                'team_no_members' => 6,
                            ]
                        ]
                    ],
                ]
            ]
        );
    }

    public function testGetTeamMembers()
    {
        $this->specify("Verify the functionality of the method getTeamMembers", function ($teamId, $expectedResults) {
            $results = $this->teamsRepository->getTeamMembers($teamId);
            verify($results)->equals($expectedResults);

        },
            [
                "examples" =>
                    [
                        //team members for team id 161
                        [
                            161,
                            [
                                [
                                    'person_id' => 222873,
                                    'first_name' => 'Rosalyn',
                                    'last_name' => 'Yu',
                                    'is_leader' => 0,
                                    'action' => 0,
                                ],
                                [
                                    'person_id' => 4628406,
                                    'first_name' => 'Myah',
                                    'last_name' => 'Burgess',
                                    'is_leader' => 1,
                                    'action' => 0,
                                ],
                                [
                                    'person_id' => 976490,
                                    'first_name' => 'Tamia',
                                    'last_name' => 'Yates',
                                    'is_leader' => 1,
                                    'action' => 0,
                                ],
                                [
                                    'person_id' => 183664,
                                    'first_name' => 'Jadiel',
                                    'last_name' => 'Khan',
                                    'is_leader' => 0,
                                    'action' => 0,
                                ]
                            ]
                        ],
                        //team members for team id 1294
                        [
                            1294,
                            [
                                [
                                    'person_id' => 161621,
                                    'first_name' => 'Dayton',
                                    'last_name' => 'Boyer',
                                    'is_leader' => 1,
                                    'action' => 0,
                                ],
                                [
                                    'person_id' => 4687142,
                                    'first_name' => 'Moses',
                                    'last_name' => 'Vasquez',
                                    'is_leader' => 0,
                                    'action' => 0,
                                ]
                            ]
                        ],
                        //invalid team id , returns no member
                        [
                            -1,
                            []
                        ]
                    ]
            ]
        );
    }

    public function testGetTeamDetails()
    {
        $this->specify("Verify the functionality of the method getTeamDetails", function ($teamId, $expectedResults) {
            $results = $this->teamsRepository->getTeamDetails($teamId);
            verify($results)->equals($expectedResults);

        },
            [
                "examples" =>
                    [
                        //team details for  team id 1293
                        [
                            1293,
                            [
                                'team_id' => 1293,
                                'team_name' => 'Test Team 00001293',
                                'modified_at' => '2015-08-27 21:35:17',

                            ]
                        ],
                        //team members for invalid team id 2222 returns null
                        [
                            2222,
                            null
                        ]
                    ]
            ]
        );
    }
}