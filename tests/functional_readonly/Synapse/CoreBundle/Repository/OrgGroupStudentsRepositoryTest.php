<?php
use Codeception\TestCase\Test;

class OrgGroupStudentRepositoryTest extends Test
{

    use Codeception\Specify;

    private $crossCountryStudents = [4879305, 4878822, 4879609, 4879784, 4879369, 4878886, 4879848, 4878950, 4879098, 4879384];

    private $indoorTrackStudents = [4879305, 4878822, 4879609, 4879784, 4879369, 4878886, 4879848, 4878950, 4879098, 4879384, 4879145, 4879448, 4879624, 4879863, 4879209, 4879513, 4879688, 4879273, 4879577, 4879752, 4879337, 4878854, 4879816, 4878918, 4878982, 4879130, 4879168, 4879471, 4879647, 4879886, 4879232, 4879536, 4879711, 4879296, 4878813, 4879600, 4879775, 4879360, 4878877, 4879839, 4878941, 4879005, 4879375, 4879136, 4879439, 4879615, 4879854, 4879200, 4879504, 4879679];

    private $outdoorTrackStudents = [4879305, 4878822, 4879609, 4879784, 4879369, 4878886, 4879848, 4878950, 4879098, 4879384, 4879145, 4879448, 4879624, 4879863, 4879209, 4879513, 4879688, 4879273, 4879577, 4879752, 4879337, 4878854, 4879816, 4878918, 4878982, 4879130, 4879168, 4879471, 4879647, 4879886, 4879232, 4879536, 4879711, 4879296, 4878813, 4879600, 4879775, 4879360, 4878877, 4879839, 4879264, 4879568, 4879743, 4879328, 4878845, 4879807, 4878909, 4878973, 4879121, 4879407, 4879591, 4879766, 4879351, 4878868, 4879830, 4878932, 4878996, 4879430, 4879191, 4879495];

    private $stark1Students = [4879138, 4879441, 4879617, 4879856, 4879202, 4879506, 4879681, 4879266, 4879570, 4879745, 4879330, 4878847, 4879809, 4878911, 4878975, 4879123, 4879593, 4879768, 4879353, 4878870, 4879832, 4878934, 4878998, 4879432, 4879193, 4879497, 4879672, 4879257, 4879561, 4879736, 4879321, 4878838, 4879800, 4878902, 4878966, 4879114, 4879400, 4879161, 4879464, 4879640, 4879879, 4879225, 4879529, 4879704, 4879289, 4878989, 4879423, 4879184, 4879487, 4879663];

