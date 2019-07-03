<?php

/**
 * Class OrgGroupFacultyRepositoryTest
 */

use Codeception\TestCase\Test;

class OrgGroupFacultyRepositoryTest extends Test
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
     * @var \Synapse\CoreBundle\Repository\OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var int
     */
    private $orgId = 203;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
    }

    public function testGetGroupStaffList()
    {
        $this->specify("Verify the functionality of the method getGroupStaffList", function($groupIdIn, $organizationIdIn, $expectedResultsSize, $expectedIds){

            $results = $this->orgGroupFacultyRepository->getGroupStaffList($organizationIdIn, $groupIdIn);

            verify(count($results))->equals($expectedResultsSize);

            for($i = 0; $i < count($expectedIds); $i++){
                verify($results[$i]['staff_id'])->notEmpty();
                verify($results[$i]['staff_id'])->equals($expectedIds[$i]);
            }
        }, ["examples"=>
            [
                [370421,214,4, [4891025, 4891259, 4891333, 4891232]],
                [370426,214,0, []],
                [370427,214,1, [4891234]],
                [370428,214,0, []],
                [370429,214,1, [4891291]],
                [370452,214,2, [4891265, 4891267]],
                [370481,214,3, [4891249, 4891271, 4891270]],
                [370495,214,1, [4891280]]
            ]
        ]);
    }

    public function testGetPermissionsByFacultyStudent()
    {
        $this->specify("Verify the functionality of the method getPermissionsByFacultyStudent", function($facultyId, $studId, $expectedResultsSize, $expectedIds){

            $results = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($facultyId, $studId);

            verify(count($results))->equals($expectedResultsSize);

            for($i = 0; $i < count($expectedIds); $i++){
                verify($results[$i]['org_permissionset_id'])->notEmpty();
                verify($results[$i]['org_permissionset_id'])->equals($expectedIds[$i]);
            }
        }, ["examples"=>
            [
                [4878751,4878808,1,[1411]],
                [4891668,4878810,1,[1411]]
                
            ]
        ]);
    }


    public function testGetGroupFacultyList()
    {
        $this->specify("Verify the functionality of the method getGroupFacultyList", function($orgId,$expectedCount,$expectedResultSet){
            $results = $this->orgGroupFacultyRepository->getGroupFacultyList($orgId);
            verify(count($results))->equals($expectedCount);
            foreach($expectedResultSet as $expectedResult) {
                verify($results)->contains($expectedResult);
            }
        }, ["examples"=>
            [
                [
                    $this->orgId,
                    19,
                    [
                        [
                            'ExternalId' => 4878751,
                            'Firstname' => 'Maximus',
                            'Lastname' => 'Lowery',
                            'PrimaryEmail' => 'MapworksBetaUser04878751@mailinator.com',
                            'FullPathNames' => '',
                            'FullPathGroupIDs' => '',
                            'GroupName' => 'All Students',
                            'GroupId' => 'ALLSTUDENTS',
                            'PermissionSet' => 'All',
                            'Invisible' => 0
                        ],
                        [
                            'ExternalId' => 4883174,
                            'Firstname' => 'Niko',
                            'Lastname' => 'Herrera',
                            'PrimaryEmail' => 'MapworksBetaUser04883174@mailinator.com',
                            'FullPathNames' => 'Residence Life',
                            'FullPathGroupIDs' => 'RL',
                            'GroupName' => 'West Side',
                            'GroupId' => 'RLWS',
                            'PermissionSet' => 'All',
                            'Invisible' => 0,
                        ],
                        [
                            'ExternalId' => 4883150,
                            'Firstname' => 'Jacoby',
                            'Lastname' => 'Gordon',
                            'PrimaryEmail' => 'MapworksBetaUser04883150@mailinator.com',
                            'FullPathNames' => 'Residence Life',
                            'FullPathGroupIDs' => 'RL',
                            'GroupName' => 'East Side',
                            'GroupId' => 'RLES',
                            'PermissionSet' => 'All',
                            'Invisible' => 0,
                        ],
                        [
                            'ExternalId' => 4883173,
                            'Firstname' => 'Dominik',
                            'Lastname' => 'Nichols',
                            'PrimaryEmail' => 'MapworksBetaUser04883173@mailinator.com',
                            'FullPathNames' => 'Residence Life | West Side | Stark Hall',
                            'FullPathGroupIDs' => 'RL | RLWS | SH',
                            'GroupName' => 'Stark 1',
                            'GroupId' => 'SH1',
                            'PermissionSet' => 'CourseOnly',
                            'Invisible' => 0,
                        ],
                        [
                            'ExternalId' => 4883165,
                            'Firstname' => 'Lucian',
                            'Lastname' => 'Warren',
                            'PrimaryEmail' => 'MapworksBetaUser04883165@mailinator.com',
                            'FullPathNames' => 'Residence Life | East Side | East Complex | Frey Tower',
                            'FullPathGroupIDs' => 'RL | RLES | RLEC | FT',
                            'GroupName' => 'Frey 1',
                            'GroupId' => 'FT1',
                            'PermissionSet' => 'CourseOnly',
                            'Invisible' => 0,
                        ]
                    ]
                ]
            ]
        ]);
    }
}