<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;

class OrganizationRoleRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->organizationRoleRepository = $this->repositoryResolver->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
    }


    public function testFindFirstPrimaryCoordinatorAlphabetically()
    {
        $this->specify("Test the mechanism for getting the first primary coordinator at an organization alphabetically", function ($organizationId, $expectedResult) {
            $result = $this->organizationRoleRepository->findFirstPrimaryCoordinatorIdAlphabetically($organizationId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                // Organization 203 has only one primary coordinator
                [203, 4878750],

                // Organization 62 has more than one primary coordinator, but the method should return only the first one alphabetically. This is not the first coordinator ID for org 62
                [62, 220115],

                // Invalid Organization Returns NULL for primary coordinator
                ['invalid_org', null]
            ]
        ]);
    }


    public function testGetCoordinatorsArray()
    {

        $this->specify("Test for getting the coordinator list  for an organization", function ($organizationId, $expectedArray) {

            $resultSet = $this->organizationRoleRepository->getCoordinatorsArray($organizationId);
            verify($resultSet)->equals($expectedArray);

        }, ["examples" =>
            [
                // coordinators for organizationId 203
                [203,

                    [
                        [
                            'id' => "4878750",
                            'welcome_email_sent_date' => "2016-01-26",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-2250",
                            'firstname' => "Kenneth",
                            'lastname' => "Stark",
                            'email' => "MapworksBetaUser04878750@mailinator.com",
                            'externalid' => "4878750",
                            'title' => "test",
                            'roleid' => "1",
                            'role' => "Primary coordinator",
                            'modified_at' => "2016-01-26 15:09:09"
                        ],

                        [
                            'id' => "4878751",
                            'welcome_email_sent_date' => "2016-01-26",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-2251",
                            'firstname' => "Maximus",
                            'lastname' => "Lowery",
                            'email' => "MapworksBetaUser04878751@mailinator.com",
                            'externalid' => "4878751",
                            'title' => "Sr Intern",
                            'roleid' => "3",
                            'role' => "Non Technical coordinator",
                            'modified_at' => "2016-01-26 15:10:10"
                        ]

                    ]

                ],
                //coordinators for organizationId 62
                [62,
                    [
                        [
                            'id' => 184178,
                            'welcome_email_sent_date' => "2015-06-12",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-4178",
                            'firstname' => "Lyric",
                            'lastname' => "Weaver",
                            'email' => "MapworksBetaUser00184178@mailinator.com",
                            'externalid' => "184178",
                            'title' => "SENIOR RESEARCH ANALYST, DEPARTMENT OF RESIDE",
                            'roleid' => "1",
                            'role' => "Primary coordinator",
                            'modified_at' => "2016-02-08 21:51:25"
                        ],
                        [
                            'id' => "183608",
                            'welcome_email_sent_date' => "2015-06-12",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-3608",
                            'firstname' => "Bentlee",
                            'lastname' => "Mathews",
                            'email' => "MapworksBetaUser00183608@mailinator.com",
                            'externalid' => "183608",
                            'title' => "ASSOCIATE DIRECTOR FOR RESIDENCE LIFE",
                            'roleid' => "1",
                            'role' => "Primary coordinator",
                            'modified_at' => "2015-08-12 18:38:44"
                        ],

                        [
                            'id' => "220115",
                            'welcome_email_sent_date' => "2015-06-12",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-0115",
                            'firstname' => "Aniyah",
                            'lastname' => "Cole",
                            'email' => "MapworksBetaUser00220115@mailinator.com",
                            'externalid' => "220115",
                            'title' => "Dean of Students",
                            'roleid' => "1",
                            'role' => "Primary coordinator",
                            'modified_at' => "2015-05-25 17:28:48",
                        ],
                        [
                            'id' => 4691399,
                            'welcome_email_sent_date' => "",
                            'primary_mobile' => null,
                            'home_phone' => "555-555-4909",
                            'firstname' => "Keagan",
                            'lastname' => "Newton",
                            'email' => "MapworksBetaUser04691399@mailinator.com",
                            'externalid' => 4691399,
                            'title' => "GRADUATE ASSISTANT-DEPARTMENT OF RESIDENCE",
                            'roleid' => 2,
                            'role' => "Technical coordinator",
                            'modified_at' => "2015-09-28 19:41:41"
                        ]
                    ]]]]);
    }


    public function testGetServiceAccountsForOrganization()
    {

        $this->specify("Test the getting the service accounts", function ($organizationId, $expectedArray) {
            $serviceAccounts = $this->organizationRoleRepository->getServiceAccountsForOrganization($organizationId);
            verify($serviceAccounts)->equals($expectedArray);
        }, ["examples" => [
            // service accounts for organization id 70
            [70,

                [

                    [
                        'id' => 4897489,
                        'lastname' => "Banner Service Account",
                        'roleid' => 6,
                        'role' => "API Coordinator",
                        'client_id' => "7_562f8cb180d247943f224d446dbce5aa",
                        'client_secret' => "32631fc8344317fd3a6882113651e2cd",
                        'modified_at' => null
                    ],
                    [
                        'id' => 4897490,
                        'lastname' => "Moodle Service Account",
                        'roleid' => 6,
                        'role' => "API Coordinator",
                        'client_id' => "11_8910cbe43c51e29e6654bab448ab5884",
                        'client_secret' => "1f9fedc29764e08249bc001a781c4e7f",
                        'modified_at' => null
                    ],

                    [
                        'id' => 4897491,
                        'lastname' => "Blackboard Service Account",
                        'roleid' => 6,
                        'role' => "API Coordinator",
                        'client_id' => "15_f30d944bd42ee9dba75227593ecdc8b6",
                        'client_secret' => "8b67fa16536ca0745f684afac0bb0cec",
                        'modified_at' => null
                    ]
                ]],
            //no service accounts for organization id 203
            [203, []]
        ]
        ]);
    }

    public function testGetNonLoggedInCoordinator()
    {
        $this->specify("testGetNonLoggedInCoordinator", function ($expectedResult, $organizationId = null) {
            $functionResults = $this->organizationRoleRepository->getNonLoggedInCoordinator($organizationId);
            verify($functionResults)->equals($expectedResult);
        }, [
            'examples' => [
                //Organization with only active non-logged in coordinators
                [
                    [
                        [
                            "person_id" => '204651'
                        ]
                    ],
                    70
                ],
                //Organization with both active and inactive non-logged in coordinators. Inactives are not included in the list.
                [
                    [
                        [
                            "person_id" => "1216860"
                        ]
                    ],
                    2
                ],
                //No passed organization ID
                [
                    []
                ],
                //Invalid organization ID
                [
                    [],
                    "This is not an integer."
                ]
            ]
        ]);
    }

    public function testGetNonLoggedInCoordinatorCount()
    {
        $this->specify("testGetNonLoggedInCoordinatorCount", function ($expectedResult, $organizationId = null) {
            $functionResults = $this->organizationRoleRepository->getNonLoggedInCoordinatorCount($organizationId);
            verify($functionResults)->equals($expectedResult);
        }, [
            'examples' => [
                //Organization with only active non-logged in coordinators
                [
                    1,
                    70
                ],
                //Organization with both active and inactive non-logged in coordinators. Inactives are not included in the list.
                [
                    1,
                    2
                ],
                //No passed organization ID
                [
                    0
                ],
                //Invalid organization ID
                [
                    0,
                    "This is not an integer."
                ]
            ]
        ]);
    }


}