<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

class PersonRepositoryTest extends Test
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
     * @var PersonRepository
     */
    private $personRepository;

    private $riskIntendToLeavePersonData = [
        '0' => [
            'external_id' => '4866038',
            'mapworks_internal_id' => '4866038',
            'firstname' => 'Marissa',
            'lastname' => 'Adams',
            'organization_id' => 201,
            'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
            'risk_group_id' => 1,
            'risk_group_name' => '2015-2016 New Students ID: 1',
            'risk_level' => 6,
            'risk_updated_date' => '2015-10-07 15:54:51',
            'risk_color_text' => 'gray',
            'risk_color_hex' => '#cccccc',
            'current_cohort' => 1,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
            'intent_to_leave_color_text' => 'dark gray',
            'intent_to_leave_color_hex' => '#626161'
        ],
        '1' => [
            'external_id' => 4866031,
            'mapworks_internal_id' => 4866031,
            'firstname' => 'Journee',
            'lastname' => 'Allen',
            'organization_id' => 201,
            'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
            'risk_group_id' => 1,
            'risk_group_name' => '2015-2016 New Students ID: 1',
            'risk_level' => 6,
            'risk_updated_date' => '2015-10-07 15:54:50',
            'risk_color_text' => 'gray',
            'risk_color_hex' => '#cccccc',
            'current_cohort' => 1,
            'intent_to_leave' => 5,
            'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
            'intent_to_leave_color_text' => 'dark gray',
            'intent_to_leave_color_hex' => '#626161'
        ]
            ];


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }


    public function testGetMyHighPriorityStudentsCount()
    {
        $this->specify("Verify the functionality of the method getMyHighPriorityStudentsCount", function ($facultyId, $orgId, $academicYearId, $expectedCount) {
            $result = $this->personRepository->getMyHighPriorityStudentsCount($facultyId, $orgId, $academicYearId);
            verify($result)->equals($expectedCount);
        }, ["examples" =>
            [
                [229929, 9, 97, 56],
                [4890955, 9, 97, 0],
                [4879335, 203, 158, 0],
                [4878750, 203, 158, 14],
                [4891668, 203, 158, 0]
            ]
        ]);
    }

    public function testGetStudentCountByRiskLevel()
    {

        $this->specify("Verify the functionality of the method getStudentCountByRiskLevel", function ($facultyId, $organizationId, $academicYearId, $expectedResultsSize, $expectedRiskLevelIds) {

            $results = $this->personRepository->getStudentCountByRiskLevel($facultyId, $organizationId, $academicYearId);
            verify(count($results))->equals($expectedResultsSize);
            $i = 0;
            foreach ($expectedRiskLevelIds as $riskText => $expectedRiskCount) {
                verify($results[$i]['count'])->notEmpty();
                verify($results[$i]['risk_text'])->equals($riskText);
                verify($results[$i]['count'])->equals($expectedRiskCount);
                $i++;
            }
        }, ["examples" =>
            [
                // Test01- Valid faculty, organization = 9 and academic year will return empty result array
                [4890955, 9, 97, 0, []],
                // Test02- Valid faculty, organization = 203 and academic year will return empty result array
                [4891668, 203, 158, 0, []],
                // Test03- Valid faculty = 157792, organization and academic year will return all risk text(red2, red, yellow, green)with their respective count
                [157792, 9, 97, 5, ["red2" => 13, "red" => 43, "yellow" => 73, "green" => 404]],
                // Test04- Valid faculty = 229929, organization and academic year will return all risk text(red2, red, yellow, green)with their respective count
                [229929, 9, 97, 5, ["red2" => 13, "red" => 43, "yellow" => 73, "green" => 404]],
                // Test05- Valid faculty = 229929, organization and academic year will return all risk text(red2, red, yellow, green)as well as gray risk text with their respective count
                [229929, 9, 97, 5, ["red2" => 13, "red" => 43, "yellow" => 73, "green" => 404, "gray" => 681]],
                // Test06- Invalid faculty and valid organization, academic year will return empty result array
                [-1, 203, 158, 0, []],
                // Test07- Faculty id as null and valid organization, academic year will return empty result array
                [null, 203, 158, 0, []],
                // Test08- Invalid organization and valid faculty, academic year will return empty result array
                [229929, -1, 158, 0, []],
                // Test09- Organization id as null and valid faculty, academic year will return empty result array
                [229929, null, 158, 0, []],
                // Test10- Invalid academic year and valid faculty, Organization will return empty result array
                [229929, 203, -1, 0, []],
                // Test11- Academic year as null and valid faculty, Organization will return empty result array
                [229929, 203, null, 0, []],
                // Test12- All required values are given as null will return empty result array
                [null, null, null, 0, []]
            ]
        ]);
    }

    public function testGetOrganizationFacultiesBySearchText()
    {
        $this->specify("Verify the functionality of the method getOrganizationFacultiesBySearchText", function ($expectedResultsSize, $expectedIds, $organization, $searchText, $activeFilter) {

            $results = $this->personRepository->getOrganizationFacultiesBySearchText($organization, $searchText, [-20], $activeFilter, 0, 25);
            verify(count($results))->equals($expectedResultsSize);
            if (count($results)) {
                for ($i = 0; $i < count($expectedIds); $i++) {
                    verify($results[$i]['id'])->notEmpty();
                    verify($results[$i]['id'])->equals($expectedIds[$i]);
                }
            }

        }, ["examples" =>
            [
                // Searching with first name
                [2, [4879376, 4879570], 203, 'Franco', 'all'],
                // Searching with email
                [1, [4883131], 203, 'MapworksBetaUser04883131@mailinator.com', 'all'],
                // Searching with lastname
                [2, [4879618, 4879591], 203, 'Atkins', 'all', 3],
                // Searching with externalid with status inactive
                [0, [], 2, '222', 'inactive'],
                // Searching with externalid with status active
                [1, [222], 2, '222', 'active'],
                // Searching with externalid with status all
                [1, [222], 2, '222', 'all'],
                // Searching with externalid with status inactive
                [1, [299], 2, '299', 'inactive']
            ]
        ]);
    }

    public function testGetOrganizationStudentsBySearchText()
    {
        $this->specify("Verify the functionality of the method getOrganizationStudentsBySearchText", function ($searchText, $academicYearId, $participantFilter, $sortBy, $personIdsToExclude, $limit, $offset, $expectedResults) {

            $results = $this->personRepository->getOrganizationStudentsBySearchText(203, $academicYearId, $personIdsToExclude, $searchText, $participantFilter, $sortBy, $limit, $offset);
            $this->assertEquals($results, $expectedResults);
        }, ["examples" =>
            [
                // List participant students with the search text '4878841'
                [
                    '4878841', 158, 'participants', '', [], 25, 0,
                    [
                        [
                            'modified_at' => '2016-01-26 18:04:41',
                            'id' => '4878841',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2341',
                            'external_id' => '4878841',
                            'firstname' => 'Tyson',
                            'lastname' => 'Dodson',
                            'primary_email' => 'MapworksBetaUser04878841@mailinator.com',
                            'participant' => '1',
                            'status' => '1'
                        ]
                    ]
                ],
                // List non participants where pagination and limit will be empty
                [
                    '4878841', 158, 'non-participants', '', [], 0, 0, []
                ],
                // Listing Participant students the results will be order by external_id asc
                [
                    '', 159, 'participants', 'external_id', [], 1, 0,
                    [
                        [
                            'modified_at' => '2016-01-26 18:04:41',
                            'id' => '4878841',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2341',
                            'external_id' => '4878841',
                            'firstname' => 'Tyson',
                            'lastname' => 'Dodson',
                            'primary_email' => 'MapworksBetaUser04878841@mailinator.com',
                            'participant' => '1',
                            'status' => '1'
                        ]

                    ]
                ],
                // Listing Participant students the results will be order by external_id descending
                ['', 159, 'participants', '-external_id', [], 25, 0,
                    [
                        [
                            'modified_at' => '2016-01-26 18:05:05',
                            'id' => '4878905',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2405',
                            'external_id' => '4878905',
                            'firstname' => 'Barrett',
                            'lastname' => 'Maddox',
                            'primary_email' => 'MapworksBetaUser04878905@mailinator.com',
                            'participant' => '1',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:04:41',
                            'id' => '4878841',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2341',
                            'external_id' => '4878841',
                            'firstname' => 'Tyson',
                            'lastname' => 'Dodson',
                            'primary_email' => 'MapworksBetaUser04878841@mailinator.com',
                            'participant' => '1',
                            'status' => '1'
                        ]
                    ]
                ],
                // List all students with the search text as an empty and page limit as 25
                [
                    '', 159, 'all', '', [], 25, 0,
                    [
                        [
                            'modified_at' => '2016-01-26 19:42:36',
                            'id' => '4879587',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3087',
                            'external_id' => '4879587',
                            'firstname' => 'Chloe',
                            'lastname' => 'Abbott',
                            'primary_email' => 'MapworksBetaUser04879587@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:46:42',
                            'id' => '4879823',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3323',
                            'external_id' => '4879823',
                            'firstname' => 'Haley',
                            'lastname' => 'Acevedo',
                            'primary_email' => 'MapworksBetaUser04879823@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:04:32',
                            'id' => '4878823',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2323',
                            'external_id' => '4878823',
                            'firstname' => 'Raymond',
                            'lastname' => 'Acevedo',
                            'primary_email' => 'MapworksBetaUser04878823@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:30:01',
                            'id' => '4879376',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2876',
                            'external_id' => '4879376',
                            'firstname' => 'Franco',
                            'lastname' => 'Acosta',
                            'primary_email' => 'MapworksBetaUser04879376@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-06-20 19:41:07',
                            'id' => '4897407',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => NULL,
                            'external_id' => '4897407',
                            'firstname' => 'Darrell',
                            'lastname' => 'Adkins',
                            'primary_email' => 'MapworksBetaUser04897407@mailinator.com',
                            'participant' => 0,
                            'status' => 0
                        ],
                        [
                            'modified_at' => '2016-01-26 19:30:16',
                            'id' => '4879407',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2907',
                            'external_id' => '4879407',
                            'firstname' => 'Reuben',
                            'lastname' => 'Adkins',
                            'primary_email' => 'MapworksBetaUser04879407@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:23:21',
                            'id' => '4879212',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2712',
                            'external_id' => '4879212',
                            'firstname' => 'Carmelo',
                            'lastname' => 'Aguilar',
                            'primary_email' => 'MapworksBetaUser04879212@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:42:05',
                            'id' => '4879507',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3007',
                            'external_id' => '4879507',
                            'firstname' => 'Jericho',
                            'lastname' => 'Aguirre',
                            'primary_email' => 'MapworksBetaUser04879507@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:22:32',
                            'id' => '4879110',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2610',
                            'external_id' => '4879110',
                            'firstname' => 'Nasir',
                            'lastname' => 'Alexander',
                            'primary_email' => 'MapworksBetaUser04879110@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:04:55',
                            'id' => '4878875',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2375',
                            'external_id' => '4878875',
                            'firstname' => 'Gregory',
                            'lastname' => 'Ali',
                            'primary_email' => 'MapworksBetaUser04878875@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:47:25',
                            'id' => '4879875',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3375',
                            'external_id' => '4879875',
                            'firstname' => 'June',
                            'lastname' => 'Ali',
                            'primary_email' => 'MapworksBetaUser04879875@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:42:36',
                            'id' => '4879589',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3089',
                            'external_id' => '4879589',
                            'firstname' => 'Eleanor',
                            'lastname' => 'Allison',
                            'primary_email' => 'MapworksBetaUser04879589@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:29:35',
                            'id' => '4879293',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2793',
                            'external_id' => '4879293',
                            'firstname' => 'Kamari',
                            'lastname' => 'Alvarado',
                            'primary_email' => 'MapworksBetaUser04879293@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:22:39',
                            'id' => '4879131',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2631',
                            'external_id' => '4879131',
                            'firstname' => 'Jalen',
                            'lastname' => 'Alvarez',
                            'primary_email' => 'MapworksBetaUser04879131@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:05:30',
                            'id' => '4878953',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2453',
                            'external_id' => '4878953',
                            'firstname' => 'Dexter',
                            'lastname' => 'Andersen',
                            'primary_email' => 'MapworksBetaUser04878953@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:43:45',
                            'id' => '4879665',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3165',
                            'external_id' => '4879665',
                            'firstname' => 'Hazel',
                            'lastname' => 'Andrade',
                            'primary_email' => 'MapworksBetaUser04879665@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:23:13',
                            'id' => '4879205',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2705',
                            'external_id' => '4879205',
                            'firstname' => 'Atlas',
                            'lastname' => 'Andrews',
                            'primary_email' => 'MapworksBetaUser04879205@mailinator.com',
                            'participant' => 0,
                            'status' => 0
                        ],
                        [
                            'modified_at' => '2016-01-26 19:42:32',
                            'id' => '4879579',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3079',
                            'external_id' => '4879579',
                            'firstname' => 'Ava',
                            'lastname' => 'Anthony',
                            'primary_email' => 'MapworksBetaUser04879579@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:05:29',
                            'id' => '4878957',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2457',
                            'external_id' => '4878957',
                            'firstname' => 'Dillon',
                            'lastname' => 'Archer',
                            'primary_email' => 'MapworksBetaUser04878957@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:05:05',
                            'id' => '4878904',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2404',
                            'external_id' => '4878904',
                            'firstname' => 'Grady',
                            'lastname' => 'Arellano',
                            'primary_email' => 'MapworksBetaUser04878904@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:46:21',
                            'id' => '4879786',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3286',
                            'external_id' => '4879786',
                            'firstname' => 'Keira',
                            'lastname' => 'Arias',
                            'primary_email' => 'MapworksBetaUser04879786@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:23:13',
                            'id' => '4879203',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2703',
                            'external_id' => '4879203',
                            'firstname' => 'Payton',
                            'lastname' => 'Armstrong',
                            'primary_email' => 'MapworksBetaUser04879203@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 19:23:02',
                            'id' => '4879186',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2686',
                            'external_id' => '4879186',
                            'firstname' => 'Otto',
                            'lastname' => 'Arnold',
                            'primary_email' => 'MapworksBetaUser04879186@mailinator.com',
                            'participant' => '0',
                            'status' => '0',
                        ],
                        [
                            'modified_at' => '2016-01-26 19:46:47',
                            'id' => '4879832',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-3332',
                            'external_id' => '4879832',
                            'firstname' => 'Chelsea',
                            'lastname' => 'Arroyo',
                            'primary_email' => 'MapworksBetaUser04879832@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                        [
                            'modified_at' => '2016-01-26 18:04:37',
                            'id' => '4878832',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2332',
                            'external_id' => '4878832',
                            'firstname' => 'Cristian',
                            'lastname' => 'Arroyo',
                            'primary_email' => 'MapworksBetaUser04878832@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ]
                    ]
                ],
                // List non-participants students with the search text 'Leland'
                [
                    'Leland', 160, 'non-participants', '', [4878841], 25, 0,
                    [
                        [
                            'modified_at' => '2016-01-26 18:05:40',
                            'id' => '4878969',
                            'welcome_email_sent_date' => NULL,
                            'title' => NULL,
                            'primary_mobile' => NULL,
                            'home_phone' => '555-555-2469',
                            'external_id' => '4878969',
                            'firstname' => 'Leland',
                            'lastname' => 'Ritter',
                            'primary_email' => 'MapworksBetaUser04878969@mailinator.com',
                            'participant' => '0',
                            'status' => '0'
                        ],
                    ]
                ]
            ]
        ]);
    }

    public function testGetOrganizationFacultyCountBySearchText()
    {
        $this->specify("Verify the functionality of the method getOrganizationFacultyCountBySearchText", function ($organization, $searchText, $activeFIlter, $totalCount) {

            $results = $this->personRepository->getOrganizationFacultyCountBySearchText($organization, $searchText, [-20], $activeFIlter);

            verify($results)->equals($totalCount);
        }, ["examples" =>
            [
                // search for firstname
                [203, 'Franco', 'all', 2],
                // search for email
                [203, 'MapworksBetaUser04883131@mailinator.com', 'all', 1],
                // search for lastname
                [203, 'Atkins', 'all', 2],
                // search for external id with status in active
                [2, '222', 'inactive', 0],
                // search for external id with status active
                [2, '222', 'active', 1],
                // search for external id
                [2, '222', 'all', 1],
                // search for external id  with status inactive
                [2, '299', 'inactive', 1]
            ]
        ]);
    }

    public function testGetOrganizationStudentCountBySearchText()
    {
        $this->specify("Verify the functionality of the method getOrganizationStudentCountBySearchText", function ($academicYearId, $searchText, $personIdsToExclude, $participantFilter, $expectedResults) {

            $results = $this->personRepository->getOrganizationStudentCountBySearchText(203, $academicYearId, $personIdsToExclude, $searchText, $participantFilter);
            $this->assertEquals($results, $expectedResults);

        }, ["examples" =>
            [
                // Count participant students.
                [157, '', '', 'participants', 1050],
                // Count non-participant students by passing search text.
                [157, '', [4878841], 'non-participants', 0],
                // Count all students - include participant and non-participant
                [158, '4878841', '', 'all', 1],
                // Count all students by search text - 'testing'
                [160, 'testing', '', 'all', 0],
                // Student count when no search text and participant is passed.
                [161, '', '', '', 1050]
            ]
        ]);
    }

    public function testGetAggregateRiskCountsPermissionCheck()
    {
        $this->specify("Verify the functionality of the method GetAggregateRiskCountsWithoutPermissionCheck", function ($studentIds, $facultyId, $expectedResult) {

            $results = $this->personRepository->getAggregateRiskCountsWithPermissionCheck($studentIds, $facultyId);
            verify($results)->equals($expectedResult);

        }, ["examples" =>
                [
                    // Students with each color
                    [
                        [5, 6, 7, 8],
                        2,
                        [
                        [
                            'risk_text' => 'red2',
                            'color_count' => 1,
                        ],
                        [
                            'risk_text' => 'red',
                            'color_count' => 1,
                        ],
                        [
                            'risk_text' => 'yellow',
                            'color_count' => 1,
                        ],
                        [
                            'risk_text' => 'green',
                            'color_count' => 1,
                        ]

                    ]
                    ],
                    //More Students
                    [
                        [14, 15, 16, 17],
                        2,
                        [
                            [
                                'risk_text' => 'red',
                                'color_count' => 1,
                            ],
                            [
                                'risk_text' => 'yellow',
                                'color_count' => 2,
                            ],
                            [
                                'risk_text' => 'green',
                                'color_count' => 1,
                            ]

                        ]
                    ],
                    //Mixed Permission, one I have permission, other I do not have risk indicator permission
                    [
                        [56, 180],
                        247,
                        [
                            [
                                'risk_text' => 'yellow',
                                'color_count' => 1,
                            ]
                        ]
                    ],
                    //
                ]
        ]);
    }

    public function testGetUsersBySearchText()
    {
        $this->specify('test getUsersBySearchText', function ($organizationId, $orgAcademicYearId, $searchText, $pageNumber, $numberOfRecords, $expectedResults) {
            $functionResults = $this->personRepository->getUsersBySearchText($organizationId, $orgAcademicYearId, $searchText, $pageNumber, $numberOfRecords);
            $this->assertEquals($functionResults, $expectedResults);

        }, ['examples' => [
            //Example 1 - Searching for all users with a name/username/external ID like 'Malik'.
            [
                62,
                48,
                'Malik',
                1,
                25,
                [
                    0 =>
                        [
                            'user_id' => '1038856',
                            'campus_id' => '62',
                            'external_id' => '1038856',
                            'first_name' => 'Malik',
                            'last_name' => 'Friedman',
                            'title' => null,
                            'email' => 'MapworksBetaUser01038856@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    1 =>
                        [
                            'user_id' => '4677036',
                            'campus_id' => '62',
                            'external_id' => '4677036',
                            'first_name' => 'Malik',
                            'last_name' => 'Green',
                            'title' => null,
                            'email' => 'MapworksBetaUser04677036@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    2 =>
                        [
                            'user_id' => '4715017',
                            'campus_id' => '62',
                            'external_id' => '4715017',
                            'first_name' => 'Malik',
                            'last_name' => 'Jackson',
                            'title' => null,
                            'email' => 'MapworksBetaUser04715017@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    3 =>
                        [
                            'user_id' => '749001',
                            'campus_id' => '62',
                            'external_id' => '749001',
                            'first_name' => 'Malik',
                            'last_name' => 'Johnson',
                            'title' => null,
                            'email' => 'MapworksBetaUser00749001@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    4 =>
                        [
                            'user_id' => '743004',
                            'campus_id' => '62',
                            'external_id' => '743004',
                            'first_name' => 'Malik',
                            'last_name' => 'Jones',
                            'title' => null,
                            'email' => 'MapworksBetaUser00743004@mailinator.com',
                            'user_type' => 'student',
                            'role' => null,
                        ],
                    5 =>
                        [
                            'user_id' => '4717016',
                            'campus_id' => '62',
                            'external_id' => '4717016',
                            'first_name' => 'Malik',
                            'last_name' => 'Martin',
                            'title' => null,
                            'email' => 'MapworksBetaUser04717016@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    6 =>
                        [
                            'user_id' => '741005',
                            'campus_id' => '62',
                            'external_id' => '741005',
                            'first_name' => 'Malik',
                            'last_name' => 'Miller',
                            'title' => null,
                            'email' => 'MapworksBetaUser00741005@mailinator.com',
                            'user_type' => 'student',
                            'role' => null,
                        ],
                    7 =>
                        [
                            'user_id' => '4719015',
                            'campus_id' => '62',
                            'external_id' => '4719015',
                            'first_name' => 'Malik',
                            'last_name' => 'Moore',
                            'title' => null,
                            'email' => 'MapworksBetaUser04719015@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    8 =>
                        [
                            'user_id' => '1036857',
                            'campus_id' => '62',
                            'external_id' => '1036857',
                            'first_name' => 'Malik',
                            'last_name' => 'Moses',
                            'title' => null,
                            'email' => 'MapworksBetaUser01036857@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    9 =>
                        [
                            'user_id' => '387182',
                            'campus_id' => '62',
                            'external_id' => '387182',
                            'first_name' => 'Malik',
                            'last_name' => 'Payne',
                            'title' => null,
                            'email' => 'MapworksBetaUser00387182@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    10 =>
                        [
                            'user_id' => '381185',
                            'campus_id' => '62',
                            'external_id' => '381185',
                            'first_name' => 'Malik',
                            'last_name' => 'Pierce',
                            'title' => null,
                            'email' => 'MapworksBetaUser00381185@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    11 =>
                        [
                            'user_id' => '4679035',
                            'campus_id' => '62',
                            'external_id' => '4679035',
                            'first_name' => 'Malik',
                            'last_name' => 'Scott',
                            'title' => null,
                            'email' => 'MapworksBetaUser04679035@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    12 =>
                        [
                            'user_id' => '4713018',
                            'campus_id' => '62',
                            'external_id' => '4713018',
                            'first_name' => 'Malik',
                            'last_name' => 'Thompson',
                            'title' => null,
                            'email' => 'MapworksBetaUser04713018@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ]
                ]
            ],
            //Example 2 - Searching for all faculty and coordinators with a name/username/external ID like 'Kase'.
            [
                20,
                null,
                'Kase',
                1,
                25,
                [
                    0 =>
                        [
                            'user_id' => '231679',
                            'campus_id' => '20',
                            'external_id' => '231679',
                            'first_name' => 'Kase',
                            'last_name' => 'Oneal',
                            'title' => 'Coord-Stu Rec/Inter, Acad Svcs',
                            'email' => 'MapworksBetaUser00231679@mailinator.com',
                            'user_type' => 'coordinator',
                            'role' => 'Primary coordinator'
                        ]

                ]
            ],
            //Example 3 - Searching for all users with a name/username/external ID like 'Yang'.
            [
                20,
                34,
                'Yang',
                1,
                25,
                [
                    0 =>
                        [
                            'user_id' => '4671853',
                            'campus_id' => '20',
                            'external_id' => '4671853',
                            'first_name' => 'Angela',
                            'last_name' => 'Finley',
                            'title' => null,
                            'email' => 'MapworksBetaUser04671853@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    1 =>
                        [
                            'user_id' => '1031227',
                            'campus_id' => '20',
                            'external_id' => '1031227',
                            'first_name' => 'Aryan',
                            'last_name' => 'Greene',
                            'title' => null,
                            'email' => 'MapworksBetaUser01031227@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    2 =>
                        [
                            'user_id' => '1032396',
                            'campus_id' => '20',
                            'external_id' => '1032396',
                            'first_name' => 'Astrid',
                            'last_name' => 'Yang',
                            'title' => null,
                            'email' => 'MapworksBetaUser01032396@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    3 =>
                        [
                            'user_id' => '1031396',
                            'campus_id' => '20',
                            'external_id' => '1031396',
                            'first_name' => 'Kohen',
                            'last_name' => 'Yang',
                            'title' => null,
                            'email' => 'MapworksBetaUser01031396@mailinator.com',
                            'user_type' => 'student',
                            'role' => null
                        ],
                    4 =>
                        [
                            'user_id' => '247396',
                            'campus_id' => '20',
                            'external_id' => '247396',
                            'first_name' => 'Malakai',
                            'last_name' => 'Yang',
                            'title' => 'PrtnrCrps AmeriCrps Mmbr-Knox',
                            'email' => 'MapworksBetaUser00247396@mailinator.com',
                            'user_type' => 'Staff/Faculty',
                            'role' => null
                        ]

                ]
            ]
        ]]);
    }


    public function testGetMapworksStudents()
    {

        $this->specify("Verify the functionality of the getting mapworks students", function ($organizationId, $searchText, $expectedResults) {

            $functionResults = $this->personRepository->getMapworksStudents($organizationId, $searchText);
            $this->assertEquals($functionResults, $expectedResults);
        }, ["examples" =>
            [
                // Searching for the student with text "Nadia" (firstname) returns 4556008 (name :  Nadia Rodriguez))
                [
                    3, "nadia", [4556008]
                ],
                // Searching for the student with "dia" (part of firstname), returns 4556008 (name :  Nadia Rodriguez)
                [
                    3, "dia", [4556008]
                ],
                // Searching for the student with "iguez" (part of last name), returns 4556008 (name :  Nadia Rodriguez)
                [
                    3, "iguez", [4556008]
                ],
                // Searching for the student with "Rodriguez" (last name), returns 4556008 (name :  Nadia Rodriguez)
                [
                    3, "Rodriguez", [4556008]
                ],
                // Searching  with text "4556008" (externalid), returns  the student having external id  with "4556008"
                [
                    3, "4556008", [4556008]
                ],
                // Searching  with text "NadiaRodriguez" (first and lastname), returns all the students having name NadiaRodriguez
                [
                    3, "NadiaRodriguez", [4556008]
                ],
                // Searching  with text "RodriguezNadia" (last and firstname), returns all the students having name NadiaRodriguez
                [
                    3, "RodriguezNadia", [4556008]
                ],
                // Searching  with text "MapworksBetaUser04556008@mailinator.com" (username), returns  the students having username "MapworksBetaUser04556008@mailinator.com"
                [
                    3, "MapworksBetaUser04556008@mailinator.com", [4556008]
                ],
                // Searching  with text "4556" (part of externalid), returns all the students having external id  starting with "4556"
                [
                    3, "4556", [4556008, 4556009, 4556010]
                ],
                // Returns all students for organization id  = 3
                [
                    3, null, [4556008, 4556009, 4556010, 4774035, 4871314]

                ],
                // Returns all students for organization id  = 200
                [
                    200, null, [4751066, 4751067, 4751068, 4751069, 4751070, 4751071, 4751072]
                ],
                // Returns all students for organization id  = 183
                [
                    183, null, [4836331, 4836332, 4836333, 4836334, 4836335, 4836336, 4836337, 4836338, 4836339]
                ]
            ]
        ]);

    }


    public function testGetMapworksFaculty()
    {

        $this->specify("Verify the functionality of the getting mapworks faculty", function ($organizationId, $searchText, $expectedResults) {

            $functionResults = $this->personRepository->getMapworksFaculty($organizationId, $searchText);
            $this->assertEquals($functionResults, $expectedResults);
        }, ["examples" =>
            [
                //Get all faculties for the organization id = 197
                [
                    197, null, [4614029, 4614476, 4614477]
                ],
                //Get all faculties for the organization id = 183
                [
                    183, null, [256031, 256044]
                ],
                // Get all Faculties for organization id = 200 (only one faculty)
                [
                    200, null, [4751065]
                ],
                // Searching for the student with "mal" (part of firstname) , returns 4751065 (name :  Malcom Bailey)
                [
                    200, "mal", [4751065]
                ],
                // Searching for the student with "com" (part of firstname), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "com", [4751065]
                ],
                // Searching for the student with "BAiley" (lastname), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "BAiley", [4751065]
                ],
                // Searching for the student with "ley"(part of lastname) , returns 4751065 (name :  Malcom Bailey)
                [
                    200, "ley", [4751065]
                ],
                // Searching  with text "47510" (part of externalid), returns all the faculty having external id  starting with "47510"
                [
                    200, "47510", [4751065]
                ],
                // Searching  with text "4751065" (external id), returns  the faculty having external id  starting with "47510"
                [
                    200, "4751065", [4751065]
                ],
                // Searching  with text " MalcomBailey" (first and lastname), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "MalcolmBailey", [4751065]
                ],
                // Searching  with text " BaileyMalcom" (last and firstname), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "BaileyMalcolm", [4751065]
                ],
                // Searching  with text " BaileyMal" (last and part of firstname), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "BaileyMal", [4751065]
                ],
                // Searching  with text "MapworksBetaUser04751065@mailinator.com" (username), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "MapworksBetaUser04751065@mailinator.com", [4751065]
                ],
                // Searching  with text "MapworksBetaUser04751065" (part of username), returns 4751065 (name :  Malcom Bailey)
                [
                    200, "MapworksBetaUser04751065", [4751065]
                ]

            ]
        ]);

    }

    public function testGetMapworksOrphanUsers()
    {

        $this->specify("Verify the functionality of the getting mapworks orphan users", function ($organizationId, $searchText, $expectedResults) {

            $functionResults = $this->personRepository->getMapworksOrphanUsers($organizationId, $searchText);
            $this->assertEquals($functionResults, $expectedResults);
        }, ["examples" =>
            [
                //Get all orphan users for the organization id = 197
                [
                    2, null, [3, 4, 4582599, 4582715, 4582824, 4583746, 4584263, 4585333, 4585710, 4585782, 4590584, 4592721, 4593715, 4593853, 4599021]
                ],
                //Get all orphan users for the organization id = 2 with search text "victor" (first name) (4 => victor belt)
                [
                    2, "victor", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "belt" (last name)(4 => victor belt)
                [
                    2, "belt", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "tor" (part of firstname)(4 => victor belt) victorbelt@macmillan.com
                [
                    2, "tor", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "victorbelt" (first and last name)(4 => victor belt) victorbelt@macmillan.com
                [
                    2, "victorbelt", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "victorbelt@macmillan.com" (username)(4 => victor belt)
                [
                    2, "victorbelt@macmillan.com", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "victorbelt@macmillan" (part of username)(4 => victor belt)
                [
                    2, "victorbelt@macmillan", [4]
                ],
                //Get all orphan users for the organization id =2  with search text "beltvictor" (last and firstname)(4 => victor belt)
                [
                    2, "beltvictor", [4]
                ]
            ]
        ]);


    }

    public function testGetMapworksPersons()
    {

        $this->specify("Verify the functionality of the getting mapworks persons", function ($organizationId, $searchText, $expectedResults) {

            $functionResults = $this->personRepository->getMapworksPersons($organizationId, $searchText);

            $this->assertEquals($functionResults, $expectedResults);
        }, ["examples" =>
            [
                //Get all Person for the organization id = 184
                [
                    184, null, [256033, 256035, 256046]
                ],
                //Get all Person for the organization id = 184 with search text gracie (firstname)(256033 => garcie wright)
                [
                    184, "gracie", [256033]
                ],
                //Get all Person for the organization id = 184 with search text wright(lastname) (256033 => garcie wright)
                [
                    184, "wright", [256033]
                ],
                //Get all Person for the organization id = 184 with search text raci(part of firstname) (256033 => garcie wright)
                [
                    184, "raci", [256033]
                ],
                //Get all Person for the organization id = 184 with search text graciewright(first and last name) (256033 => garcie wright)
                [
                    184, "graciewright", [256033]
                ],
                //Get all Person for the organization id = 184 with search text graciewr(first and part of last name) (256033 => garcie wright)
                [
                    184, "graciewr", [256033]
                ],
                //Get all Person for the organization id = 184 with search text wrightgracie(last and first name) (256033 => garcie wright)
                [
                    184, "wrightgracie", [256033]
                ],
                //Get all Person for the organization id = 184 with search text "MapworksBetaUser00256033@mailinator.com"(username) (256033 => garcie wright)
                [
                    184, "MapworksBetaUser00256033@mailinator.com", [256033]
                ],
                //Get all Person for the organization id = 184 with search text "MapworksBetaUser00256033"(part of username) (256033 => garcie wright)
                [
                    184, "MapworksBetaUser00256033", [256033]
                ],

            ]
        ]);

    }

    public function testGetMapworksPersonData()
    {

        $this->specify("Verify the functionality of the getting mapworks persons data", function ($organizationId, $personIds, $offset, $recordsPerPage, $expectedResults) {

            $functionResults = $this->personRepository->getMapworksPersonData($organizationId, $personIds, $offset, $recordsPerPage);

            $this->assertEquals($functionResults, $expectedResults);
        }, ["examples" =>
            [
                //Get all Person for the organization id = 184 , total of 3 records
                [
                    184, [256033, 256035, 256046], 0, 4,
                    [
                        [
                            'external_id' => "256046",
                            'mapworks_internal_id' => "256046",
                            'auth_username' => "",
                            'firstname' => "Valeria",
                            'lastname' => "Phillips",
                            'primary_email' => "MapworksBetaUser00256046@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => "",
                        ],
                        [
                            'external_id' => "256035",
                            'mapworks_internal_id' => "256035",
                            'auth_username' => "",
                            'firstname' => "Emilia",
                            'lastname' => "Scott",
                            'primary_email' => "MapworksBetaUser00256035@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => ""
                        ],
                        [
                            'external_id' => "256033",
                            'mapworks_internal_id' => "256033",
                            'auth_username' => "",
                            'firstname' => "Gracie",
                            'lastname' => "Wright",
                            'primary_email' => "MapworksBetaUser00256033@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => ""
                        ]

                    ]
                ],

                // Pagination test , 1 record per page , offset =0 , returns 1st out of 3 records
                [
                    184, [256033, 256035, 256046], 0, 1,
                    [
                        [
                            'external_id' => "256046",
                            'mapworks_internal_id' => "256046",
                            'auth_username' => "",
                            'firstname' => "Valeria",
                            'lastname' => "Phillips",
                            'primary_email' => "MapworksBetaUser00256046@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => "",
                        ]
                    ]
                ],
                // Pagination test , 1 record per page , offset =1, returns 2nd out of 3 records
                [
                    184, [256033, 256035, 256046], 1, 1,
                    [
                        [
                            'external_id' => "256035",
                            'mapworks_internal_id' => "256035",
                            'auth_username' => "",
                            'firstname' => "Emilia",
                            'lastname' => "Scott",
                            'primary_email' => "MapworksBetaUser00256035@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => ""
                        ]
                    ]

                ],
                // Pagination test , 1 record per page , offset=1, returns 3rd out of 3 records
                [
                    184, [256033, 256035, 256046], 2, 1,
                    [
                        [
                            'external_id' => "256033",
                            'mapworks_internal_id' => "256033",
                            'auth_username' => "",
                            'firstname' => "Gracie",
                            'lastname' => "Wright",
                            'primary_email' => "MapworksBetaUser00256033@mailinator.com",
                            'photo_url' => "",
                            'is_student' => 0,
                            'is_faculty' => 1,
                            'primary_connection_person_id' => "",
                            'risk_group_id' => "",
                            'risk_group_description' => ""
                        ]
                    ]
                ],
                // No Person ids passed, will return empty array
                [
                    184, [], 2, 1, []
                ]
            ]
        ]);
    }

    public function testGetPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria()
    {

        $this->specify("Verify the functionality of the getting persons current risk and intent to leave filter by student criteria", function ($organizationId, $searchText, $cohort, $riskGroupId, $recordsPerPage, $offset, $currentOrgAcademicYearId, $expectedResults, $arraySlice = false) {

            $functionResults = $this->personRepository->getPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria($organizationId, $currentOrgAcademicYearId, $searchText, $cohort, $riskGroupId, $recordsPerPage, $offset);

            if ($arraySlice) {
                $functionResults = array_slice($functionResults, 0, 2);
            }
            $this->assertEquals($functionResults, $expectedResults);

        }, ["examples" =>
            [
                // No search filter, no current cohort parameter, no risk group parameter, no pagination (0, 0, 0, 0)
                [
                    201, null, null, null, null, null, 147, $this->getRiskAndIntentToLeavePersonDetails([]), true
                ],
                // Search filter, no current cohort parameter, no risk group parameter, no pagination (1, 0, 0, 0)
                [
                    201, 'Marissa', null, null, null, null, 147, $this->getRiskAndIntentToLeavePersonDetails([0]), true
                ],
                // Search filter, current cohort parameter, no risk group parameter, no pagination (1, 1, 0, 0)
                [
                    201, 'Marissa', 1, null, null, null, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // Search filter, current cohort parameter, risk group parameter, no pagination (1, 1, 1, 0)
                [
                    201, 'Marissa', 1, 1, null, null, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // Search filter, current cohort parameter, risk group parameter, pagination (1, 1, 1, 1)
                [
                    201, 'Marissa', 1, 1, 1, 0, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // No search filter, current cohort parameter, no risk group parameter, no pagination (0, 1, 0, 0)
                [
                    201, null, 1, null, null, null, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ],
                    '1' => [
                        'external_id' => 4866031,
                        'mapworks_internal_id' => 4866031,
                        'firstname' => 'Journee',
                        'lastname' => 'Allen',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:50',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // No search filter, current cohort parameter, risk group parameter, no pagination (0, 1, 1, 0)
                [
                    201, null, 1, 1, null, null, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ],
                    '1' => [
                        'external_id' => 4866031,
                        'mapworks_internal_id' => 4866031,
                        'firstname' => 'Journee',
                        'lastname' => 'Allen',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:50',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // No search filter, current cohort parameter, risk group parameter, pagination (0, 1, 1, 1)
                [
                    201, null, 1, 1, 2, 0, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ],
                    '1' => [
                        'external_id' => 4866031,
                        'mapworks_internal_id' => 4866031,
                        'firstname' => 'Journee',
                        'lastname' => 'Allen',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:50',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // No search filter, no current cohort parameter, risk group parameter, no pagination(0, 0 , 1, 0)
                [
                    201, null, null, 1, null, null, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ],
                    '1' => [
                        'external_id' => 4866031,
                        'mapworks_internal_id' => 4866031,
                        'firstname' => 'Journee',
                        'lastname' => 'Allen',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:50',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ], true
                ],
                // No search filter, no current cohort parameter, risk group parameter, pagination (0, 0, 1, 1)
                [
                    201, null, null, 1, 2, 0, 147, [
                    '0' => [
                        'external_id' => '4866038',
                        'mapworks_internal_id' => '4866038',
                        'firstname' => 'Marissa',
                        'lastname' => 'Adams',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866038@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:51',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ],
                    '1' => [
                        'external_id' => 4866031,
                        'mapworks_internal_id' => 4866031,
                        'firstname' => 'Journee',
                        'lastname' => 'Allen',
                        'organization_id' => 201,
                        'primary_email' => 'MapworksBetaUser04866031@mailinator.com',
                        'risk_group_id' => 1,
                        'risk_group_name' => '2015-2016 New Students ID: 1',
                        'risk_level' => 6,
                        'risk_updated_date' => '2015-10-07 15:54:50',
                        'risk_color_text' => 'gray',
                        'risk_color_hex' => '#cccccc',
                        'current_cohort' => 1,
                        'intent_to_leave' => 5,
                        'intent_to_leave_updated_date' => '2015-10-07 16:04:41',
                        'intent_to_leave_color_text' => 'dark gray',
                        'intent_to_leave_color_hex' => '#626161'
                    ]
                ]
                ],
                // Search filter, no current cohort parameter, no risk group parameter, pagination (0, 0, 0, 1)
                [
                    201, null, null, null, 2, 0, 147, $this->getRiskAndIntentToLeavePersonDetails([0, 1])
                ]
            ]
        ]);

    }

    public function getRiskAndIntentToLeavePersonDetails($studentFilterKeys)
    {
        if (empty($studentFilterKeys)) {
            return $this->riskIntendToLeavePersonData;
        } else {
            $resultArray = [];
            foreach ($studentFilterKeys as $studentFilterKey) {
                $resultArray[] = $this->riskIntendToLeavePersonData[$studentFilterKey];
            }
            return $resultArray;
        }
    }

}