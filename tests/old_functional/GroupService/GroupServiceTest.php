<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\OrgGroupDto;

class GroupServiceTest extends \Codeception\TestCase\Test
{
    /**
     * @var UnitTester
     */
    protected $tester;
    
    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     * @var \Synapse\CoreBundle\Service\OrganizationService
     */
    private $groupService;
    
    private $org = 1;
     
    private $invalidorg = -2;
    
    private $userId = 1;
    
    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->groupService = $this->container
        ->get('group_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->userId);
    }
    public function testCreateGroup()
    {
        $this->initializeRbac();
        $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $this->assertEquals('Functional_Test_Group', $group->getGroupName());
        $this->assertEquals($this->org, $group->getOrganizationId());
        $this->assertGreaterThan(0, $group->getGroupId());
       
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateGroupWithInvlidParent()
    {
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setParentGroupId(1111111);
        $group = $this->groupService->createGroup($groupDto);
        
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateGroupWithDuplicateName()
    {
        //$this->markTestSkipped("Failed");
        $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $group = $this->groupService->createGroup($groupDto);
    
    }
    
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    // commented since duplicate allowed
    /*public function testEditGroupWithDuplicateName()
    {
        //$this->markTestSkipped("Failed");
        $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $groupId  = $groupDto->getGroupId();
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second Group");
        $group = $this->groupService->createGroup($groupDto);
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second Group");
        $groupDto->setGroupId($groupId);
        $group = $this->groupService->editGroup($groupDto);
        
    
    }*/
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditGroupWithInvalidGroupId()
    {
        $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $groupId  = $group->getGroupId();
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second Group");
        $group = $this->groupService->createGroup($groupDto);
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second Group");
        $groupDto->setGroupId(-1);
        $group = $this->groupService->editGroup($groupDto);
    
    
    }
    
    public function testeditGroupWithRemoveStaff()
    {
        $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $staff = $group->getStaffList(); 
        
        $staff[0]['staff_is_remove'] = 1;
       
        $staff[1]['staff_is_remove'] = 0;
        
        
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setStaffList($staff);
        $groupDto->setGroupId($group->getGroupId());
       
        $group = $this->groupService->editGroup($groupDto);
       $this->assertEquals('Functional_Test_Group', $group->getGroupName());
       $this->assertEquals($this->org, $group->getorganizationId());
       $this->assertEquals($groupDto->getGroupId(), $group->getGroupId());
       
    }
    
    
    public function testeditGroupWithAddStaff()
    {
         $groupDto = $this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $staff = $group->getStaffList();
        $staff[0]['staff_is_remove'] = 0;
        $staff[1]['staff_is_remove'] = 0;
        $staff[2]['staff_id'] = 3;
        $staff[2]['staff_permissionset_id'] = 1;
        $staff[2]['staff_is_invisible'] = 0;
        $staff[2]['staff_is_remove'] = 0;
       
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setStaffList($staff);
        $groupDto->setGroupId($group->getGroupId());
        $group = $this->groupService->editGroup($groupDto);
       $this->assertEquals('Functional_Test_Group', $group->getGroupName());
       $this->assertEquals($this->org, $group->getOrganizationId());
       $this->assertEquals($groupDto->getGroupId(), $group->getGroupId());
       
    }
    
    
    public function testGetGroupById()
    {
        $groupDto=$this->createOrgGroupDto();
        $group = $this->groupService->createGroup($groupDto);
        $groupFound = $this->groupService->getGroupById($group->getOrganizationId(),$group->getGroupId());
        $this->assertEquals('Functional_Test_Group',$groupFound['group_name']);
        $this->assertEquals($this->org, $groupFound['organization_id']);
        $this->assertEquals($group->getGroupId(), $groupFound['group_id']);
        $this->assertArrayHasKey('parent_group_id', $groupFound);
        $this->assertArrayHasKey('parent_group_name', $groupFound);
        $this->assertArrayHasKey('subgroups_student_count', $groupFound);
        $this->assertArrayHasKey('subgroups_staff_count', $groupFound);
        $this->assertArrayHasKey('students_count', $groupFound);
        $this->assertArrayHasKey('staff_list', $groupFound);
        $this->assertArrayHasKey('staff_lastname', $groupFound['staff_list'][0]);
        $this->assertArrayHasKey('staff_is_invisible', $groupFound['staff_list'][0]);
        $this->assertArrayHasKey('staff_permissionset_id', $groupFound['staff_list'][0]);
        $this->assertArrayHasKey('staff_permissionset_name', $groupFound['staff_list'][0]);
    }
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteGroup()
    {
        $groupDto = $this->createOrgGroupDto();
        $group=$this->groupService->createGroup($groupDto);
        $masterId = $group->getGroupId();
        $groupDto = $this->createOrgGroupDto($masterId,"Sample Sub Group");
        $group=$this->groupService->createGroup($groupDto);
        $deletedGroup=$this->groupService->deleteGroup($groupDto->getOrganizationId(),$masterId);
        $listGroup=$this->groupService->getGroupById($groupDto->getOrganizationId(),$masterId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteGroupWithInvalidId()
    {
        $groupDto = $this->createOrgGroupDto();
        $group=$this->groupService->createGroup($groupDto);
        $masterId = $group->getGroupId();
        $groupDto = $this->createOrgGroupDto($masterId,"Sample Sub Group");
        $group=$this->groupService->createGroup($groupDto);
        $deletedGroup=$this->groupService->deleteGroup($groupDto->getOrganizationId(),-1);
       
         
    
    }
    
    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function testDeleteGroupWithInvalidOrgId()
    {
        $groupDto = $this->createOrgGroupDto();
        $group=$this->groupService->createGroup($groupDto);
        $masterId = $group->getGroupId();
        $groupDto = $this->createOrgGroupDto($masterId,"Sample Sub Group");
        $group=$this->groupService->createGroup($groupDto);
        $deletedGroup=$this->groupService->deleteGroup($this->invalidorg,$masterId);
         
         
    
    }
    public function testListGroup()
    {
        //$this->markTestSkipped("Failed");
        $this->initializeRbac();
        $groupDto = $this->createOrgGroupDto();
        $group=$this->groupService->createGroup($groupDto);
        
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second_Group");
        $group=$this->groupService->createGroup($groupDto);
        
        $listGroup=$this->groupService->getGroupList($groupDto->getOrganizationId());
        $this->assertInternalType('array', $listGroup);
        $this->assertEquals($this->org, $listGroup['organization_id']);
        $this->assertArrayHasKey('total_groups', $listGroup);
        $this->assertArrayHasKey('groups', $listGroup);
        $this->assertArrayHasKey('group_id', $listGroup['groups'][0]);
        $this->assertArrayHasKey('parent_id', $listGroup['groups'][0]);
        //$this->assertArrayHasKey('parent_group_name', $listGroup['groups'][0]);
        //$this->assertArrayHasKey('subgroups_staff_count', $listGroup['groups'][0]);
        //$this->assertArrayHasKey('subgroups_student_count', $listGroup['groups'][0]);
        
    
    }
    
    public function testListGroupWithChilds()
    {        
        $groupDto = $this->createOrgGroupDto();
        $group=$this->groupService->createGroup($groupDto);
    
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second_Group");
        $group=$this->groupService->createGroup($groupDto);
        
        $groupId = $group->getGroupId();
        
        $groupDto = $this->createOrgGroupDto();
        $groupDto->setGroupName("Second_Group-child-1");
        $groupDto->setParentGroupId($groupId);
        $group=$this->groupService->createGroup($groupDto);
    
        $listGroup=$this->groupService->getGroupList($groupDto->getOrganizationId());
        $this->assertInternalType('array', $listGroup);		
        $this->assertEquals($this->org, $listGroup['organization_id']);
        $this->assertArrayHasKey('total_groups', $listGroup);
        $this->assertArrayHasKey('groups', $listGroup);
        $this->assertArrayHasKey('group_id', $listGroup['groups'][0]);
        $this->assertArrayHasKey('parent_id', $listGroup['groups'][0]);
        $this->assertArrayHasKey('group_name', $listGroup['groups'][0]);
        $this->assertArrayHasKey('staff_count', $listGroup['groups'][0]);
        $this->assertArrayHasKey('student_count', $listGroup['groups'][0]);
        
    
    }
    
    public function testGetGroupsSearch()
    {
        for($i = 0; $i<10;$i++)
        {
            $groupDto = $this->createOrgGroupDto(0,"Groups".$i.time());
            $group=$this->groupService->createGroup($groupDto);
        }
        $groupDto = $this->createOrgGroupDto(0,"Groups_Search_fun");
        $group=$this->groupService->createGroup($groupDto);
        $listGroup=$this->groupService->getGroupsSearch($groupDto->getOrganizationId(),null);
        //print_r($listGroup);die;        
        $this->assertArrayHasKey('parent_group_id', $listGroup[0]);
        $this->assertArrayHasKey('parent_group_name', $listGroup[0]);
        $this->assertArrayHasKey('parent_group_student_count', $listGroup[0]);
        $this->assertArrayHasKey('group_name', $listGroup[0]);
        $this->assertArrayHasKey('group_id', $listGroup[0]);
        $this->assertArrayHasKey('group_staff_count', $listGroup[0]);
        foreach ($listGroup as $lstGroup)
        {
            if ($lstGroup['group_id'] == $group->getGroupId())
            {
                $this->assertContains('Groups_Search_fun',$lstGroup);
            }
        }
        
    }
    private function createOrgGroupDto ($parent = 0,$name = "Functional_Test_Group",$type = "insert")
    {
        $groupDto = new OrgGroupDto();
        $groupDto->setOrganizationId($this->org);
        $groupDto->setParentGroupId($parent);
        $groupDto->setGroupName($name);
        $groupDto->setExternalId(rand(1,1000));
        $stafflist = array();
        
        $stafflist[0]['staff_id'] = 1;
        $stafflist[0]['staff_permissionset_id'] = 1;
        $stafflist[0]['staff_is_invisible'] = 1;
        
        if ($type == 'update')
        {
            $stafflist[0]['staff_is_remove'] = 1;
            $stafflist[0]['group_staff_id'] = 1;
        }
        
        $stafflist[1]['staff_id'] = 2;
        $stafflist[1]['staff_permissionset_id'] = 1;
        $stafflist[1]['staff_is_invisible'] = 0;
        $stafflist[1]['staff_is_remove'] = 0;
        if ($type == 'update')
        {
            $stafflist[1]['staff_is_remove'] = 0;
            $stafflist[1]['group_staff_id'] = 2;
        }
        $groupDto->setStaffList($stafflist);
        return $groupDto;
    }
    
    private function createOrgGroupDto2 ($parent = 0,$name = "Functional_Test_Group",$type = "insert")
    {
        $groupDto = new OrgGroupDto();
        $groupDto->setOrganizationId(1);
        $groupDto->setParentGroupId($parent);
        $groupDto->setGroupName($name);
        $stafflist = array();
        $stafflist[0]['group_staff_id'] = 1;
        $stafflist[0]['staff_id'] = 1;
        $stafflist[0]['staff_permissionset_id'] = 1;
        $stafflist[0]['staff_is_invisible'] = 1;
    
        if ($type == 'update')
        {
            $stafflist[0]['staff_is_remove'] = 1;
        }
       
        $stafflist[1]['staff_id'] = 2;
        $stafflist[1]['staff_permissionset_id'] = 1;
        $stafflist[1]['staff_is_invisible'] = 0;
        $stafflist[1]['staff_is_remove'] = 0;
        if ($type == 'update')
        {
            $stafflist[1]['staff_is_remove'] = 0;
        }
        $groupDto->setStaffList($stafflist);
        return $groupDto;
    }
    /**
     * {@inheritDoc}
     */
    protected function _after()
    {
    
    }
}
