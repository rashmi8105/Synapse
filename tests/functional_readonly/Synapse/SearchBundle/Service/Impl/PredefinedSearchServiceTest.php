<?php

use Codeception\TestCase\Test;

class PredefinedSearchServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\SearchBundle\Service\Impl\PredefinedSearchService
     */
    private $predefinedSearchService;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->predefinedSearchService = $this->container->get('predefined_search_service');
    }


    public function testGetPredefinedSearchListByCategory()
    {
        $this->specify("Verify the functionality of the method getPredefinedSearchListByCategory", function($category, $facultyId, $expectedResult) {

            $result = $this->predefinedSearchService->getPredefinedSearchListByCategory($category, $facultyId);

            verify($result)->equals($expectedResult);

        }, ["examples" =>
            [
                // Example 1:  "Populations of Students" searches for someone with an all-access permission set (permission set 1397)
                ['student_search', 250474,
                    [
                        [
                            'search_key' => 'all_my_students',
                            'name' => 'All my students',
                            'description' => 'Students that I am connected to through either a group or course',
                            'last_run' => '2015-10-01T17:25:49+0000'
                        ],
                        [
                            'search_key' => 'my_primary_campus_connections',
                            'name' => 'My primary campus connections',
                            'description' => 'Students for whom I am the primary campus connection',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'at_risk_students',
                            'name' => 'At-risk students',
                            'description' => 'Students with a Red or Red 2 risk indicator',
                            'last_run' => '2015-10-20T17:18:22+0000'
                        ],
                        [
                            'search_key' => 'high_intent_to_leave',
                            'name' => 'Students with a high intent to leave',
                            'description' => 'Students who have indicated that they intend to leave the institution',
                            'last_run' => '2015-10-01T14:21:42+0000'
                        ],
                        [
                            'search_key' => 'high_priority_students',
                            'name' => 'High priority students',
                            'description' => 'Students who have not had any interaction contacts since their risk indicator changed to Red or Red 2',
                            'last_run' => '2015-10-21T05:26:08+0000'
                        ]
                    ]
                ],
                // Example 2:  "Academic Performance" searches for someone with an all-access permission set (permission set 1397)
                ['academic_update_search', 250474,
                    [
                        [
                            'search_key' => 'high_risk_of_failure',
                            'name' => 'High risk of failure',
                            'description' => 'Students with high risk of failure in any course in the current academic term(s)',
                            'last_run' => '2015-12-04T14:18:52+0000'
                        ],
                        [
                            'search_key' => 'four_or_more_absences',
                            'name' => 'Four or more absences',
                            'description' => 'Students with four or more absences in any course in the current academic term(s)',
                            'last_run' => '2015-12-04T14:23:12+0000'
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_c_or_below',
                            'name' => 'In-progress grade of C or below',
                            'description' => 'Students with an in-progress grade of C or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_d_or_below',
                            'name' => 'In-progress grade of D or below',
                            'description' => 'Students with an in-progress grade of D or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_in-progress_grades_of_d_or_below',
                            'name' => 'Two or more in-progress grades of D or below',
                            'description' => 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)',
                            'last_run' => '2015-09-29T14:38:37+0000'
                        ],
                        [
                            'search_key' => 'final_grade_of_c_or_below',
                            'name' => 'Final grade of C or below',
                            'description' => 'Students with a final grade of C or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_d_or_below',
                            'name' => 'Final grade of D or below',
                            'description' => 'Students with a final grade of D or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_final_grades_of_d_or_below',
                            'name' => 'Two or more final grades of D or below',
                            'description' => 'Students with a final grade of D or below in two or more courses in the current academic year',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 3:  "Activity" searches for someone with an all-access permission set (permission set 1397)
                ['activity_search', 250474,
                    [
                        [
                            'search_key' => 'interaction_contacts',
                            'name' => 'Students with interaction contacts',
                            'description' => 'Students who have had interaction contacts logged with them',
                            'last_run' => '2016-04-01T18:15:07+0000'
                        ],
                        [
                            'search_key' => 'no_interaction_contacts',
                            'name' => 'Students without any interaction contacts',
                            'description' => 'Students who have had no interaction contacts logged with them',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'have_not_been_reviewed',
                            'name' => 'Students who have not been reviewed by me since their risk changed',
                            'description' => 'Students whose profile pages have not been reviewed by me since their risk changed',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 4:  This faculty member only has an aggregate-only permission set, so shouldn't be able to run any predefined searches.
                ['student_search', 401375, []],
                // Example 5:  This faculty member is not connected to any students (even though he is in a group), so shouldn't be able to run any predefined searches.
                ['student_search', 176445, []],
                // Example 6:  "Populations of Students" searches for someone without permission to see risk or intent to leave (permission set 106)
                ['student_search', 185246,
                    [
                        [
                            'search_key' => 'all_my_students',
                            'name' => 'All my students',
                            'description' => 'Students that I am connected to through either a group or course',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'my_primary_campus_connections',
                            'name' => 'My primary campus connections',
                            'description' => 'Students for whom I am the primary campus connection',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 7:  "Populations of Students" searches for someone with permission to see risk but not intent to leave (permission set 537)
                ['student_search', 1029244,
                    [
                        [
                            'search_key' => 'all_my_students',
                            'name' => 'All my students',
                            'description' => 'Students that I am connected to through either a group or course',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'my_primary_campus_connections',
                            'name' => 'My primary campus connections',
                            'description' => 'Students for whom I am the primary campus connection',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'at_risk_students',
                            'name' => 'At-risk students',
                            'description' => 'Students with a Red or Red 2 risk indicator',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'high_priority_students',
                            'name' => 'High priority students',
                            'description' => 'Students who have not had any interaction contacts since their risk indicator changed to Red or Red 2',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 8:  "Populations of Students" searches for someone with permission to see intent to leave but not risk (permission set 1246)
                ['student_search', 143073,
                    [
                        [
                            'search_key' => 'all_my_students',
                            'name' => 'All my students',
                            'description' => 'Students that I am connected to through either a group or course',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'my_primary_campus_connections',
                            'name' => 'My primary campus connections',
                            'description' => 'Students for whom I am the primary campus connection',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'high_intent_to_leave',
                            'name' => 'Students with a high intent to leave',
                            'description' => 'Students who have indicated that they intend to leave the institution',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 9:  "Academic Performance" searches for someone who has no course-related or academic-update-related permissions (permission set 1239)
                ['academic_update_search', 231441, []],
                // Example 10:  "Academic Performance" searches for someone who has permission to view courses but has no academic-update-related permissions (permission set 106)
                ['academic_update_search', 185246, []],
                // Example 11:  "Academic Performance" searches for someone who has permission to create and view academic updates for her own courses,
                // but has no other course-related permissions (permission sets 1066, 1177) -- all the searches should be available.
                ['academic_update_search', 214613,
                    [
                        [
                            'search_key' => 'high_risk_of_failure',
                            'name' => 'High risk of failure',
                            'description' => 'Students with high risk of failure in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'four_or_more_absences',
                            'name' => 'Four or more absences',
                            'description' => 'Students with four or more absences in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_c_or_below',
                            'name' => 'In-progress grade of C or below',
                            'description' => 'Students with an in-progress grade of C or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_d_or_below',
                            'name' => 'In-progress grade of D or below',
                            'description' => 'Students with an in-progress grade of D or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_in-progress_grades_of_d_or_below',
                            'name' => 'Two or more in-progress grades of D or below',
                            'description' => 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_c_or_below',
                            'name' => 'Final grade of C or below',
                            'description' => 'Students with a final grade of C or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_d_or_below',
                            'name' => 'Final grade of D or below',
                            'description' => 'Students with a final grade of D or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_final_grades_of_d_or_below',
                            'name' => 'Two or more final grades of D or below',
                            'description' => 'Students with a final grade of D or below in two or more courses in the current academic year',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 12:  "Academic Performance" searches for someone who has permission to view courses, view academic updates, and view final grades,
                // but not create academic updates (permission sets 113, 1202)
                ['academic_update_search', 231532,
                    [
                        [
                            'search_key' => 'high_risk_of_failure',
                            'name' => 'High risk of failure',
                            'description' => 'Students with high risk of failure in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'four_or_more_absences',
                            'name' => 'Four or more absences',
                            'description' => 'Students with four or more absences in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_c_or_below',
                            'name' => 'In-progress grade of C or below',
                            'description' => 'Students with an in-progress grade of C or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_d_or_below',
                            'name' => 'In-progress grade of D or below',
                            'description' => 'Students with an in-progress grade of D or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_in-progress_grades_of_d_or_below',
                            'name' => 'Two or more in-progress grades of D or below',
                            'description' => 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_c_or_below',
                            'name' => 'Final grade of C or below',
                            'description' => 'Students with a final grade of C or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_d_or_below',
                            'name' => 'Final grade of D or below',
                            'description' => 'Students with a final grade of D or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_final_grades_of_d_or_below',
                            'name' => 'Two or more final grades of D or below',
                            'description' => 'Students with a final grade of D or below in two or more courses in the current academic year',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 13:  "Academic Performance" searches for someone who has permission to view courses and academic updates,
                // but not view final grades or create academic updates (permission set 113)
                ['academic_update_search', 229540,
                    [
                        [
                            'search_key' => 'high_risk_of_failure',
                            'name' => 'High risk of failure',
                            'description' => 'Students with high risk of failure in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'four_or_more_absences',
                            'name' => 'Four or more absences',
                            'description' => 'Students with four or more absences in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_c_or_below',
                            'name' => 'In-progress grade of C or below',
                            'description' => 'Students with an in-progress grade of C or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'in-progress_grade_of_d_or_below',
                            'name' => 'In-progress grade of D or below',
                            'description' => 'Students with an in-progress grade of D or below in any course in the current academic term(s)',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_in-progress_grades_of_d_or_below',
                            'name' => 'Two or more in-progress grades of D or below',
                            'description' => 'Students with an in-progress grades of D or below in two or more courses in the current academic term(s)',
                            'last_run' => null
                        ]
                    ]
                ],
                // Example 14:  "Academic Performance" searches for someone who has permission to view courses and view final grades,
                // but not view other academic updates or create academic updates (permission set 369)
                ['academic_update_search', 250049,
                    [
                        [
                            'search_key' => 'final_grade_of_c_or_below',
                            'name' => 'Final grade of C or below',
                            'description' => 'Students with a final grade of C or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'final_grade_of_d_or_below',
                            'name' => 'Final grade of D or below',
                            'description' => 'Students with a final grade of D or below in any course in the current academic year',
                            'last_run' => null
                        ],
                        [
                            'search_key' => 'two_or_more_final_grades_of_d_or_below',
                            'name' => 'Two or more final grades of D or below',
                            'description' => 'Students with a final grade of D or below in two or more courses in the current academic year',
                            'last_run' => null
                        ]
                    ]
                ]
            ]
        ]);
    }
}