    private $stark2Students = [4879248, 4879552, 4879727, 4879312, 4878829, 4879791, 4878893, 4878957, 4879105, 4879391, 4879152, 4879455, 4879631, 4879870, 4879216, 4879520, 4879695, 4879280, 4879584, 4879759, 4879344, 4878861, 4879823, 4878925, 4879414, 4879175, 4879478, 4879654, 4879239, 4879543, 4879718, 4879303, 4878820, 4879607, 4879782, 4879367, 4878884, 4879846, 4878948, 4879096, 4879382, 4879143, 4879446, 4879622, 4879861, 4879207, 4879511, 4879686, 4879271, 4879575];


    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgGroupStudentsRepository
     */
    private $orgGroupStudentsRepository;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgGroupStudentsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgGroupStudents");
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPermissionset");
    }

    public function testGetNonArchivedStudentsByGroups()
    {
        $this->specify("Verify the functionality of the method getNonArchivedStudentsByGroups", function ($orgGroupId, $academicYearId, $studentCount, $expectedIds) {
            $results = $this->orgGroupStudentsRepository->getNonArchivedStudentsByGroups($orgGroupId, $academicYearId);
            $expectedStudentCount = 0;
            foreach ($expectedIds as $studentId) {
                verify($results)->contains($studentId);
                $expectedStudentCount++;
            }
            verify($studentCount)->equals($expectedStudentCount);
        }, ["examples" =>
            [
                [[369206], 158, 2, [4878841, 4878905]],
                [[365443], 97, 4, [989864, 989896, 990017, 990103]],
                [[365446], 97, 18, [989545, 989552, 989512, 989668, 989616, 989782, 989575, 989725, 989735, 989674, 989846, 989970, 989897, 990127, 990056, 4774745, 4774322, 4774636]],
                [[365425], 97, 5, [989480, 989568, 989585, 989637, 990092]]
            ]
        ]);
    }

    public function testGetStudentsByGroups()
    {
        $this->specify("Verify the functionality of the method getStudentsByGroups", function ($orgGroupIds, $expectedResult) {

            $result = $this->orgGroupStudentsRepository->getStudentsByGroups($orgGroupIds);

            verify(array_diff($result, $expectedResult))->equals([]);
            verify(array_diff($expectedResult, $result))->equals([]);
            verify(count($result))->equals(count($expectedResult));

        }, ["examples" =>
            [
                // Example 1: Students directly in the cross country group
                [[370658], $this->crossCountryStudents],
                // Example 2: Students in cross country, indoor track, or outdoor track (some overlap)
                [[370658, 370659, 370660], array_unique(array_merge($this->crossCountryStudents, $this->indoorTrackStudents, $this->outdoorTrackStudents))],
                // Example 3: Students in Stark Hall (There are no students directly in Stark Hall; they're in the subgroups Stark 1 and Stark 2.)
                [[370636], array_unique(array_merge($this->stark1Students, $this->stark2Students))],
            ]
        ]);
    }


    public function testGetStudentGroupsDetails()
    {
        $this->specify("Verify the functionality of the method GetStudentGroupsDetails", function ($studentId, $orgId, $expectedGroupIdsArray, $expectedCount) {

            $results = $this->orgGroupStudentsRepository->getStudentGroupsDetails($studentId, $orgId);
            $countExpectedGroupIdCheck = 0;
            foreach ($results as $result) {
                if (in_array($result['group_id'], $expectedGroupIdsArray)) {
                    $countExpectedGroupIdCheck++;
                }
            }
            verify($countExpectedGroupIdCheck)->equals(count($expectedGroupIdsArray));
            verify(count($results))->equals($expectedCount);

        }, ["examples" =>
            [
                [4878808, 203, [369206, 370638, 370656], 3],
                [4878822, 203, [370660, 370659, 370658, 370639, 369206], 5],
                [4878813, 203, [370660, 370659, 370640, 369206], 4],
                [4878886, 203, [370660, 370659, 370658, 370639, 369206], 5]
            ]
        ]);


    }

    public function testGetGroupStudentCountOrg()
    {
        $this->specify("Verify the functionality of the method getGroupStudentCountOrg", function ($orgId, $expectedCount) {
            $results = $this->orgGroupStudentsRepository->getGroupStudentCountOrg($orgId);
            verify($results)->equals($expectedCount);
        }, ["examples" =>
            [
                [203, 1000],
                [214, 0],
                [81, 10792] // with All Students its 33622
            ]
        ]);
    }

    public function testListExternalIdsForStudentsInGroup()
    {
        $this->specify("Verify the functionality of the method listExternalIdsForStudentsInGroup", function ($organizationId, $groupId, $includeMetadata, $expectedResult) {
            $studentIds = $this->orgGroupStudentsRepository->listExternalIdsForStudentsInGroup($organizationId, $groupId, $includeMetadata);
            verify($studentIds)->equals($expectedResult);
        }, ["examples" =>
            [
                // checks subset of students present
                [
                    117, 333124, false,
                    [
                        [
                            'external_id' => '669620'
                        ],
                        [
                            'external_id' => '4884657'
                        ],
                        [
                            'external_id' => '4884662'
                        ]
                    ]
                ],
                // checks subset of students present
                [
                    156, 333244, false,
                    [
                        [
                            'external_id' => '4649298'
                        ],
                        [
                            'external_id' => '4649574'
                        ],
                        [
                            'external_id' => '4650220'
                        ],
                    ]
                ],
                /*
                 * Modified the test case below, the GroupStudentsListByGroup method was using the group hierarchy,
                 * for which the  below test case was passing, as now the function does not use hierarchy,
                 * it was failing as the parent group does not have any child associated to it directly , modified it check for 0 count
                 */
                [
                    203, 370655, false, []
                ],
                // checks the count for  group id = 51 and organization id =2
                [
                    2, 51, true,
                    [
                        [
                            'external_id' => 81,
                            'first_name' => "Emily",
                            'last_name' => "Torres",
                            'primary_email' => "34938903812@mailinator.com"
                        ],
                        [
                            'external_id' => 88,
                            'first_name' => "Spencer",
                            'last_name' => "Wessel",
                            'primary_email' => "34938903882@mailinator.com"
                        ]
                    ]
                ],
                // checks the count for  group id = 51 and organization id =2 including metadata
                [
                    2, 51, true,
                    [

                        [
                            'external_id' => 81,
                            'first_name' => "Emily",
                            'last_name' => "Torres",
                            'primary_email' => "34938903812@mailinator.com"
                        ],
                        [
                            'external_id' => 88,
                            'first_name' => "Spencer",
                            'last_name' => "Wessel",
                            'primary_email' => "34938903882@mailinator.com"
                        ]

                    ]
                ]
            ]

        ]);
    }


    public function testDeleteBulkStudentEnrolledGroups()
    {

    }
    public function testGetAllStudentsForFeature()
    {
        $this->specify("Verify the functionality of the method getAllStudentsForFeature", function ($orgId, $facultyId, $expectedIdsArray, $expectedCount) {

            $results = $this->orgGroupStudentsRepository->getAllStudentsForFeature($orgId, $facultyId, 1);
            if (!empty($expectedIdsArray)) {
                $countExpectedStudentIdCheck = 0;
                foreach ($results as $result) {
                    if (in_array($result['student_id'], $expectedIdsArray)) {
                        $countExpectedStudentIdCheck++;
                    }
                }
                verify($countExpectedStudentIdCheck)->equals(count($expectedIdsArray));
            } else {
                verify(count($results))->equals($expectedCount);
            }
        }, ["examples" =>
            [
                [203, 4878750, [], 1000],
                [203, 4878750, [4878808, 4878809, 4878810, 4878811], 4]
            ]
        ]);
    }

    public function testCountStudentsForGroup()
    {

        $this->specify("Test to get the count of non archived stuident for a  group in a given academic year", function ($orgId, $groupId, $expectedStudentCount) {

            $results = $this->orgGroupStudentsRepository->countStudentsForGroup($orgId, $groupId);
            verify($results)->equals($expectedStudentCount);

        }, [
                "examples" =>
                    [
                        [203, 370659, 50],
                        [203, 369206, 1000],
                        [203, 370654, 200]
                    ]
            ]
        );
    }


    public function testGetGroupStudentCount()
    {
        $this->markTestSkipped('getGroupStudentCount  method does not exists');
        $this->specify("Verify the functionality of the method getGroupStudentCount", function ($expectedResultsSize, $expectedRes, $orgId, $groupId) {

            $results = $this->orgGroupStudentsRepository->getGroupStudentCount($orgId, $groupId);
            verify(count($results))->equals($expectedResultsSize);
            verify($results)->notEmpty();
            verify($results)->equals($expectedRes);
        }, ["examples" =>
            [
                [1, 1000, 203, 369206]
            ]
        ]);
    }
}