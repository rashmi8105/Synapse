<?php

use Codeception\TestCase\Test;

class QuickSearchDAOTest extends Test
{
    use \Codeception\Specify;
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\SearchBundle\DAO\QuickSearchDAO
     */
    private $quickSearchDao;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->quickSearchDao = $this->container->get('quick_search_dao');
    }

    public function testSearchFor100StudentsAsCoordinator()
    {
        $this->specify("Verify the functionality of the method searchFor100StudentsAsCoordinator", function ($organizationId, $searchString, $orgAcademicYearId, $expectedResults) {
            $results = $this->quickSearchDao->searchFor100StudentsAsCoordinator($organizationId, $searchString, $orgAcademicYearId);
            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [

                // Example 1:  Single token matching a single field (email)
                [2, 'kurtmoderson', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => '1'
                        ]
                    ]
                ],
                // Example 2:  Single token which matches varying fields
                [2, 'moderson', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4636540,
                            'firstname' => 'Test',
                            'lastname' => 'Moderson',
                            'external_id' => 4636540,
                            'primary_email' => 'Test2015Moderson@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 3:  Two tokens separated by a space
                [2, 'Kurt Moderson', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 4:  Two tokens separated by a comma and space
                [2, 'Moderson, Kurt', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 5:  Two tokens separated by a comma
                [2, 'Moderson,Kurt', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 6:  A token that's a partial name
                [2, 'Kurt Mod', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 7:  Tokens matching the middle or end of names
                [2, 'urt so', 88,
                    [
                        [
                            'person_id' => 4897439,
                            'firstname' => 'Test',
                            'lastname' => 'Cohort1',
                            'external_id' => 4897439,
                            'primary_email' => 'kurtmoderson.testing@gmail.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 8:  Three tokens
                [2, 'Kurt Moderson mailinator', 88,
                    [
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4622310,
                            'firstname' => 'Kurt',
                            'lastname' => 'Moderson',
                            'external_id' => 4622310,
                            'primary_email' => 'Moderson.Kurt@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 9:  Search by external_id
                [2, 4628424, 88,
                    [
                        [
                            'person_id' => 4628424,
                            'firstname' => 'Moderson',
                            'lastname' => 'Kurt',
                            'external_id' => 4628424,
                            'primary_email' => 'ModersonKurttest@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 10:  Ensure SQL injection is prevented
                [2, '\') or p.id in (select id from person) ##', 88, []]

            ]
        ]);
    }

    public function testSearchFor100StudentsAsFaculty()
    {
        $this->specify("Verify the functionality of the method searchFor100StudentsAsFaculty", function ($organizationId, $facultyId, $searchString, $orgAcademicYearId, $purpose, $expectedResults) {
            $results = $this->quickSearchDao->searchFor100StudentsAsFaculty($organizationId, $facultyId, $searchString, $orgAcademicYearId, $purpose);


            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [

                // Example 1:  Single token (note that case doesn't matter)
                [203, 4883106, 'branson', 157, null,
                    [
                        [
                            'person_id' => 4879232,
                            'firstname' => 'Branson',
                            'lastname' => 'Jacobs',
                            'external_id' => 4879232,
                            'primary_email' => 'MapworksBetaUser04879232@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 2:  Two tokens separated by a space
                [203, 4883106, 'branson jacobs', 157, null,
                    [
                        [
                            'person_id' => 4879232,
                            'firstname' => 'Branson',
                            'lastname' => 'Jacobs',
                            'external_id' => 4879232,
                            'primary_email' => 'MapworksBetaUser04879232@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 3:  Two tokens separated by a comma
                [203, 4883106, 'Jacobs, Branson', 157, null,
                    [
                        [
                            'person_id' => 4879232,
                            'firstname' => 'Branson',
                            'lastname' => 'Jacobs',
                            'external_id' => 4879232,
                            'primary_email' => 'MapworksBetaUser04879232@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 4:  Tokens that are partial names
                [203, 4883106, 'br j', 157, null,
                    [
                        [
                            'person_id' => 4879232,
                            'firstname' => 'Branson',
                            'lastname' => 'Jacobs',
                            'external_id' => 4879232,
                            'primary_email' => 'MapworksBetaUser04879232@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 5:  Tokens matching the middle or end of names
                [203, 4883106, 'an ley', 157, null,
                    [
                        [
                            'person_id' => 4878813,
                            'firstname' => 'Emiliano',
                            'lastname' => 'Bentley',
                            'external_id' => 4878813,
                            'primary_email' => 'MapworksBetaUser04878813@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4879209,
                            'firstname' => 'Morgan',
                            'lastname' => 'Riley',
                            'external_id' => 4879209,
                            'primary_email' => 'MapworksBetaUser04879209@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 6:  Search by external_id
                [203, 4883106, 4879232, 157, null,
                    [
                        [
                            'person_id' => 4879232,
                            'firstname' => 'Branson',
                            'lastname' => 'Jacobs',
                            'external_id' => 4879232,
                            'primary_email' => 'MapworksBetaUser04879232@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 7:  Ensure SQL injection is prevented
                [203, 4883106, '\') or p.id in (select id from person) ##', 157, null, []],
                // Example 8:  Ensure students aren't returned that this faculty member doesn't have access to.
                // (Note that there is a student named Travis in org 203.)
                [203, 4883106, 'Travis', 157, null, []],
                // Example 9a:  Ensure aggregate-only students aren't accessible.
                // This faculty member has both an aggregate-only (101) and an individual (1323) permission set,
                // but is only connected to a student named Avery via groups which have the aggregate permission set (parent group 352758).
                [18, 178146, 'Avery', 6, null, []],
                // Example 9b:  This faculty member can search for students she is connected to via the individual permission set.
                [18, 178146, 'Faith', 6, null,
                    [
                        [
                            'person_id' => 4723675,
                            'firstname' => 'Faith',
                            'lastname' => 'Eaton',
                            'external_id' => 4723675,
                            'primary_email' => 'MapworksBetaUser04723675@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 10a:  This faculty member only has permission set 446, which does not give him permission to create appointments.
                // When this function is called without the $purpose parameter, this faculty member has access to a student named Colin.
                [76, 67492, 'Colin', 41, null,
                    [
                        [
                            'person_id' => 938670,
                            'firstname' => 'Colin',
                            'lastname' => 'Dyer',
                            'external_id' => 938670,
                            'primary_email' => 'MapworksBetaUser00938670@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 10b:  When $purpose has value 'appointment', no students are returned.
                [76, 67492, 'Colin', 41, 'appointment', []],
                // Example 11a:  This faculty member has permission set 584, which does not give him permission to create appointments,
                // and permission set 585, which allows him to create public appointments.
                // When this function is called without the $purpose parameter, this search returns 3 students.
                [99, 134964, 'Martin J', 27, null,
                    [
                        [
                            'person_id' => 967016,
                            'firstname' => 'Jonas',
                            'lastname' => 'Martin',
                            'external_id' => 967016,
                            'primary_email' => 'MapworksBetaUser00967016@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 280016,
                            'firstname' => 'Juliana',
                            'lastname' => 'Martin',
                            'external_id' => 280016,
                            'primary_email' => 'MapworksBetaUser00280016@mailinator.com',
                            'status' => 1
                        ],
                        [
                            'person_id' => 4613010,
                            'firstname' => 'Jaylen',
                            'lastname' => 'Martinez',
                            'external_id' => 4613010,
                            'primary_email' => 'MapworksBetaUser04613010@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ],
                // Example 11b:  When $purpose has value 'appointment', only one student is returned
                // -- this faculty member is connected to this student via groups with both permission sets.
                [99, 134964, 'Martin J', 27, 'appointment',
                    [
                        [
                            'person_id' => 280016,
                            'firstname' => 'Juliana',
                            'lastname' => 'Martin',
                            'external_id' => 280016,
                            'primary_email' => 'MapworksBetaUser00280016@mailinator.com',
                            'status' => 1
                        ]
                    ]
                ]
            ]
        ]);
    }
}