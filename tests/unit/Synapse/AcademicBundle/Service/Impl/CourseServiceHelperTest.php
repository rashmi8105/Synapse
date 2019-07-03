<?php

use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicBundle\Service\Impl\CourseServiceHelper;

class CourseServiceHelperTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    private $courseDetailCoordinator = [
        "0" =>
            [
                'course_id' => 373418,
                'year_id' => 201617,
                'year_name' => '2016-2017',
                'term_name' => 'Spring',
                'term_code' => 'Spring2017',
                'subject_code' => 'a2',
                'course_number' => 'a3',
                'section_number' => 'a4',
                'course_section_id' => 'a1',
                'course_name' => 'a5',
                'location' => '',
                'days_times' => 12,
                'college_code' => 'a7',
                'dept_code' => 'a8'
            ]
    ];

    private $formattedCoordinatorList = [
        'total_courses' => 1,
        'total_faculty' => 2,
        'total_students' => 106,
        'course_list' =>
            [
                '0' =>
                    [
                        'year_id' => 201617,
                        'year_name' => '2016-2017',
                        'terms' =>
                            [
                                '0' =>
                                    [
                                        'term_code' => 'Spring2017',
                                        'term_name' => 'Spring',
                                        'colleges' =>
                                            [
                                                '0' =>
                                                    [
                                                        'college_code' => 'a7',
                                                        'departments' =>
                                                            [
                                                                '0' =>
                                                                    [
                                                                        'dept_code' => 'a8',
                                                                        'courses' =>
                                                                            [
                                                                                '0' =>
                                                                                    [
                                                                                        'subject_code' => 'a2',
                                                                                        'course_number' => 'a3',
                                                                                        'course_name' => 'a5',
                                                                                        'sections' =>
                                                                                            [
                                                                                                '0' =>
                                                                                                    [
                                                                                                        'course_id' => 373418,
                                                                                                        'section_number' => 'a4',
                                                                                                        'course_section_id' => 'a1',
                                                                                                        'location' => '',
                                                                                                        'days_times' => 12,
                                                                                                        'total_faculty' => 2,
                                                                                                        'total_students' => 106
                                                                                                    ]

                                                                                            ]

                                                                                    ]

                                                                            ]

                                                                    ]

                                                            ]

                                                    ]

                                            ]

                                    ]

                            ]

                    ]

            ]

    ];


    public function testFormatCourseList()
    {
        $this->specify("Test to get the formatted course list", function ($courseDetails, $faculty, $student, $userType, $courseListCount, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));

            $mockOrgCourseFacultyRepository = $this->getMock('orgCourseFacultyRepository', array('getCourseFacultyCountByOrganization'));
            $mockOrgCourseStudentRepository = $this->getMock('orgCourseStudentRepository', array('getCourseStudentCountByOrganization'));

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        OrgCourseFacultyRepository::REPOSITORY_KEY,
                        $mockOrgCourseFacultyRepository
                    ],
                    [
                        OrgCourseStudentRepository::REPOSITORY_KEY,
                        $mockOrgCourseStudentRepository
                    ]
                ]);
            $mockOrgCourseFacultyRepository->method('getCourseFacultyCountByOrganization')->willReturn(653);
            $mockOrgCourseStudentRepository->method('getCourseStudentCountByOrganization')->willReturn(111);

            $courseServiceHelper = new CourseServiceHelper($mockRepositoryResolver, $mockLogger);
            $results = $courseServiceHelper->formatCourseList($courseDetails, $faculty, $student, $userType, $courseListCount);

            $this->assertEquals($results, $expectedResult);

        }, [
            'examples' => [
                //Test formatted data of Course for coordinator
                [
                    $this->courseDetailCoordinator,
                    ['373418' => 2],
                    ['373418' => 106],
                    'coordinator',

                    1,
                    $this->formattedCoordinatorList
                ],
                //Test No Course data for coordinator
                [
                    [],
                    ['373418' => 2],
                    ['373418' => 106],
                    'coordinator',

                    0,
                    [
                        'total_courses' => 0,
                        'total_faculty' => 2,
                        'total_students' => 106,
                        'course_list' => []
                    ]
                ],
                //Test formatted data of Course for faculty
                [
                    [
                        '0' =>
                            [
                                'college_code' => 'a7',
                                'dept_code' => 'a8',
                                'org_academic_year_id' => 192,
                                'org_academic_terms_id' => 493,
                                'year_id' => 201617,
                                'year_name' => '2016-2017',
                                'term_name' => 'Spring',
                                'term_code' => 'Spring2017',
                                'org_course_id' => 373418,
                                'subject_code' => 'a2',
                                'course_number' => 'a3',
                                'section_number' => 'a4',
                                'course_section_id' => 'a1',
                                'course_name' => 'a5',
                                'location' => '',
                                'days_times' => 12,
                                'create_view_academic_update' => 1,
                                'view_all_academic_update_courses' => 1
                            ]

                    ],
                    ['373418' => 2],
                    ['373418' => 106],
                    'faculty',
                    1,
                    [
                        'total_courses' => 1,
                        'course_list' =>
                            [
                                '0' =>
                                    [
                                        'year_id' => 201617,
                                        'year_name' => '2016-2017',
                                        'terms' =>
                                            [
                                                '0' =>
                                                    [
                                                        'term_code' => 'Spring2017',
                                                        'term_name' => 'Spring',
                                                        'colleges' =>
                                                            [
                                                                '0' =>
                                                                    [
                                                                        'college_code' => 'a7',
                                                                        'departments' =>
                                                                            [
                                                                                '0' =>
                                                                                    [
                                                                                        'dept_code' => 'a8',
                                                                                        'courses' =>
                                                                                            [
                                                                                                '0' =>
                                                                                                    [
                                                                                                        'subject_code' => 'a2',
                                                                                                        'course_number' => 'a3',
                                                                                                        'course_name' => 'a5',
                                                                                                        'sections' =>
                                                                                                            [
                                                                                                                '0' =>
                                                                                                                    [
                                                                                                                        'course_id' => 373418,
                                                                                                                        'section_number' => 'a4',
                                                                                                                        'course_section_id' => 'a1',
                                                                                                                        'location' => '',
                                                                                                                        'days_times' => 12,
                                                                                                                        'total_faculty' => 2,
                                                                                                                        'total_students' => 106,
                                                                                                                        'create_view_academic_update' => 1,
                                                                                                                        'view_all_academic_update_courses' => 1
                                                                                                                    ]

                                                                                                            ]

                                                                                                    ]

                                                                                            ]

                                                                                    ]

                                                                            ]

                                                                    ]

                                                            ]

                                                    ]

                                            ]

                                    ]

                            ]

                    ]
                ],
                //No courses detail for faculty
                [
                    [],
                    ['373418' => 2],
                    ['373418' => 106],
                    'faculty',
                    0,
                    [
                        'total_courses' => 0,
                        'course_list' => []

                    ]
                ]

            ]
        ]);
    }


}