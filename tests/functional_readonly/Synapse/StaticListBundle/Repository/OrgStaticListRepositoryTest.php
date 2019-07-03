<?php
use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\StaticListBundle\Repository\OrgStaticListStudentsRepository;
use Synapse\StaticListBundle\Repository\OrgStaticListRepository;

class OrgStaticListRepositoryTest extends Test
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
     * @var OrgStaticListRepository
     */
    private $orgStaticListRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgStaticListRepository = $this->repositoryResolver->getRepository(OrgStaticListRepository::REPOSITORY_KEY);

    }

    public function testGetStaticListsWithStudentId()
    {
        $this->specify('test Get Static Lists With Student id', function ($faultyId, $studentId, $orgAcademicYearId, $expectedResult) {

            $result = $this->orgStaticListRepository->getStaticListsWithStudentId($faultyId, $studentId, '', 300, 0, $orgAcademicYearId);

            verify($result)->equals($expectedResult);

        }, ['examples' => [
                //student 971065 is added in static list 1223
                [174388, 971065, 92, [0 => [
                    'id' => '1223',
                    'name' => 'F15_Survey_Outreach',
                    'description' => 'Students who expressed intent to leave, homesickness, or academic struggles and who should have staff performing outreach.',
                    'created_at' => '2015-10-02 16:39:39',
                    'modified_at' => '2015-10-02 16:39:39',
                    'created_by_person_id' => '174388',
                    'created_by_firstname' => 'Kailey',
                    'created_by_lastname' => 'Moss',
                    'modified_by_person_id' => null,
                    'modified_by_firstname' => null,
                    'modified_by_lastname' => null,
                    'student_count' => '175'
                ]]],
                //student 4543345 is added in static list 11
                [4551171, 4543345, 44, [0 => [
                    'id' => '11',
                    'name' => 'list test',
                    'description' => '',
                    'created_at' => '2015-08-16 08:19:32',
                    'modified_at' => '2015-08-16 08:19:32',
                    'created_by_person_id' => '4551171',
                    'created_by_firstname' => 'Tony',
                    'created_by_lastname' => 'Patel',
                    'modified_by_person_id' => null,
                    'modified_by_firstname' => null,
                    'modified_by_lastname' => null,
                    'student_count' => '1'
                ]]],
                //student 4622752 is not added in any static list
                [174388, 4622752, 92, []]
            ]]
        );
    }

    public function testGetCountOfStaticListsWithStudentID()
    {
        $this->markTestSkipped("To be fixed as a part of ESPRJ-14307");

        $this->specify('get Count Of Static Lists with Student id', function ($faultyId, $studentId, $organizationId, $expectedResult) {

            $staticListCount = $this->orgStaticListRepository->getCountOfStaticListsWithStudentID($faultyId, $studentId, $organizationId);

            verify($staticListCount)->equals($expectedResult);

        }, ['examples' => [
                //student 971065 is added in only one static list
                [174388, 971065, 120, 1],
                //student 4543345 is added in only one static list
                [4551171, 4543345, 182, 1],
                //student 4622752 is not added in any static list
                [4551171, 4622752, 182, 0]
            ]]
        );
    }

    public function testGetStaticListsForFaculty()
    {
        $this->specify('get Static Lists For Faculty', function ($faultyId, $orgAcademicYearId, $resultsPerPage, $expectedResult) {

            $result = $this->orgStaticListRepository->getStaticListsForFaculty($faultyId, '', $resultsPerPage, 0, $orgAcademicYearId);
            verify($result)->equals($expectedResult);
        }, ['examples' => [
                //Static lists created by person 174388
                [174388, 92, 5, [
                    0 => [
                        'id' => '3414',
                        'name' => '2016 Spring Very High Priority',
                        'description' => 'Students from midterm deficiencies with a "Very High" risk',
                        'created_at' => '2016-03-08 21:42:58',
                        'modified_at' => '2016-03-08 21:42:58',
                        'created_by_person_id' => '194367',
                        'created_by_firstname' => 'Emmalyn',
                        'created_by_lastname' => 'Le',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '0'
                    ],
                    1 => [
                        'id' => '2429',
                        'name' => 'Registration Attrition Profile F15 - S16',
                        'description' => 'FTIC students fitting the registered but higher risk of attrition profile between fall and spring.',
                        'created_at' => '2016-01-06 12:45:27',
                        'modified_at' => '2016-01-06 12:45:27',
                        'created_by_person_id' => '174388',
                        'created_by_firstname' => 'Kailey',
                        'created_by_lastname' => 'Moss',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '23'

                    ],
                    2 => [
                        'id' => '2122',
                        'name' => 'Week 13 Academic Monitoring',
                        'description' => 'Student athletes not involved in the test of academic updates.',
                        'created_at' => '2015-11-12 19:51:27',
                        'modified_at' => '2015-11-12 19:51:27',
                        'created_by_person_id' => '174388',
                        'created_by_firstname' => 'Kailey',
                        'created_by_lastname' => 'Moss',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '444'
                    ],
                    3 => [
                        'id' => '2115',
                        'name' => 'Academic Update Test',
                        'description' => '6 student test of academic update functionality.',
                        'created_at' => '2015-11-11 21:23:08',
                        'modified_at' => '2015-11-11 21:23:08',
                        'created_by_person_id' => '174388',
                        'created_by_firstname' => 'Kailey',
                        'created_by_lastname' => 'Moss',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '6'
                    ],
                    4 => [
                        'id' => '1223',
                        'name' => 'F15_Survey_Outreach',
                        'description' => 'Students who expressed intent to leave, homesickness, or academic struggles and who should have staff performing outreach.',
                        'created_at' => '2015-10-02 16:39:39',
                        'modified_at' => '2015-10-02 16:39:39',
                        'created_by_person_id' => '174388',
                        'created_by_firstname' => 'Kailey',
                        'created_by_lastname' => 'Moss',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '175'
                    ]
                ]],
                //Pagination check for Static lists created by person 174388
                [174388, 92, 2, [
                    0 => [
                        'id' => '3414',
                        'name' => '2016 Spring Very High Priority',
                        'description' => 'Students from midterm deficiencies with a "Very High" risk',
                        'created_at' => '2016-03-08 21:42:58',
                        'modified_at' => '2016-03-08 21:42:58',
                        'created_by_person_id' => '194367',
                        'created_by_firstname' => 'Emmalyn',
                        'created_by_lastname' => 'Le',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '0'
                    ],
                    1 => [
                        'id' => '2429',
                        'name' => 'Registration Attrition Profile F15 - S16',
                        'description' => 'FTIC students fitting the registered but higher risk of attrition profile between fall and spring.',
                        'created_at' => '2016-01-06 12:45:27',
                        'modified_at' => '2016-01-06 12:45:27',
                        'created_by_person_id' => '174388',
                        'created_by_firstname' => 'Kailey',
                        'created_by_lastname' => 'Moss',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '23'
                    ]
                ]],
                //No student added in Static lists created by person 174388
                [174388, 92, 1, [
                    0 => [
                        'id' => '3414',
                        'name' => '2016 Spring Very High Priority',
                        'description' => 'Students from midterm deficiencies with a "Very High" risk',
                        'created_at' => '2016-03-08 21:42:58',
                        'modified_at' => '2016-03-08 21:42:58',
                        'created_by_person_id' => '194367',
                        'created_by_firstname' => 'Emmalyn',
                        'created_by_lastname' => 'Le',
                        'modified_by_person_id' => null,
                        'modified_by_firstname' => null,
                        'modified_by_lastname' => null,
                        'student_count' => '0'
                    ]
                ]],
                //No static lists created by person 4551172
                [4551172, 74, 10, []],
                // Duplicate student count in student_count key
                [4893792, 8, 2, [
                    0 => [
                        'id' => '3972',
                        'name' => 'COE Connection',
                        'description' => '',
                        'created_at' => '2016-06-15 20:11:04',
                        'modified_at' => '2016-06-15 20:11:04',
                        'created_by_person_id' => '4893792',
                        'created_by_firstname' => 'Leila',
                        'created_by_lastname' => 'Cantu',
                        'modified_by_person_id' => '',
                        'modified_by_firstname' => '',
                        'modified_by_lastname' => '',
                        'student_count' => '25' // duplicates being counted it will be 82
                    ],
                    1 => [
                        'id' => '3924',
                        'name' => 'Additional Resources Requested',
                        'description' => 'All Levels or Risk, CLAS only, Freshman and Sophomores, All Cohorts, Indicated on survey wanting more resources',
                        'created_at' => '2016-05-23 14:34:42',
                        'modified_at' => '2016-05-23 14:34:42',
                        'created_by_person_id' => '4893792',
                        'created_by_firstname' => 'Leila',
                        'created_by_lastname' => 'Cantu',
                        'modified_by_person_id' => '',
                        'modified_by_firstname' => '',
                        'modified_by_lastname' => '',
                        'student_count' => '335' // duplicates being counted it will be 1013
                    ]
                ]]
            ]]
        );
    }

    public function testGetCountOfStaticListsForFaculty()
    {
        $this->markTestSkipped("To be fixed as a part of ESPRJ-14307");

        $this->specify('getCountOfStaticListsForFaculty', function ($faultyId, $organizationId, $expectedResult) {

            $staticListCount = $this->orgStaticListRepository->getCountOfStaticListsForFaculty($faultyId, $organizationId);
            //checking for count of static list id
            verify($staticListCount)->equals($expectedResult);

        }, ['examples' => [
                // 5 static list created by faculty 174388
                [174388, 120, 5],
                // 1 static list created by faculty 4551171
                [4551171, 182, 1],
                // 0 static list created by faculty 4551172
                [4551172, 182, 0]

            ]]
        );
    }

}
