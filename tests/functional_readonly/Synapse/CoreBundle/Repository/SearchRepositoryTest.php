<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Repository\SearchRepository;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;

class SearchRepositoryTest extends Test
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
     * @var SearchRepository
     */
    private $searchRepository;


    public function _before()
    {
        $this->container = $this->getModule(SynapseConstant::SYMFONY2_MODULE_KEY)->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->searchRepository = $this->repositoryResolver->getRepository(SearchRepository::REPOSITORY_KEY);
    }

    public function testGetPredefinedSearchListByCategory()
    {
        $this->specify(" test getPredefinedSearchListByCategory", function ($category, $facultyId, $expectedResult) {

            $results = $this->searchRepository->getPredefinedSearchListByCategory($category, $facultyId);

            verify($results)->equals($expectedResult);

        }, [
            "examples" =>
                [
                    //Academic Update searches
                    //TODO:: With the fixes coming in ESPRJ-15754, this test will fail after the migration is run against the functional_readonly database.
                    //TODO:: This test case will need to be update with 0 results returned, since all of the academic_update searches were marked inactive.
                    [
                        'academic_update_search',
                        100158,
                        [
                            [
                                "search_key" => 'high_risk_of_failure',
                                "name" => 'High risk of failure',
                                'description' => 'Students with high risk of failure in any course in the current academic term(s)',
                                "last_run" => '2015-09-03 20:06:52'
                            ],
                            [
                                "search_key" => 'four_or_more_absences',
                                "name" => 'Four or more absences',
                                'description' => 'Students with four or more absences in any course in the current academic term(s)',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'in-progress_grade_of_c_or_below',
                                "name" => 'In-progress grade of C or below',
                                'description' => 'Students with an in-progress grade of C or below in any course in the current academic term(s)',
                                "last_run" => '2015-09-03 20:00:06'
                            ],
                            [
                                "search_key" => 'in-progress_grade_of_d_or_below',
                                "name" => 'In-progress grade of D or below',
                                'description' => 'Students with an in-progress grade of D or below in any course in the current academic term(s)',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'two_or_more_in-progress_grades_of_d_or_below',
                                "name" => 'Two or more in-progress grades of D or below',
                                'description' => 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'final_grade_of_c_or_below',
                                "name" => 'Final grade of C or below',
                                'description' => 'Students with a final grade of C or below in any course in the current academic year',
                                "last_run" => '2015-09-03 20:06:10'
                            ],
                            [
                                "search_key" => 'final_grade_of_d_or_below',
                                "name" => 'Final grade of D or below',
                                'description' => 'Students with a final grade of D or below in any course in the current academic year',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'two_or_more_final_grades_of_d_or_below',
                                "name" => 'Two or more final grades of D or below',
                                'description' => 'Students with a final grade of D or below in two or more courses in the current academic year',
                                "last_run" => null
                            ]
                        ]
                    ],
                    //Student Searches
                    [
                        'student_search',
                        87523,
                        [
                            [
                                "search_key" => 'all_my_students',
                                "name" => 'All my students',
                                'description' => 'Students that I am connected to through either a group or course',
                                "last_run" => '2015-10-20 19:57:28'
                            ],
                            [
                                "search_key" => 'my_primary_campus_connections',
                                "name" => 'My primary campus connections',
                                'description' => 'Students for whom I am the primary campus connection',
                                "last_run" => '2015-10-12 15:19:02'
                            ],
                            [
                                "search_key" => 'at_risk_students',
                                "name" => 'At-risk students',
                                'description' => 'Students with a Red or Red 2 risk indicator',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'high_intent_to_leave',
                                "name" => 'Students with a high intent to leave',
                                'description' => 'Students who have indicated that they intend to leave the institution',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'high_priority_students',
                                "name" => 'High priority students',
                                'description' => 'Students who have not had any interaction contacts since their risk indicator changed to Red or Red 2',
                                "last_run" => null
                            ],
                        ]
                    ],
                    //Activity Searches
                    [
                        'activity_search',
                        84574,
                        [
                            [
                                "search_key" => 'interaction_contacts',
                                "name" => 'Students with interaction contacts',
                                'description' => 'Students who have had interaction contacts logged with them',
                                "last_run" =>  '2015-09-29 16:21:25'
                            ],
                            [
                                "search_key" => 'no_interaction_contacts',
                                "name" => 'Students without any interaction contacts',
                                'description' => 'Students who have had no interaction contacts logged with them',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'have_not_been_reviewed',
                                "name" => 'Students who have not been reviewed by me since their risk changed',
                                'description' => 'Students whose profile pages have not been reviewed by me since their risk changed',
                                "last_run" => '2015-09-22 18:37:03'
                            ],
                        ]
                    ],
                    //Valid faculty, invalid category
                    [
                        'Bet you wont find this category',
                        87523,
                        [

                        ]
                    ],
                    //Invalid faculty, valid category
                    [
                        'activity_search',
                        12345678931415629,
                        [
                            [
                                "search_key" => 'interaction_contacts',
                                "name" => 'Students with interaction contacts',
                                'description' => 'Students who have had interaction contacts logged with them',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'no_interaction_contacts',
                                "name" => 'Students without any interaction contacts',
                                'description' => 'Students who have had no interaction contacts logged with them',
                                "last_run" => null
                            ],
                            [
                                "search_key" => 'have_not_been_reviewed',
                                "name" => 'Students who have not been reviewed by me since their risk changed',
                                'description' => 'Students whose profile pages have not been reviewed by me since their risk changed',
                                "last_run" => null
                            ]
                        ]
                    ],
                    //Both invalid
                    [
                        'Wont find this category either',
                        31415629,
                        [

                        ]
                    ]
            ]]);
    }

}