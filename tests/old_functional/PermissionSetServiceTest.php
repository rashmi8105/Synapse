<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\PermissionSetDto;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\RestBundle\Entity\PermissionSetStatusDto;

class PermissionSetServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\PermissionSetService
     */
    private $permissionSetService;

    /**
     * {@inheritDoc}
     */
    private $langId = 1;
	private $invalidLangId = -1;
    private $status='';
	private $blockType = "profile" ;
	

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->permissionSetService = $this->container->get('permissionset_service');
    }

    /**
     * 
     * Common Functions
     */
    
    public function createPermissionSetDtoWithAllData()
    {
        $permissionsetDto = new PermissionSetDto();
        $permissionsetDto->setPermissionTemplateName("MY Permission Template");
        $accessLevel = new AccessLevelDto();
        $accessLevel->setAggregateOnly(true);
        $accessLevel->setIndividualAndAggregate(false);
        $permissionsetDto->setAccessLevel($accessLevel);
        $permissionsetDto->setRiskIndicator(true);
        $permissionsetDto->setIntentToLeave(false);
        $permissionsetDto->setProfileBlocks($this->createBlockDto(4, 1));
        $permissionsetDto->setSurveyBlocks($this->createBlockDto(4, 7));
        $permissionsetDto->setFeatures($this->createFeatures());
        $permissionsetDto->setLangId($this->langId);
        return $permissionsetDto;
    }
   
  public function testCreatePermissionSetWithReferralOff()
    {
        $this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $features = $permissionSetDto->getFeatures();
        $features[0]->setReceiveReferrals(false);
        $permissionSetDto->setFeatures($features);
        $permission = $this->permissionSetService->create($permissionSetDto);
        
        $this->assertEquals('MY Permission Template', $permission->getPermissionTemplateName());
        $this->assertNotNull($permission->getPermissionTemplateId());
        $this->assertEquals($this->langId, $permission->getLangId());
        $this->assertTrue($permission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertFalse($permission->getFeatures()[0]->getReceiveReferrals());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $permission->getAccessLevel());	
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreatePermissionSetInvalidLang()
    {
		$this->markTestSkipped("Errored");
    	$permissionSetDto = $this->createPermissionSetDtoWithAllData();
    	$permissionSetDto->setLangId(-1);
    	
    	$permission = $this->permissionSetService->create($permissionSetDto);
    }

   public function createBlockDto($count, $start)
    {
        $return = [];
        for ($i = $start; $i < $count; $i ++) {

            $blockDto = new BlockDto();
            $blockDto->setBlockId($i);
            if($i == 3 || $i == 10)
            {
                $blockDto->setBlockSelection(false);
            }else{
                $blockDto->setBlockSelection(true);
            }

            $return[] = $blockDto;
        }
        return $return;
    }

    public function createFeatures()
    {
        $return = [];
        for ($i = 1; $i < 3; $i ++) {
            $blockDto = new FeatureBlockDto();
            $blockDto->setId($i);
            $per = new PermissionValueDto();
            $per->setCreate(true);
            $per->setView(true);
            $blockDto->setPrivateShare($per);

            $per = new PermissionValueDto();
            $per->setCreate(true);
            $per->setView(true);
            $blockDto->setPublicShare($per);

            $per = new PermissionValueDto();
            $per->setCreate(true);
            $per->setView(true);
            $blockDto->setTeamsShare($per);
            if ($i == 1) {
                $blockDto->setReceiveReferrals(true);
            }
            $return[] = $blockDto;
        }
        return $return;
    }

    public function  testListPermissionSetByStatus(){
        $this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setIntentToLeave(true);
        $permissionSetDto->setRiskIndicator(false);
        $this->permissionSetService->create($permissionSetDto);
        $result = $this->permissionSetService->listPermissionSetByStatus($this->langId,$this->status);
        
        //print_r($result);die;
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->langId, $result['lang_id']);
        $this->assertNotNull($result['lang_id']);
        $this->assertNotNull($result['permission_template'][0]->getPermissionTemplateId());
        $this->assertNotNull($result['permission_template'][1]->getPermissionTemplateId());
        $this->assertNotNull($result['permission_template'][2]->getPermissionTemplateId());
        $this->assertNotNull($result['permission_template'][0]->getPermissionTemplateName());
        $this->assertNotNull($result['permission_template'][1]->getPermissionTemplateName());
        $this->assertNotNull($result['permission_template'][2]->getPermissionTemplateName());
    }
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function  testListPermissionSetByStatusInvalid(){
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSet= $this->permissionSetService->create($permissionSetDto);
        $permissionSet = $this->permissionSetService->listPermissionSetByStatus(-1,$this->status);
    }
	
	
	   /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreatePermissionSetWithInvalidBlock()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $block = $permissionSetDto->getProfileBlocks();
        $block[0]->setBlockId(-1);
        $permissionSetDto->setProfileBlocks($block);
        $permission = $this->permissionSetService->create($permissionSetDto);
    
    }
	
	public function testCreatePermissionSetWithoutFeatures()
   	{
		 $this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setFeatures(null);
        $permission = $this->permissionSetService->create($permissionSetDto);
        $this->assertEquals('MY Permission Template', $permission->getPermissionTemplateName());
        $this->assertNotNull($permission->getPermissionTemplateId());
        $this->assertTrue($permission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertEmpty($permission->getFeatures());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $permission->getAccessLevel());
   	}
		
	  /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreatePermissionSetWithInvalidFeature()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $features = $permissionSetDto->getFeatures();
        $features[0]->setId(-1);
        $permissionSetDto->setFeatures($features);
        $permission = $this->permissionSetService->create($permissionSetDto);
        
    }
	
	  /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreatePermissionSetDuplicate()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
        $permission = $this->permissionSetService->create($permissionSetDto);
    
    }
	
	    /**
     * 
     *  Tests For Update Permission Sets starts here
     */
    
    public function testEditPermissionSetWithAll()
    {
        $this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
        $permissionSetDto->setIntentToLeave(true);
        $permissionSetDto->setRiskIndicator(false);
        $permissionSetDto->setPermissionTemplateId($permission->getPermissionTemplateId());
        $permission = $this->permissionSetService->edit($permissionSetDto);
        
        $this->assertEquals('MY Permission Template', $permission->getPermissionTemplateName());
        $this->assertNotNull($permission->getPermissionTemplateId());
        $this->assertTrue($permission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($permission->getFeatures()[0]->getReceiveReferrals());
        $this->assertFalse($permission->getRiskIndicator());
        $this->assertTrue($permission->getIntentToLeave());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $permission->getAccessLevel());
    }
	
	 /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditPermissionSetWithInvalidId()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
        $permissionSetDto->setIntentToLeave(true);
        $permissionSetDto->setRiskIndicator(false);
        $permissionSetDto->setPermissionTemplateId(-1);
        $permission = $this->permissionSetService->edit($permissionSetDto);
    
    }
	
	 /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditPermissionSetDuplicate()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission1 = $this->permissionSetService->create($permissionSetDto);
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setPermissionTemplateName("My Second Template Name");
        $permission2 = $this->permissionSetService->create($permissionSetDto);
        $permissionSetDto->setPermissionTemplateId($permission1->getPermissionTemplateId());
        $permission = $this->permissionSetService->edit($permissionSetDto);
    
    }
	
	    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditPermissionSetWithInvalidFeature()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
        
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setPermissionTemplateId($permission->getPermissionTemplateId());
        $features = $permissionSetDto->getFeatures();
        $features[0]->setId(-1);
        $permissionSetDto->setFeatures($features);
        $permission = $this->permissionSetService->edit($permissionSetDto);
    
    }
	
	
	public function testGetDatablocksWithValidLangId()
    {
		$this->markTestSkipped("Errored");
		$dataBlock = $this->permissionSetService->getDatablocks($this->langId, $this->blockType);
        $this->assertInternalType('array', $dataBlock);
        $this->assertEquals($this->langId, $dataBlock['lang_id']);
        $this->assertEquals('profile', $dataBlock['data_block_type']);
        $this->assertNotNull($dataBlock['lang_id']);
        $this->assertNotNull($dataBlock['data_blocks']);
	}
	
	   /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testGetDatablocksWithInvalidLangId()
	{
		$this->markTestSkipped("Errored");
		$dataBlock = $this->permissionSetService->getDatablocks($this->invalidLangId, $this->blockType);
		$this->assertInternalType('array', $dataBlock);
		$this->assertEquals($this->invalidLangId, $dataBlock['lang_id']);
		$this->assertEquals('profile', $dataBlock['data_block_type']);
		$this->assertNotNull($dataBlock['lang_id']);
		$this->assertNotNull($dataBlock['data_blocks']);
	}

	
	
	
	public function testEditPermissionSetDeSelectProfileBlock()
    {
        $this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
        
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setPermissionTemplateId($permission->getPermissionTemplateId());
        
        $pBlock = $permissionSetDto->getProfileBlocks();
        $pBlock[0]->setBlockSelection(false);
        
        $pBlock[2]->setBlockSelection(true);
        
        $permissionSetDto->setProfileBlocks($pBlock);
        $permission = $this->permissionSetService->edit($permissionSetDto);
        $this->assertEquals('MY Permission Template', $permission->getPermissionTemplateName());
        $this->assertNotNull($permission->getPermissionTemplateId());
        $this->assertFalse($permission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($permission->getProfileBlocks()[1]->getBlockSelection());
        $this->assertTrue($permission->getProfileBlocks()[2]->getBlockSelection());
    }
	
	  /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
    public function testEditPermissionSetDeSelectProfileBlockWithInvalidBlockId()
    {
		$this->markTestSkipped("Errored");
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permission = $this->permissionSetService->create($permissionSetDto);
    
        $permissionSetDto = $this->createPermissionSetDtoWithAllData();
        $permissionSetDto->setPermissionTemplateId($permission->getPermissionTemplateId());
    
        $pBlock = $permissionSetDto->getProfileBlocks();
        $pBlock[0]->setBlockSelection(false);
    
        $pBlock[2]->setBlockSelection(true);
        $pBlock[2]->setBlockId(-1);
        $permissionSetDto->setProfileBlocks($pBlock);
        $permission = $this->permissionSetService->edit($permissionSetDto);
        
    }
	
	public function testGetPermissionSetByValidLangId()
	{
        $this->markTestSkipped("Errored");
		$permissionSetDto = $this->createPermissionSetDtoWithAllData();
		$permission = $this->permissionSetService->create($permissionSetDto);
		$permissionset = $this->permissionSetService->getPermissionSet($this->langId,$permission->getPermissionTemplateId());
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\PermissionSetDto',$permissionset);	
		$this->assertNotNull($permissionset->getLangId());
		$this->assertNotNull($permissionset->getPermissionTemplateId());
		$this->assertNotNull($permissionset->getAccessLevel());
		$this->assertNotNull($permissionset->getPermissionTemplateStatus());
		$this->assertNotNull($permissionset->getProfileBlocks());
		
	}
	
	  /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testGetPermissionSetByInvalidLangId()
	{
		$this->markTestSkipped("Errored");
		$permissionSetDto = $this->createPermissionSetDtoWithAllData();
		$permission = $this->permissionSetService->create($permissionSetDto);
		$permissionset = $this->permissionSetService->getPermissionSet($this->invalidLangId,$permission->getPermissionTemplateId());
		
		$this->assertInternalType('array',$permissionset);
		
	}
	
	 /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testGetPermissionSetByInvalidTemplateId()
	{
		$this->markTestSkipped("Errored");
		$permissionSetDto = $this->createPermissionSetDtoWithAllData();
		$permission = $this->permissionSetService->create($permissionSetDto);
		$permissionset = $this->permissionSetService->getPermissionSet($this->langId,-1);
		$this->assertInternalType('array',$permissionset);
		
	}	
	
	
	public function testUpdateStatus()
	{
        $this->markTestSkipped("Errored");
		$permissionSetDto = $this->createPermissionSetDtoWithAllData();
		$permissionSetStatusDto = new PermissionSetStatusDto();
		$permission = $this->permissionSetService->create($permissionSetDto);
		$permissionSetStatusDto->setPermissionTemplateId($permission->getPermissionTemplateId());
		$permissionSetStatusDto->setPermissionTemplateStatus('active');
		
		$permissionsetStatus = $this->permissionSetService->updateStatus($permissionSetStatusDto);
		
		$this->assertInstanceOf('Synapse\RestBundle\Entity\PermissionSetStatusDto', $permissionsetStatus);
		$this->assertEquals($permissionSetStatusDto->getPermissionTemplateId(), $permissionsetStatus->getPermissionTemplateId());
		$this->assertEquals($permissionSetStatusDto->getPermissionTemplateStatus(), $permissionsetStatus->getPermissionTemplateStatus());
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testUpdateStatusInvalidId()
	{
		$permissionSetStatusDto = new PermissionSetStatusDto();
	
		$permissionSetStatusDto->setPermissionTemplateId(-1);
	
		$permissionsetStatus = $this->permissionSetService->updateStatus($permissionSetStatusDto);
	
	}
	
	
	public function testIsPermissionSetExists()
	{
        $this->markTestSkipped("Errored");
		$permissionsetDto = $this->createPermissionSetDtoWithAllData();
		$permission = $this->permissionSetService->create($permissionsetDto);
		$permissionset = $this->permissionSetService->isPermissionSetExists($permission->getPermissionTemplateName());
		
		$this->assertTrue($permissionset);
	}
	
	
	public function testIsPermissionSetExistsInvalid()
	{
		$permissionset = $this->permissionSetService->isPermissionSetExists('invalid');
		$this->assertFalse($permissionset);
	}
}
