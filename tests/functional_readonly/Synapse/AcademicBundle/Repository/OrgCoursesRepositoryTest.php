<?php

class OrgCoursesRepository extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\AcademicBundle\Repository\OrgCoursesRepository
     */
    private $orgCoursesRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgCoursesRepository = $this->repositoryResolver->getRepository("SynapseAcademicBundle:OrgCourses");
    }

    public function testGetAllCoursesEncapsulatingDatetime()
    {
        $this->specify("test getAllCoursesEncapsulatingDatetime", function ($expectedResults, $organizationId = null, $datetimeString = null) {
            $actualResults = $this->orgCoursesRepository->getAllCoursesEncapsulatingDatetime($organizationId, $datetimeString);
            verify($actualResults)->equals($expectedResults);
        }, ['examples' => [
            //Valid organization ID, valid datetime string. Returns all courses in the term overlapping the datetime string
            [
                [
                    0 => [
                        "org_courses_id" => '157923'
                    ],
                    1 => [
                        "org_courses_id" => '157924'
                    ],
                    2 => [
                        "org_courses_id" => '157925'
                    ],
                    3 => [
                        "org_courses_id" => '157926'
                    ],
                    4 => [
                        "org_courses_id" => '157927'
                    ],
                    5 => [
                        "org_courses_id" => '157928'
                    ]
                ],
                2,
                '2015-10-01 00:00:00'
            ],
            //Valid organization, valid datetime string but no courses over that datetime.
            [
                [],
                2,
                '2016-10-01 00:00:00'
            ],
            //Valid organization, valid datetime string but no courses over that datetime.
            [
                [],
                2,
                '2159-10-01 00:00:00'
            ],
            //Valid organization, no datetime. No results returned.
            [
                [],
                2
            ],
            //No organization, no datetime. No results returned.
            [
                []
            ],
            //No organization, valid datetime. No results returned.
            [
                [],
                null,
                '2016-10-01 00:00:00'
            ],
        ]]);
    }

    public function testGetCoursesForStudent()
    {
        $this->specify('', function ($organizationId, $studentId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText, $expectedResults) {
            $functionResults = $this->orgCoursesRepository->getCoursesForStudent($organizationId, $studentId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            [
                190,
                4708243,
                '',
                'all',
                'all',
                'all',
                'all',
                '',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '232777',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233097',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    2 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233482',
                        'subject_code' => 'TEP',
                        'course_number' => '404',
                        'section_number' => '312',
                        'course_section_id' => 'QEN102010201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    3 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TLE',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233525',
                        'subject_code' => 'TLE',
                        'course_number' => '432',
                        'section_number' => 'OC6',
                        'course_section_id' => 'QLC130LC4201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    4 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64036',
                        'subject_code' => 'FOO',
                        'course_number' => '519',
                        'section_number' => '303',
                        'course_section_id' => 'COM217001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    5 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64008',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    6 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64233',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    7 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'SS[',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64579',
                        'subject_code' => 'SS[',
                        'course_number' => '403',
                        'section_number' => '307',
                        'course_section_id' => 'PSY101005',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    8 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64681',
                        'subject_code' => 'TEP',
                        'course_number' => '403',
                        'section_number' => '314',
                        'course_section_id' => 'QEN101012',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    9 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TMR',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64686',
                        'subject_code' => 'TMR',
                        'course_number' => '402',
                        'section_number' => '312',
                        'course_section_id' => 'QMP100010',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    10 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'VLF',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64553',
                        'subject_code' => 'VLF',
                        'course_number' => '402',
                        'section_number' => '303',
                        'course_section_id' => 'SLD100001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ],
            [
                190,
                4708243,
                '',
                '201516',
                'all',
                'all',
                'all',
                '',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '232777',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233097',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    2 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233482',
                        'subject_code' => 'TEP',
                        'course_number' => '404',
                        'section_number' => '312',
                        'course_section_id' => 'QEN102010201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    3 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TLE',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233525',
                        'subject_code' => 'TLE',
                        'course_number' => '432',
                        'section_number' => 'OC6',
                        'course_section_id' => 'QLC130LC4201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    4 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64036',
                        'subject_code' => 'FOO',
                        'course_number' => '519',
                        'section_number' => '303',
                        'course_section_id' => 'COM217001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    5 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64008',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    6 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64233',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    7 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'SS[',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64579',
                        'subject_code' => 'SS[',
                        'course_number' => '403',
                        'section_number' => '307',
                        'course_section_id' => 'PSY101005',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    8 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64681',
                        'subject_code' => 'TEP',
                        'course_number' => '403',
                        'section_number' => '314',
                        'course_section_id' => 'QEN101012',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    9 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TMR',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64686',
                        'subject_code' => 'TMR',
                        'course_number' => '402',
                        'section_number' => '312',
                        'course_section_id' => 'QMP100010',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    10 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'VLF',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64553',
                        'subject_code' => 'VLF',
                        'course_number' => '402',
                        'section_number' => '303',
                        'course_section_id' => 'SLD100001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ],
            [
                190,
                4708243,
                '2016-09-08',
                'all',
                'future',
                'all',
                'all',
                '',
                [

                ]
            ],
            [
                190,
                4708243,
                '2016-09-08',
                'all',
                'current',
                'all',
                'all',
                '',
                []
            ],
            [
                190,
                4708243,
                '',
                'all',
                '381',
                'all',
                'all',
                '',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '232777',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233097',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    2 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233482',
                        'subject_code' => 'TEP',
                        'course_number' => '404',
                        'section_number' => '312',
                        'course_section_id' => 'QEN102010201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    3 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TLE',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233525',
                        'subject_code' => 'TLE',
                        'course_number' => '432',
                        'section_number' => 'OC6',
                        'course_section_id' => 'QLC130LC4201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ],
            [
                190,
                4708243,
                '',
                'all',
                'all',
                'ORG-190',
                'all',
                '',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '232777',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233097',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    2 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233482',
                        'subject_code' => 'TEP',
                        'course_number' => '404',
                        'section_number' => '312',
                        'course_section_id' => 'QEN102010201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    3 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TLE',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233525',
                        'subject_code' => 'TLE',
                        'course_number' => '432',
                        'section_number' => 'OC6',
                        'course_section_id' => 'QLC130LC4201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    4 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64036',
                        'subject_code' => 'FOO',
                        'course_number' => '519',
                        'section_number' => '303',
                        'course_section_id' => 'COM217001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    5 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64008',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    6 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64233',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    7 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'SS[',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64579',
                        'subject_code' => 'SS[',
                        'course_number' => '403',
                        'section_number' => '307',
                        'course_section_id' => 'PSY101005',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    8 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TEP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64681',
                        'subject_code' => 'TEP',
                        'course_number' => '403',
                        'section_number' => '314',
                        'course_section_id' => 'QEN101012',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    9 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'TMR',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64686',
                        'subject_code' => 'TMR',
                        'course_number' => '402',
                        'section_number' => '312',
                        'course_section_id' => 'QMP100010',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    10 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'VLF',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64553',
                        'subject_code' => 'VLF',
                        'course_number' => '402',
                        'section_number' => '303',
                        'course_section_id' => 'SLD100001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ],
            [
                190,
                4708243,
                '',
                'all',
                'all',
                'all',
                'FOO',
                '',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '232777',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64036',
                        'subject_code' => 'FOO',
                        'course_number' => '519',
                        'section_number' => '303',
                        'course_section_id' => 'COM217001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ],
                    2 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'FOO',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64008',
                        'subject_code' => 'FOO',
                        'course_number' => '512',
                        'section_number' => '303',
                        'course_section_id' => 'COM210001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ],
            [
                190,
                4708243,
                '',
                'all',
                'all',
                'all',
                'all',
                'NIP',
                [
                    0 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '381',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'SPRING 2016',
                        'term_code' => 'SP',
                        'org_course_id' => '233097',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001201516SP',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2016-01-11',
                        'end_date' => '2016-05-31',
                        'current_or_future_term_course' => '0'
                    ],
                    1 => [
                        'college_code' => 'ORG-190',
                        'dept_code' => 'NIP',
                        'org_academic_year_id' => '95',
                        'org_academic_terms_id' => '214',
                        'year_id' => '201516',
                        'year_name' => '1516',
                        'term_name' => 'Fall 2015',
                        'term_code' => 'FA',
                        'org_course_id' => '64233',
                        'subject_code' => 'NIP',
                        'course_number' => '412',
                        'section_number' => '303',
                        'course_section_id' => 'KIN110001',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM',
                        'start_date' => '2015-08-11',
                        'end_date' => '2015-12-18',
                        'current_or_future_term_course' => '0'
                    ]
                ]
            ]

        ]]);
    }

    public function testGetCoursesForFaculty()
    {
        $this->specify('', function ($organizationId, $facultyId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText, $dataCount, $expectedResults) {
            $functionResults = $this->orgCoursesRepository->getCoursesForFaculty($organizationId, $facultyId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText, $dataCount);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [

            //All Courses for Given Faculty
            [
                63,
                250240,
                '',
                'all',
                'all',
                'all',
                'all',
                '',
                false,
                [
                    0 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191562',
                            'subject_code' => 'DGC',
                            'course_number' => '514',
                            'section_number' => '33',
                            'course_section_id' => '129376',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    1 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191707',
                            'subject_code' => 'DGD',
                            'course_number' => ';06',
                            'section_number' => 'FB03',
                            'course_section_id' => '129379',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    2 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191719',
                            'subject_code' => 'DGD',
                            'course_number' => ';16',
                            'section_number' => '31F',
                            'course_section_id' => '130695',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    3 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191578',
                            'subject_code' => 'DGD',
                            'course_number' => '513',
                            'section_number' => '31F',
                            'course_section_id' => '130608',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    4 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191592',
                            'subject_code' => 'DGD',
                            'course_number' => '537',
                            'section_number' => '33',
                            'course_section_id' => '129378',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    5 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191692',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => 'FB03',
                            'course_section_id' => '139190',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    6 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191727',
                            'subject_code' => 'DGR',
                            'course_number' => '759',
                            'section_number' => '33',
                            'course_section_id' => '129380',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    7 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22410',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '33',
                            'course_section_id' => '127995',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    8 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22416',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '34',
                            'course_section_id' => '127996',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    9 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22443',
                            'subject_code' => 'DGD',
                            'course_number' => '632',
                            'section_number' => '33',
                            'course_section_id' => '127999',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    10 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22481',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => '33',
                            'course_section_id' => '128001',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    11 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22491',
                            'subject_code' => 'DGE',
                            'course_number' => '517',
                            'section_number' => '33',
                            'course_section_id' => '128002',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    12 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22512',
                            'subject_code' => 'DGU',
                            'course_number' => '415',
                            'section_number' => '33',
                            'course_section_id' => '128008',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                ]
            ],
            //Specific Year Submitted for Given Faculty
            [
                63,
                250240,
                '',
                '201617',
                'all',
                'all',
                'all',
                '',
                false,
                []
            ],
            //Current Term For Given Faculty
            [
                63,
                250240,
                '2016-09-08',
                'all',
                'current',
                'all',
                'all',
                '',
                false,
                []
            ],
            //Specific Term For Given Faculty
            [
                63,
                250240,
                '',
                'all',
                '196',
                'all',
                'all',
                '',
                false,
                [
                    0 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191562',
                            'subject_code' => 'DGC',
                            'course_number' => '514',
                            'section_number' => '33',
                            'course_section_id' => '129376',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    1 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191707',
                            'subject_code' => 'DGD',
                            'course_number' => ';06',
                            'section_number' => 'FB03',
                            'course_section_id' => '129379',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    2 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191719',
                            'subject_code' => 'DGD',
                            'course_number' => ';16',
                            'section_number' => '31F',
                            'course_section_id' => '130695',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    3 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191578',
                            'subject_code' => 'DGD',
                            'course_number' => '513',
                            'section_number' => '31F',
                            'course_section_id' => '130608',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    4 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191592',
                            'subject_code' => 'DGD',
                            'course_number' => '537',
                            'section_number' => '33',
                            'course_section_id' => '129378',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    5 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191692',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => 'FB03',
                            'course_section_id' => '139190',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    6 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191727',
                            'subject_code' => 'DGR',
                            'course_number' => '759',
                            'section_number' => '33',
                            'course_section_id' => '129380',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ]
                ]
            ],
            //Specific College Code Submitted
            [
                63,
                250240,
                '',
                'all',
                'all',
                'ORG-063',
                'all',
                '',
                false,
                [
                    0 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191562',
                            'subject_code' => 'DGC',
                            'course_number' => '514',
                            'section_number' => '33',
                            'course_section_id' => '129376',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    1 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191707',
                            'subject_code' => 'DGD',
                            'course_number' => ';06',
                            'section_number' => 'FB03',
                            'course_section_id' => '129379',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    2 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191719',
                            'subject_code' => 'DGD',
                            'course_number' => ';16',
                            'section_number' => '31F',
                            'course_section_id' => '130695',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    3 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191578',
                            'subject_code' => 'DGD',
                            'course_number' => '513',
                            'section_number' => '31F',
                            'course_section_id' => '130608',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    4 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191592',
                            'subject_code' => 'DGD',
                            'course_number' => '537',
                            'section_number' => '33',
                            'course_section_id' => '129378',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    5 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191692',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => 'FB03',
                            'course_section_id' => '139190',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    6 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191727',
                            'subject_code' => 'DGR',
                            'course_number' => '759',
                            'section_number' => '33',
                            'course_section_id' => '129380',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    7 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22410',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '33',
                            'course_section_id' => '127995',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    8 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22416',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '34',
                            'course_section_id' => '127996',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    9 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22443',
                            'subject_code' => 'DGD',
                            'course_number' => '632',
                            'section_number' => '33',
                            'course_section_id' => '127999',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    10 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22481',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => '33',
                            'course_section_id' => '128001',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    11 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22491',
                            'subject_code' => 'DGE',
                            'course_number' => '517',
                            'section_number' => '33',
                            'course_section_id' => '128002',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    12 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22512',
                            'subject_code' => 'DGU',
                            'course_number' => '415',
                            'section_number' => '33',
                            'course_section_id' => '128008',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                ]
            ],
            //Specific Department Code Submitted
            [
                63,
                250240,
                '',
                'all',
                'all',
                'all',
                'DGRK',
                '',
                false,
                [
                    0 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191562',
                            'subject_code' => 'DGC',
                            'course_number' => '514',
                            'section_number' => '33',
                            'course_section_id' => '129376',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    1 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191707',
                            'subject_code' => 'DGD',
                            'course_number' => ';06',
                            'section_number' => 'FB03',
                            'course_section_id' => '129379',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    2 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191719',
                            'subject_code' => 'DGD',
                            'course_number' => ';16',
                            'section_number' => '31F',
                            'course_section_id' => '130695',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    3 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191578',
                            'subject_code' => 'DGD',
                            'course_number' => '513',
                            'section_number' => '31F',
                            'course_section_id' => '130608',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    4 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191592',
                            'subject_code' => 'DGD',
                            'course_number' => '537',
                            'section_number' => '33',
                            'course_section_id' => '129378',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    5 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191692',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => 'FB03',
                            'course_section_id' => '139190',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    6 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191727',
                            'subject_code' => 'DGR',
                            'course_number' => '759',
                            'section_number' => '33',
                            'course_section_id' => '129380',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    7 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22410',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '33',
                            'course_section_id' => '127995',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    8 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22416',
                            'subject_code' => 'DGC',
                            'course_number' => '417',
                            'section_number' => '34',
                            'course_section_id' => '127996',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    9 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22443',
                            'subject_code' => 'DGD',
                            'course_number' => '632',
                            'section_number' => '33',
                            'course_section_id' => '127999',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    10 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22481',
                            'subject_code' => 'DGD',
                            'course_number' => '739',
                            'section_number' => '33',
                            'course_section_id' => '128001',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    11 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22491',
                            'subject_code' => 'DGE',
                            'course_number' => '517',
                            'section_number' => '33',
                            'course_section_id' => '128002',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                    12 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '195',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Fall 2015',
                            'term_code' => '08_2015',
                            'org_course_id' => '22512',
                            'subject_code' => 'DGU',
                            'course_number' => '415',
                            'section_number' => '33',
                            'course_section_id' => '128008',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ],
                ]
            ],
            //Search Text Submitted with department text
            [
                63,
                250240,
                '',
                'all',
                'all',
                'all',
                'all',
                'DGR',
                false,
                [
                    0 =>
                        [
                            'college_code' => 'ORG-063',
                            'dept_code' => 'DGRK',
                            'org_academic_year_id' => '60',
                            'org_academic_terms_id' => '196',
                            'year_id' => '201516',
                            'year_name' => '2015-2016',
                            'term_name' => 'Spring 2016',
                            'term_code' => '01_2016',
                            'org_course_id' => '191727',
                            'subject_code' => 'DGR',
                            'course_number' => '759',
                            'section_number' => '33',
                            'course_section_id' => '129380',
                            'course_name' => 'Test Course',
                            'location' => 'Dagobah',
                            'days_times' => 'MWF 12:00 PM - 12:50 PM',
                            'create_view_academic_update' => '0',
                            'view_all_academic_update_courses' => '0'
                        ]
                ]
            ],
            //Search Text Submitted with department text and getting the count
            [
                63,
                250240,
                '',
                'all',
                'all',
                'all',
                'all',
                'DGR',
                true,
                1
            ],


        ]]);
    }

    public function testGetCoursesForOrganization()
    {
        $this->specify('', function ($organizationId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText, $recordsPerPage, $offset, $formatResultSet, $isInternal, $dataCount, $expectedResults) {
            $functionResults = $this->orgCoursesRepository->getCoursesForOrganization($organizationId, $currentDate, $year, $term, $collegeCode, $departmentCode, $searchText, $recordsPerPage, $offset, $formatResultSet, $isInternal, $dataCount);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            //Testing top 5 of all selections with no year
            [
                192,
                '',
                'all',
                'all',
                'all',
                'all',
                '',
                5,
                0,
                true,
                true,
                false,
                [
                    0 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84904',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ';:',
                        'course_section_id' => '29083',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    1 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84903',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ':9',
                        'course_section_id' => '29091',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    2 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84905',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '<;',
                        'course_section_id' => '29084',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    3 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84906',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '42',
                        'course_section_id' => '29085',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    4 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84897',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '43',
                        'course_section_id' => '29074',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ]
                ]
            ],
            //Specific Year Passed
            [
                192,
                '',
                '201516',
                'all',
                'all',
                'all',
                '',
                5,
                0,
                true,
                true,
                false,
                [
                    0 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84904',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ';:',
                        'course_section_id' => '29083',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    1 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84903',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ':9',
                        'course_section_id' => '29091',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    2 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84905',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '<;',
                        'course_section_id' => '29084',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    3 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84906',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '42',
                        'course_section_id' => '29085',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    4 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84897',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '43',
                        'course_section_id' => '29074',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ]

                ]
            ],
            //Future Terms
            [
                192,
                '2016-09-08',
                'all',
                'future',
                'all',
                'all',
                '',
                5,
                0,
                true,
                true,
                false,
                []
            ],
            //Current Terms
            [
                192,
                '2016-09-08',
                'all',
                'current',
                'all',
                'all',
                '',
                5,
                0,
                true,
                true,
                false,
                []
            ],
            //College Code Selection
            [
                192,
                '',
                'all',
                'all',
                'ORG-192',
                'all',
                '',
                5,
                0,
                true,
                true,
                false,
                [
                    0 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84904',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ';:',
                        'course_section_id' => '29083',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    1 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84903',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ':9',
                        'course_section_id' => '29091',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    2 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84905',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '<;',
                        'course_section_id' => '29084',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    3 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84906',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '42',
                        'course_section_id' => '29085',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    4 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84897',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '43',
                        'course_section_id' => '29074',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ]
                ]
            ],
            //Dept Code Selection
            [
                192,
                '',
                'all',
                'all',
                'all',
                'JE"',
                '',
                5,
                0,
                true,
                true,
                false,
                [
                    0 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84904',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ';:',
                        'course_section_id' => '29083',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    1 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84903',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ':9',
                        'course_section_id' => '29091',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    2 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84905',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '<;',
                        'course_section_id' => '29084',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    3 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84906',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '42',
                        'course_section_id' => '29085',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    4 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84897',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '43',
                        'course_section_id' => '29074',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ]
                ]
            ],
            //Course Section Search Text
            [
                192,
                '',
                'all',
                'all',
                'all',
                'all',
                '403',
                5,
                0,
                true,
                true,
                false,
                [
                    0 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84904',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ';:',
                        'course_section_id' => '29083',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    1 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84903',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => ':9',
                        'course_section_id' => '29091',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    2 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84905',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '<;',
                        'course_section_id' => '29084',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    3 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84906',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '42',
                        'course_section_id' => '29085',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ],
                    4 => [
                        'college_code' => 'ORG-192',
                        'dept_code' => 'JE"',
                        'year_id' => '201516',
                        'year_name' => 'FALL 2015 AND SPRING 2016',
                        'term_name' => 'FALL 2015',
                        'term_code' => 'FA2015',
                        'org_course_id' => '84897',
                        'subject_code' => 'JE"',
                        'course_number' => '403',
                        'section_number' => '43',
                        'course_section_id' => '29074',
                        'course_name' => 'Test Course',
                        'location' => 'Dagobah',
                        'days_times' => 'MWF 12:00 PM - 12:50 PM'
                    ]
                ]
            ],
            // Count Selection
            [
                192,
                '',
                'all',
                'all',
                'all',
                'all',
                '403',
                5,
                0,
                true,
                true,
                true,
                13
            ]
        ]]);
    }

    public function testGetCountOfFacultyInCourse()
    {
        $this->specify('', function ($organizationId, $courseIds, $expectedResults) {
            $functionResults = $this->orgCoursesRepository->getCountOfFacultyInCourse($organizationId, $courseIds);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            [
                192, [84897],
                [
                    0 =>
                        [
                            'course_id' => 84897,
                            'faculty_count' => 3
                        ],
                ]
            ],

            [
                192, [84897, 84898, 84899, 84900, 84901, 84902, 84903, 84904, 84905, 84906, 84907, 84908, 84909],
                [
                    0 =>
                        [
                            'course_id' => 84897,
                            'faculty_count' => 3
                        ],

                    1 =>
                        [
                            'course_id' => 84898,
                            'faculty_count' => 3
                        ],
                    2 =>
                        [
                            'course_id' => 84899,
                            'faculty_count' => 1
                        ],
                    3 =>
                        [
                            'course_id' => 84900,
                            'faculty_count' => 3
                        ],
                    4 =>
                        [
                            'course_id' => 84901,
                            'faculty_count' => 3
                        ],
                    5 =>
                        [
                            'course_id' => 84902,
                            'faculty_count' => 2
                        ],
                    6 =>
                        [
                            'course_id' => 84903,
                            'faculty_count' => 3
                        ],
                    7 =>
                        [
                            'course_id' => 84904,
                            'faculty_count' => 2
                        ],
                    8 =>
                        [
                            'course_id' => 84905,
                            'faculty_count' => 3
                        ],
                    9 =>
                        [
                            'course_id' => 84906,
                            'faculty_count' => 2
                        ],
                    10 =>
                        [
                            'course_id' => 84907,
                            'faculty_count' => 2
                        ],
                    11 =>
                        [
                            'course_id' => 84908,
                            'faculty_count' => 2
                        ],
                    12 =>
                        [
                            'course_id' => 84909,
                            'faculty_count' => 2
                        ]
                ]
            ]
        ]]);
    }

    public function testGetCountOfStudentInCourse()
    {
        $this->specify('', function ($organizationId, $courseIds, $expectedResults) {
            $functionResults = $this->orgCoursesRepository->getCountOfStudentInCourse($organizationId, $courseIds);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            [
                192, [84897],
                [
                    0 =>
                        [
                            'course_id' => 84897,
                            'student_count' => 26
                        ],
                ]
            ],
            [
                192, [84897, 84898, 84899, 84900, 84901, 84902, 84903, 84904, 84905, 84906, 84907, 84908, 84909],
                [
                    0 =>
                        [
                            'course_id' => 84897,
                            'student_count' => 26
                        ],

                    1 =>
                        [
                            'course_id' => 84898,
                            'student_count' => 52
                        ],
                    2 =>
                        [
                            'course_id' => 84899,
                            'student_count' => 68
                        ],
                    3 =>
                        [
                            'course_id' => 84900,
                            'student_count' => 38
                        ],
                    4 =>
                        [
                            'course_id' => 84901,
                            'student_count' => 54
                        ],
                    5 =>
                        [
                            'course_id' => 84902,
                            'student_count' => 36
                        ],
                    6 =>
                        [
                            'course_id' => 84903,
                            'student_count' => 47
                        ],
                    7 =>
                        [
                            'course_id' => 84904,
                            'student_count' => 69
                        ],
                    8 =>
                        [
                            'course_id' => 84905,
                            'student_count' => 41
                        ],
                    9 =>
                        [
                            'course_id' => 84906,
                            'student_count' => 24
                        ],
                    10 =>
                        [
                            'course_id' => 84907,
                            'student_count' => 24
                        ],
                    11 =>
                        [
                            'course_id' => 84908,
                            'student_count' => 31
                        ],
                    12 =>
                        [
                            'course_id' => 84909,
                            'student_count' => 26
                        ]
                ]
            ]
        ]]);
    }

    public function testGetAllStudentsInCourse()
    {
        $this->specify('Verify the functionality of the method getAllStudentsInCourse', function ($courseId, $organizationId, $isReturnInternalIds, $expectedResults) {
            $results = $this->orgCoursesRepository->getAllStudentsInCourse($courseId, $organizationId, $isReturnInternalIds);
            verify($results)->equals($expectedResults);
        }, ['examples' => [
            [
                // Invalid organization id
                257925,
                -89,
                true,
                []
            ],
            [
                // List of internal student id's for a course
                235860,
                203,
                true,
                [
                    ['student_id' => 4878818],
                    ['student_id' => 4879100],
                    ['student_id' => 4879301],
                    ['student_id' => 4879701]
                ]
            ],
            [
                // No students associated with the course
                257925,
                203,
                true,
                []
            ],
            [
                // List of external student id for a course
                235860,
                203,
                false,
                [
                    ['student_id' => 4878818],
                    ['student_id' => 4879100],
                    ['student_id' => 4879301],
                    ['student_id' => 4879701]
                ]
            ],
            [
                // Invalid course id for organization
                33942,
                203,
                true,
                []
            ],
        ]]);
    }


    public function testGetSingleCourseFacultiesDetails()
    {
        $this->specify('Verify the functionality of the method getSingleCourseFacultiesDetails', function ($courseId, $organizationId, $isInternal, $expectedResults) {

            $results = $this->orgCoursesRepository->getSingleCourseFacultiesDetails($courseId, $organizationId, $isInternal);
            verify($results)->equals($expectedResults);

        }, [
            'examples' => [
                // Test0: Invalid Course Id, return blank array
                [
                    -1,
                    192,
                    false,
                    []
                ],
                // Test1: Valid Course Id and $isReturnInternalIds is true, return result array of faculty details with internal ids
                [
                    84909,
                    192,
                    true,
                    [
                        [
                            'faculty_id' => '4543329',
                            'org_permission_set' => '1141',
                            'permissionset' => 'AllAccess',
                            'firstname' => 'Hank',
                            'lastname' => 'Schwartz',
                            'primary_email' => 'MapworksBetaUser04543329@mailinator.com',
                            'external_id' => '4543329',
                        ],
                        [
                            'faculty_id' => '4867933',
                            'org_permission_set' => '1146',
                            'permissionset' => 'CourseOnly',
                            'firstname' => 'Aylin',
                            'lastname' => 'Whitney',
                            'primary_email' => 'MapworksBetaUser04867933@mailinator.com',
                            'external_id' => '4867933',
                        ]
                    ]
                ],
                // Test2: Valid Course Id and $isReturnInternalIds is false, return result array of faculty details without internal ids
                [
                    84909,
                    192,
                    false,
                    [
                        [
                            'permissionset' => 'AllAccess',
                            'firstname' => 'Hank',
                            'lastname' => 'Schwartz',
                            'primary_email' => 'MapworksBetaUser04543329@mailinator.com',
                            'external_id' => '4543329',
                        ],
                        [
                            'permissionset' => 'CourseOnly',
                            'firstname' => 'Aylin',
                            'lastname' => 'Whitney',
                            'primary_email' => 'MapworksBetaUser04867933@mailinator.com',
                            'external_id' => '4867933',
                        ]
                    ]
                ],
            ]
        ]);
    }
}