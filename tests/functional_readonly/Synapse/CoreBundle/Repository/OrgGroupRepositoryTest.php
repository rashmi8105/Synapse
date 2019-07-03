<?php

class OrgGroupRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OrgGroupRepository
     */
    private $orgGroupRepository;

    /**
     * @var int
     */
    private $orgId = 203;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgGroupRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroup');
    }

    public function testGetGroupsWithPathsForOrganization()
    {
        $this->specify("Verify the functionality of the method getGroupsWithPathsForOrganization", function ($expectedGids, $totalExpectedGroupCount) {
            $results = $this->orgGroupRepository->getGroupsWithPathsForOrganization($this->orgId);
            verify(count($results))->equals($totalExpectedGroupCount);
            foreach ($expectedGids as $gid) {
                verify($results)->contains($gid);
            }
        }, ["examples" =>
            [
                [[
                    ["gid" => 369206, "Group_Name" => "All Students", "Group_ID" => "ALLSTUDENTS", "Parent_Group_ID" => null, "FullPathGroupIDs" => "ALLSTUDENTS", "FullPathNames" => "All Students"],
                    ["gid" => 370655, "Group_Name" => "Athletics", "Group_ID" => "NCAA", "Parent_Group_ID" => null, "FullPathGroupIDs" => "NCAA", "FullPathNames" => "Athletics"],
                    ["gid" => 370660, "Group_Name" => "Outdoor Track and Field", "Group_ID" => "OTF", "Parent_Group_ID" => "NCAA", "FullPathGroupIDs" => "NCAA | OTF", "FullPathNames" => "Athletics | Outdoor Track and Field"],
                    ["gid" => 370656, "Group_Name" => "Football", "Group_ID" => "FB", "Parent_Group_ID" => "NCAA", "FullPathGroupIDs" => "NCAA | FB", "FullPathNames" => "Athletics | Football"],
                    ["gid" => 370659, "Group_Name" => "Indoor Track and Field", "Group_ID" => "ITF", "Parent_Group_ID" => "NCAA", "FullPathGroupIDs" => "NCAA | ITF", "FullPathNames" => "Athletics | Indoor Track and Field"],
                    ["gid" => 370657, "Group_Name" => "Softball", "Group_ID" => "SB", "Parent_Group_ID" => "NCAA", "FullPathGroupIDs" => "NCAA | SB", "FullPathNames" => "Athletics | Softball"],
                    ["gid" => 370658, "Group_Name" => "Cross Country", "Group_ID" => "XC", "Parent_Group_ID" => "NCAA", "FullPathGroupIDs" => "NCAA | XC", "FullPathNames" => "Athletics | Cross Country"],
                    ["gid" => 370654, "Group_Name" => "Off Campus", "Group_ID" => "OFF", "Parent_Group_ID" => null, "FullPathGroupIDs" => "OFF", "FullPathNames" => "Off Campus"],
                    ["gid" => 370628, "Group_Name" => "Residence Life", "Group_ID" => "RL", "Parent_Group_ID" => null, "FullPathGroupIDs" => "RL", "FullPathNames" => "Residence Life"],
                    ["gid" => 370629, "Group_Name" => "East Side", "Group_ID" => "RLES", "Parent_Group_ID" => "RL", "FullPathGroupIDs" => "RL | RLES", "FullPathNames" => "Residence Life | East Side"],
                    ["gid" => 370631, "Group_Name" => "Bolton Hall", "Group_ID" => "BH", "Parent_Group_ID" => "RLES", "FullPathGroupIDs" => "RL | RLES | BH", "FullPathNames" => "Residence Life | East Side | Bolton Hall"],
                    ["gid" => 370638, "Group_Name" => "Bolton 1", "Group_ID" => "BH1", "Parent_Group_ID" => "BH", "FullPathGroupIDs" => "RL | RLES | BH | BH1", "FullPathNames" => "Residence Life | East Side | Bolton Hall | Bolton 1"],
                ], 34]
            ]
        ]);
    }

    public function testFetchGroupInfo()
    {
        $this->specify("Verify the functionality of the method fetchGroupInfo", function ($organizationId, $groupId, $expectedName) {

            $results = $this->orgGroupRepository->fetchGroupInfo($organizationId, $groupId);
            verify($results['group_id'])->notEmpty();
            verify($results['group_id'])->equals($groupId);
            verify($results['group_name'])->equals($expectedName);

        }, ["examples" =>
            [
                [203, 369206, 'All Students'],
                [203, 370655, 'Athletics'],
                [203, 370638, 'Bolton 1']
            ]
        ]);
    }

    public function testFetchListOfGroups()
    {

        $this->specify("Verify the functionality of the method fetchListOfGroups", function ($expectedResultsSize, $expectedgroupIdArr, $rootOnly) {
            $results = $this->orgGroupRepository->fetchListOfGroups($this->orgId, $rootOnly);
            verify(count($results))->equals($expectedResultsSize);

            foreach ($expectedgroupIdArr as $groups) {
                verify($results)->contains($groups);
            }
        }, ["examples" =>
            [
                [4, [
                    [
                        'group_id' => 369206,
                        'external_id' => "ALLSTUDENTS",
                        'group_name' => "All Students",
                        'created_at' => '2016-01-26 15:02:05',
                        'modified_at' => '2016-01-26 15:02:05',
                        'parent_id' => '',
                        'student_count' => 1000,
                        'staff_count' => 2
                    ],
                    [
                        'group_id' => 370628,
                        'external_id' => "RL",
                        'group_name' => "Residence Life",
                        'created_at' => '2016-04-18 17:34:23',
                        'modified_at' => '2016-04-18 17:34:23',
                        'parent_id' => '',
                        'student_count' => 0,
                        'staff_count' => 1
                    ],
                    [
                        'group_id' => 370654,
                        'external_id' => "OFF",
                        'group_name' => "Off Campus",
                        'created_at' => '2016-04-18 18:06:27',
                        'modified_at' => '2016-04-18 18:06:27',
                        'parent_id' => '',
                        'student_count' => 200,
                        'staff_count' => 0
                    ],
                    [
                        'group_id' => 370655,
                        'external_id' => "NCAA",
                        'group_name' => "Athletics",
                        'created_at' => '2016-04-18 18:12:47',
                        'modified_at' => '2016-04-18 18:12:47',
                        'parent_id' => '',
                        'student_count' => 0,
                        'staff_count' => 1
                    ]], true],
                [34, [
                    [
                        'group_id' => 369206,
                        'external_id' => "ALLSTUDENTS",
                        'group_name' => "All Students",
                        'created_at' => '2016-01-26 15:02:05',
                        'modified_at' => '2016-01-26 15:02:05',
                        'parent_id' => '',
                        'student_count' => 1000,
                        'staff_count' => 2
                    ],
                    [
                        'group_id' => 370628,
                        'external_id' => "RL",
                        'group_name' => "Residence Life",
                        'created_at' => '2016-04-18 17:34:23',
                        'modified_at' => '2016-04-18 17:34:23',
                        'parent_id' => '',
                        'student_count' => 0,
                        'staff_count' => 1
                    ], [
                        'group_id' => 370629,
                        'external_id' => "RLES",
                        'group_name' => "East Side",
                        'created_at' => '2016-04-18 17:34:48',
                        'modified_at' => '2016-04-18 17:34:48',
                        'parent_id' => '370628',
                        'student_count' => 0,
                        'staff_count' => 1
                    ]
                ], false]
            ]
        ]);
    }

    /**
     *
     * This will test the convert external id or group name
     * to a system id. This will test both group name
     * external id and a failed attempt
     */
    public function testConvertExternalIdOrGroupNameToId()
    {
        $this->specify("Test to convert an external id or group name to an id ", function ($externalIdOrGroupName, $orgId, $isExternalIdOrGroupName, $expectedResults) {
            $returnValues = $this->orgGroupRepository->convertExternalIdOrGroupNameToId($externalIdOrGroupName, $orgId, $isExternalIdOrGroupName);

            $this->assertEquals($returnValues, $expectedResults);

        }, ["examples" =>
            [//examples array
                //example 1
                [
                    // Group Name test
                    'Lannister 3',
                    203,
                    'groupname',
                    '370642'
                ],
                [
                    // Group Name with external id
                    'Lannister 3',
                    203,
                    'externalid',
                    array('error' => 'There are no groups with the external id Lannister 3')
                ],
                [
                    // Group Name test with both
                    'Lannister 3',
                    203,
                    'both',
                    '370642'
                ],
                [
                    // External id test
                    'RLEC',
                    203,
                    'externalid',
                    '370633'
                ],
                [
                    // External id with group name
                    'RLEC',
                    203,
                    'groupname',
                    array('error' => 'There are no groups with the group name RLEC')
                ],
                [
                    // External id test with both
                    'RLEC',
                    203,
                    'both',
                    '370633'
                ],
                [
                    // Incorrect id sent
                    'Lannister',
                    203,
                    'both',
                    array('error' => 'There are no groups with the external id or group name Lannister')
                ]
            ]
        ]);
    }

    /**
     *
     * This will convert an group id to the either
     * an external id or an group name. This should be
     * what the user sees.
     */
    public function testConvertIdToExternalIdOrGroupName()
    {
        $this->specify("Test to convert a system id to an external id or group name", function ($orgGroupId, $orgId, $expectedResults) {
            $returnValues = $this->orgGroupRepository->convertIdToExternalIdOrGroupName($orgGroupId, $orgId);
            //$this->assertEquals(count($returnValues), $countOfExpectedResults);

            // getting the results that are $returnValues are sending, the
            // $expected values are what the database are returning
            $this->assertEquals($returnValues, $expectedResults[0]['external_id']);

        }, ["examples" =>
            [//examples array
                //example 1
                [
                    370639,
                    203,
                    [
                        ['external_id' => 'BH2']
                    ]
                ],
                [
                    370645,
                    203,
                    [
                        ['external_id' => 'TT3']
                    ]
                ],
            ]
        ]);
    }

    /**
     *
     * this will test the Top Level Group Function.
     * This function will get all groups in an
     * organization that does not have an parent group
     * id or their parent group id is ALLSTUDENTS group
     */
    public function testGetTopLevelGroups()
    {
        $this->specify("Test to grab all top level groups in an organization ", function ($organizationId, $includeALLSTUDENTS, $countOfExpectedResults, $expectedResults) {
            $returnValues = $this->orgGroupRepository->getTopLevelGroups($organizationId, $includeALLSTUDENTS);

            // if nothing is returned: and that is what we expect pass
            if (empty($returnValues)) {
                $this->assertEquals($countOfExpectedResults, 0);
                $this->assertEquals($returnValues, $expectedResults);
            } else {

                // Does the number of rows equal the expected number of rows
                $this->assertEquals(count($returnValues), $countOfExpectedResults);

                // make sure that the headers returned in the array equal
                // the expected returned array headers
                $returnValuesKeys = array_keys($returnValues[0]);
                $expectedResultsKeys = array_keys($expectedResults[0]);
                $this->assertEquals($returnValuesKeys, $expectedResultsKeys);

                // I grabbed a random sample of students for the check
                // I am going to make sure that the random sample is within
                // the results returned.
                foreach ($expectedResults as $expectedResultRow) {
                    $this->assertContains($expectedResultRow, $returnValues);
                }
            }

        }, ["examples" =>
            [//examples array
                //example 1; include ALLSTUDENTS
                [
                    203,
                    true,
                    4,
                    [
                        ['group_name' => 'ALLSTUDENTS'],
                        ['group_name' => 'RL'],
                        ['group_name' => 'OFF'],
                        ['group_name' => 'NCAA'],
                    ]
                ],
                //example 2; do not include ALLSTUDENTS
                [
                    203,
                    false,
                    3,
                    [
                        ['group_name' => 'RL'],
                        ['group_name' => 'OFF'],
                        ['group_name' => 'NCAA'],
                    ]
                ],
            ]
        ]);
    }

    /**
     * This is going to test the getImmediateChildrenOfAllStudentsGroup
     * function which will get all immediate children of ALLSTUDENTS group
     */
    public function testGetImmediateChildrenOfAllStudentsGroup()
    {

        $this->specify("Test to grab all top level groups in an organization ", function ($organizationId, $countOfExpectedResults, $expectedResults) {
            $returnValues = $this->orgGroupRepository->getImmediateChildrenOfAllStudentsGroup($organizationId);

            // if nothing is returned: and that is what we expect pass
            if (empty($returnValues)) {
                $this->assertEquals($countOfExpectedResults, 0);
                $this->assertEquals($returnValues, $expectedResults);
            } else {

                // Does the number of rows equal the expected number of rows
                $this->assertEquals(count($returnValues), $countOfExpectedResults);

                // make sure that the headers returned in the array equal
                // the expected returned array headers
                $returnValuesKeys = array_keys($returnValues[0]);
                $expectedResultsKeys = array_keys($expectedResults[0]);
                $this->assertEquals($returnValuesKeys, $expectedResultsKeys);

                // I grabbed a random sample of students for the check
                // I am going to make sure that the random sample is within
                // the results returned.
                foreach ($expectedResults as $expectedResultRow) {
                    $this->assertContains($expectedResultRow, $returnValues);
                }
            }

        }, ["examples" =>
            [//examples array
                //example 1
                [
                    203,
                    0,
                    [
                    ]
                ],
            ]
        ]);
    }

    public function testFetchOrgGroupTotalCount()
    {
        $this->specify("Verify the functionality of the method fetchOrgGroupTotalCount", function ($organizationId, $searchText, $totalGroupCount) {

            $results = $this->orgGroupRepository->fetchOrgGroupTotalCount($organizationId, $searchText);

            verify($results['total_groups'])->equals($totalGroupCount);

        }, ["examples" =>
            [
                // Group count for the organization id   =  203
                [203, null, 34],
                // Group count for the organization id   =  214
                [214, null, 0],
                // Group count for the organization id   =  81
                [81, null, 622],
                // Group count for the organization id   =  182
                [182, null, 4],
                // Group count for the organization id   =  182  with search text "Test" , this would get all the group names with "test" in it
                [182, "Test", 3],
                // Group count for the organization id   =  182  with search text "EXID00370412" , this would get the group with external id "EXID00370412"
                [182, "EXID00370412", 1],
                // Group count for the organization id   =  119 with searchtext "FAM_Romo" (search text with wildcard(_) for mysql)
                [119, "FAM_Romo", 1],
                // Group count for the organization id   =  119 with searchtext "FAM Mentor_Martinez" (search text with wildcard(_) for mysql)
                [119, "FAM Mentor_Martinez", 1],
                // Group count for the organization id   =  119 with searchtext " _" (wildcard for mysql)
                [119, "_", 2],
                // Group count for the organization id   =  119 with searchtext " %" (wildcard for mysql)
                [119, "%", 0],
            ]
        ]);
    }

    public function testFetchGroupSummaryLastModifiedDate()
    {
        $this->specify("Verify the functionality of the method fetchGroupSummaryLastModifiedDate", function ($organizationId, $expectedLastModifiedDate) {

            $results = $this->orgGroupRepository->fetchGroupSummaryLastModifiedDate($organizationId);
            verify($results['last_updated'])->equals($expectedLastModifiedDate);

        }, ["examples" =>
            [
                [203, "2016-05-04 21:18:58"],
                [214, "0"],
                [81, "2016-05-28 07:16:16"]
            ]
        ]);
    }

    public function testGetGroupsMetaData()
    {
        $this->specify("Verify the functionality of the method GetGroupsMetaData", function ($organizationId, $searchText, $pageNumber, $recordsPerPage, $expectedResult) {
            $results = $this->orgGroupRepository->getGroupsMetaData($organizationId, $searchText, $pageNumber, $recordsPerPage);
            verify($results)->equals($expectedResult);
        }, ["examples" =>
            [
                // fetch all groups for the organization
                [182, null, null, null, [

                    [
                        'mapworks_internal_id' => 345437,
                        'group_name' => "All Students",
                        'external_id' => "ALLSTUDENTS"
                    ],
                    [
                        'mapworks_internal_id' => 346984,
                        'group_name' => "Test Group 00346984",
                        'external_id' => "EXID00346984"
                    ],
                    [
                        'mapworks_internal_id' => 370411,
                        'group_name' => "Test Group 00370411",
                        'external_id' => "EXID00370411"
                    ],
                    [
                        'mapworks_internal_id' => 370412,
                        'group_name' => "Test Group 00370412",
                        'external_id' => "EXID00370412"
                    ]
                ]

                ],
                // Searching for group  where group name contains "Test"
                [182, "Test", null, null, [

                    [
                        'mapworks_internal_id' => 346984,
                        'group_name' => "Test Group 00346984",
                        'external_id' => "EXID00346984"
                    ],
                    [
                        'mapworks_internal_id' => 370411,
                        'group_name' => "Test Group 00370411",
                        'external_id' => "EXID00370411"
                    ],
                    [
                        'mapworks_internal_id' => 370412,
                        'group_name' => "Test Group 00370412",
                        'external_id' => "EXID00370412"
                    ]
                ]

                ],
                // Searching for group  where externalId   contains "EXID00370412"
                [182, "EXID00370412", null, null, [


                    [
                        'mapworks_internal_id' => 370412,
                        'group_name' => "Test Group 00370412",
                        'external_id' => "EXID00370412"
                    ]
                ]

                ],
                // Testing pagination , page 1 eith records perpage =1 , will returns the 1st out of 3 record
                [182, "Test", 0, 1, [

                    [
                        'mapworks_internal_id' => 346984,
                        'group_name' => "Test Group 00346984",
                        'external_id' => "EXID00346984"
                    ]

                ]

                ],
                // Testing pagination , page 1 with records per page =1 , will returns the 2nt out of 3 record
                [182, "Test", 1, 1, [
                    [
                        'mapworks_internal_id' => 370411,
                        'group_name' => "Test Group 00370411",
                        'external_id' => "EXID00370411"
                    ]
                ]

                ],
            ]
        ]);
    }

}