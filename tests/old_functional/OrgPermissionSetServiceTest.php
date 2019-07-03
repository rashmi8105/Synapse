<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\OrgPermissionSetDto;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\BlockDto;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\RestBundle\Entity\IspBlockDto;
use Synapse\RestBundle\Entity\IsqBlockDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\RestBundle\Entity\CoursesAccessDto;

class OrgPermissionSetServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\Impl\OrgPermissionsetService
     */
    private $orgPermissionSetService;

    /**
     * {@inheritDoc}
     */
    private $orgId = 1;

    private $invalidOrgId = -2;
    
    private $userId = 1;
	
    private $featureType = 'CREATE_CONTACT';
    
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->orgPermissionSetService = $this->container->get('orgpermissionset_service');
    }
    
    protected function initializeRbac()
    {
    	// Bootstrap Rbac Authorization.
    	/** @var Manager $rbacMan */
    	$rbacMan = $this->container->get('tinyrbac.manager');
    	$rbacMan->initializeForUser($this->userId);
    }
    
    public function testGetActivePermissionset()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $result = $this->orgPermissionSetService->getActivePermissionset($this->orgId);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('permissionset_name', $result[0]);
        $this->assertArrayHasKey('id', $result[1]);
        $this->assertArrayHasKey('permissionset_name', $result[1]);
    }

    /**
     * Tests For Create Permission Sets starts here
     */
    public function testCreateOrgPermissionSetWithAll()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $this->assertInstanceOf('Synapse\RestBundle\Entity\OrgPermissionSetDto', $orgPermission);
        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getIsp()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getRiskIndicator());
        $this->assertFalse($orgPermission->getIntentToLeave());
        $this->assertTrue($orgPermission->getIsq()[0]->getBlockSelection());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }

    public function testCreateOrgPermissionSetWithReferalOff()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $features = $orgPermissionSetDto->getFeatures();
        $features[0]->setReceiveReferrals(false);
        $orgPermissionSetDto->setFeatures($features);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getRiskIndicator());
        $this->assertFalse($orgPermission->getIntentToLeave());
        $this->assertTrue($orgPermission->getIsq()[0]->getBlockSelection());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }

    public function testCreateOrgPermissionSetWithoutFeatures()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setFeatures(null);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertEmpty($orgPermission->getFeatures());
        $this->assertTrue($orgPermission->getRiskIndicator());
        $this->assertFalse($orgPermission->getIntentToLeave());
        $this->assertTrue($orgPermission->getIsq()[0]->getBlockSelection());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgPermissionSetWithInvalidFeature()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $features = $orgPermissionSetDto->getFeatures();
        $features[0]->setId(-1);
        $orgPermissionSetDto->setFeatures($features);

        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgPermissionSetWithInvalidIsp()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $isp = $orgPermissionSetDto->getIsp();
        $isp[0]->setId(-1);
        $orgPermissionSetDto->setIsp($isp);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgPermissionSetWithInvalidIsq()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $isq = $orgPermissionSetDto->getIsq();
        $isq[0]->setId(-1);
        $orgPermissionSetDto->setIsq($isq);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgPermissionSetWithInvalidBlock()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $block = $orgPermissionSetDto->getProfileBlocks();
        $block[0]->setBlockId(-1);
        $orgPermissionSetDto->setProfileBlocks($block);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgPermissionSetDuplicate()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

    }

    /**
     *
     *  Tests For Update Permission Sets starts here
     */

    public function testEditOrgPermissionSetWithAll()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermissionSetDto->setIntentToLeave(true);
        $orgPermissionSetDto->setRiskIndicator(false);
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertEquals($orgPermissionSetDto->getPermissionTemplateId(), $orgPermission->getPermissionTemplateId());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getFeatures()[0]->getReceiveReferrals());
        $this->assertFalse($orgPermission->getRiskIndicator());
        $this->assertTrue($orgPermission->getIntentToLeave());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetWithInvalidId()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermissionSetDto->setIntentToLeave(true);
        $orgPermissionSetDto->setRiskIndicator(false);
        $orgPermissionSetDto->setPermissionTemplateId(-1);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetDuplicate()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission1 = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateName("My Second Template Name");
        $orgPermission2 = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission1->getPermissionTemplateId());
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetWithInvalidFeature()
    {
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());
        $features = $orgPermissionSetDto->getFeatures();
        $features[0]->setId(-1);
        $orgPermissionSetDto->setFeatures($features);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }


    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetWithInvalidIsp()
    {
		//$this->markTestSkipped("Errored");
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());


        $isp = $orgPermissionSetDto->getIsp();
        $isp[0]->setId(-1);
        $orgPermissionSetDto->setIsp($isp);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetWithInvalidIsq()
    {
		//$this->markTestSkipped("Errored");
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());


        $isq = $orgPermissionSetDto->getIsq();
        $isq[0]->setId(-1);
        $orgPermissionSetDto->setIsq($isq);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }

    public function testEditOrgPermissionSetDeSelectProfileBlock()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());

        $pBlock = $orgPermissionSetDto->getProfileBlocks();
        $pBlock[0]->setBlockSelection(false);

        $pBlock[2]->setBlockSelection(true);

        $orgPermissionSetDto->setProfileBlocks($pBlock);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);
        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertFalse($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getProfileBlocks()[1]->getBlockSelection());
        $this->assertTrue($orgPermission->getProfileBlocks()[2]->getBlockSelection());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditOrgPermissionSetDeSelectProfileBlockWithInvalidBlockId()
    {
		//$this->markTestSkipped("Errored");
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());

        $pBlock = $orgPermissionSetDto->getProfileBlocks();
        $pBlock[0]->setBlockSelection(false);

        $pBlock[2]->setBlockSelection(true);
        $pBlock[2]->setBlockId(-1);
        $orgPermissionSetDto->setProfileBlocks($pBlock);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

    }

    public function testEditOrgPermissionSetDeSelectIsp()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());

        $isp = $orgPermissionSetDto->getIsp();
        $isp[0]->setBlockSelection(false);


        $orgPermissionSetDto->setIsp($isp);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);


        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());

        $isp = $orgPermissionSetDto->getIsp();
        $isp[0]->setBlockSelection(true);


        $orgPermissionSetDto->setIsp($isp);

        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getProfileBlocks()[1]->getBlockSelection());
        $this->assertFalse($orgPermission->getProfileBlocks()[2]->getBlockSelection());
        $this->assertTrue($orgPermission->getIsp()[0]->getBlockSelection());
    }

    public function testEditOrgPermissionSetDeSelectIsq()
    {
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);

        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());

        $isq = $orgPermissionSetDto->getIsq();
        $isq[0]->setBlockSelection(false);
        $isq[1]->setBlockSelection(true);


        $orgPermissionSetDto->setIsq($isq);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);

        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        $this->assertTrue($orgPermission->getProfileBlocks()[1]->getBlockSelection());
        $this->assertFalse($orgPermission->getProfileBlocks()[2]->getBlockSelection());
        $this->assertFalse($orgPermission->getIsq()[0]->getBlockSelection());
    }


    public function testgetPermissionTemplateByIdWithValidId()
    {

       // $this->markTestSkipped("Errored");
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermission = $this->orgPermissionSetService->getPermissionTemplateById($orgPermission->getPermissionTemplateId());

        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertEquals($orgPermissionSetDto->getOrganizationId(), $orgPermission->getOrganizationId());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }

    public function testgetPermissionTemplateByIdWithISPfalse()
    {
       // $this->markTestSkipped("Errored");
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $isp = $orgPermissionSetDto->getIsp();
        $isp[0]->setBlockSelection(false);
        $orgPermission->setIsp($isp);
        $orgPermission = $this->orgPermissionSetService->edit($orgPermissionSetDto);
        $orgPermissionSetDto->setPermissionTemplateId($orgPermission->getPermissionTemplateId());
        $orgPermission = $this->orgPermissionSetService->getPermissionTemplateById($orgPermission->getPermissionTemplateId());
        $this->assertEquals('MY Permission Template', $orgPermission->getPermissionTemplateName());
        $this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
        $this->assertNotNull($orgPermission->getPermissionTemplateId());
        $this->assertTrue($orgPermission->getProfileBlocks()[0]->getBlockSelection());
        //@todo Features, ISPs, and ISQs have to be manually fetched and added at present. - Fixed
		$this->assertFalse($orgPermission->getIsp()[0]->getBlockSelection());
    }


    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetPermissionSetByIdWithInValidId()
    {
		//$this->markTestSkipped("Errored");
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermission = $this->orgPermissionSetService->getPermissionSetById(-1);

    }


    public function testGetSurveyDataBlocks()
    {
        $this->initializeRbac();
    	$surveyDataBlock = $this->orgPermissionSetService->getSurveyDataBlocks($this->orgId);
    	$this->assertInternalType('array', $surveyDataBlock);
    	$this->assertEquals($this->orgId, $surveyDataBlock['organization_id']);
    	$this->assertEquals('survey', $surveyDataBlock['data_block_type']);
    	$this->assertNotNull($surveyDataBlock['organization_id']);
    	$this->assertNotNull($surveyDataBlock['data_blocks']);
    	
    }


    public function testGetProfileDataBlocks()
    {
    	$profileDataBlock = $this->orgPermissionSetService->getProfileDataBlocks($this->orgId);
    	$this->assertInternalType('array', $profileDataBlock);
    	$this->assertEquals($this->orgId, $profileDataBlock['organization_id']);
    	$this->assertEquals('profile', $profileDataBlock['data_block_type']);
    	$this->assertNotNull($profileDataBlock['organization_id']);
    	$this->assertNotNull($profileDataBlock['data_blocks']);
    	$this->assertNotNull($profileDataBlock['isp']);
    }

    public function testGetPermissionSetByUser()
    {
       // $this->markTestSkipped("Errored");
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgPermissionData = $this->orgPermissionSetService->getPermissionSetByUser($this->userId);    	
    	$this->assertEquals($orgPermissionSetDto->getOrganizationId(), $orgPermissionData['organization_id']);
    	$this->assertArrayHasKey('permission_template_count', $orgPermissionData);
    	$this->assertArrayHasKey('permission_templates', $orgPermissionData);    	 	
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetSurveyBlocksPermission()
    {	
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgSurveyBlockData = $this->orgPermissionSetService->getSurveyBlocksPermission($this->userId);  	
    	$this->assertArrayHasKey('survey_blocks', $orgSurveyBlockData);
    	$this->assertArrayHasKey('block_id', $orgSurveyBlockData['survey_blocks'][0]);
    	$this->assertArrayHasKey('block_name', $orgSurveyBlockData['survey_blocks'][0]);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetSurveyBlocksPermissionFromDB()
    {
       // $this->markTestSkipped("Errored");
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgSurveyBlockData = $this->orgPermissionSetService->getSurveyBlocksPermissionFromDB($this->userId);  
    	
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetProfileblockPermission()
    {
      //  $this->markTestSkipped("Errored");
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgProfileBlockData = $this->orgPermissionSetService->getProfileblockPermission($this->userId);
    	$this->assertArrayHasKey('profile_blocks', $orgProfileBlockData);
    	$this->assertArrayHasKey('block_id', $orgProfileBlockData['profile_blocks'][0]);
    	$this->assertArrayHasKey('block_name', $orgProfileBlockData['profile_blocks'][0]);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetProfileblockPermissionFromDB()
    {
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgProfileBlockData = $this->orgPermissionSetService->getProfileblockPermissionFromDB($this->userId);    	
    	$this->assertArrayHasKey('block_id', $orgProfileBlockData[0]);
    	$this->assertArrayHasKey('block_name', $orgProfileBlockData[0]);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetRiskIndicator()
    {
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$riskIndicatorData = $this->orgPermissionSetService->getRiskIndicator($this->userId);    	
    	$this->assertArrayHasKey('risk_indicator', $riskIndicatorData);
    	$this->assertArrayHasKey('intent_to_leave', $riskIndicatorData);
    	$riskIndicatorData = json_decode(json_encode($riskIndicatorData), FALSE);
    	$this->assertAttributeInternalType('boolean', 'risk_indicator', $riskIndicatorData);
    	$this->assertAttributeInternalType('boolean', 'intent_to_leave', $riskIndicatorData);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetRiskIndicatorFromDB()
    {
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$riskIndicatorData = $this->orgPermissionSetService->getRiskIndicatorFromDB($this->userId);
    	$this->assertArrayHasKey('risk_indicator', $riskIndicatorData);
    	$this->assertArrayHasKey('intent_to_leave', $riskIndicatorData);
    	$riskIndicatorData = json_decode(json_encode($riskIndicatorData), FALSE);    	
    	$this->assertAttributeInternalType('boolean', 'risk_indicator', $riskIndicatorData);
    	$this->assertAttributeInternalType('boolean', 'intent_to_leave', $riskIndicatorData);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetAllowedIspIsqBlocks()
    {
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$orgIspIsqBlockData = $this->orgPermissionSetService->getAllowedIspIsqBlocks('isp', $this->userId);    	
    	$this->assertArrayHasKey('isp', $orgIspIsqBlockData);
    	$this->assertArrayHasKey('id', $orgIspIsqBlockData['isp'][0]);
    	$this->assertArrayHasKey('item_label', $orgIspIsqBlockData['isp'][0]);    	
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetFeaturesBlockPermission()
    {        
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$featureBlockData = $this->orgPermissionSetService->getFeaturesBlockPermission($this->userId);
    	$this->assertArrayHasKey('features', $featureBlockData);
    	$this->assertArrayHasKey('id', $featureBlockData['features'][1]);
    	$this->assertArrayHasKey('name', $featureBlockData['features'][1]);
    	$this->assertArrayHasKey('private_share', $featureBlockData['features'][1]['direct_referral']);
    	$this->assertArrayHasKey('public_share', $featureBlockData['features'][1]['direct_referral']);
    	$featureBlockData = json_decode(json_encode($featureBlockData['features'][1]), FALSE);    	
    	$this->assertAttributeInternalType('integer', 'id', $featureBlockData);    	
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
	
    public function testGetFeaturesBlockPermissionFromDB()
    {   
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$featureBlockData = $this->orgPermissionSetService->getFeaturesBlockPermissionFromDB($this->userId);
    	$this->assertArrayHasKey('id', $featureBlockData[0]);
    	$this->assertArrayHasKey('name', $featureBlockData[0]);
    	$this->assertArrayHasKey('private_share', $featureBlockData[0]);
    	$this->assertArrayHasKey('public_share', $featureBlockData[0]);
    	$featureBlockData = json_decode(json_encode($featureBlockData[0]), FALSE);
    	$this->assertAttributeInternalType('integer', 'id', $featureBlockData);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetFeaturesPermission()
    {
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$featureBlockData = $this->orgPermissionSetService->getFeaturesPermission($this->userId);
    	$this->assertArrayHasKey('features', $featureBlockData);
    	$this->assertArrayHasKey('id', $featureBlockData['features'][0]);
    	$this->assertArrayHasKey('name', $featureBlockData['features'][0]);  
    	$featureBlockData = json_decode(json_encode($featureBlockData['features'][0]), FALSE);
    	$this->assertAttributeInternalType('integer', 'id', $featureBlockData);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }
    
    public function testGetFeaturesPermissionFromDB()
    {    	
        $this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
    	$orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
    	$featureBlockData = $this->orgPermissionSetService->getFeaturesPermissionFromDB($this->userId);    	
    	$this->assertArrayHasKey('id', $featureBlockData[0]);
    	$this->assertArrayHasKey('name', $featureBlockData[0]);
    	$featureBlockData = json_decode(json_encode($featureBlockData[0]), FALSE);
    	$this->assertAttributeInternalType('integer', 'id', $featureBlockData);
    	$this->assertInstanceOf('Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel());
    }

    public function testGetAllowedFeatureAccess()
    {
    	$this->initializeRbac();
    	$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData ();
		$orgPermission = $this->orgPermissionSetService->create ( $orgPermissionSetDto, false );
		$featureBlockData = $this->orgPermissionSetService->getAllowedFeatureAccess ( $this->userId, $this->featureType );
		$this->assertNotNull ( $featureBlockData );
		$this->assertInstanceOf ( 'Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel () );
	}
	public function testGetAccessLevelPermission() {
		$this->initializeRbac ();
		$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData ();
		$orgPermission = $this->orgPermissionSetService->create ( $orgPermissionSetDto, false );
		$accessLevelData = $this->orgPermissionSetService->getAccessLevelPermission ( $this->userId );		
		$this->assertArrayHasKey ( 'individual_and_aggregate', $accessLevelData['access_level'] );
		$this->assertArrayHasKey ( 'aggregate_only', $accessLevelData['access_level'] );
		$accessLevelData = json_decode ( json_encode ( $accessLevelData['access_level'] ), FALSE );		
		$this->assertAttributeInternalType ( 'boolean', 'individual_and_aggregate', $accessLevelData );
		$this->assertAttributeInternalType ( 'boolean', 'aggregate_only', $accessLevelData );
		$this->assertInstanceOf ( 'Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel () );
	}
	
	public function testGetAccessLevelPermissionFromDB() {
	    $this->initializeRbac();
		$orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData ();
		$orgPermission = $this->orgPermissionSetService->create ( $orgPermissionSetDto, false );
		$accessLevelData = $this->orgPermissionSetService->getAccessLevelPermissionFromDB ( $this->userId );		
		$this->assertArrayHasKey ( 'individual_and_aggregate', $accessLevelData['access_level'] );
		$this->assertArrayHasKey ( 'aggregate_only', $accessLevelData['access_level'] );
		$accessLevelData = json_decode ( json_encode ( $accessLevelData['access_level'] ), FALSE );		
		$this->assertAttributeInternalType ( 'boolean', 'individual_and_aggregate', $accessLevelData );
		$this->assertAttributeInternalType ( 'boolean', 'aggregate_only', $accessLevelData );
		$this->assertInstanceOf ( 'Synapse\RestBundle\Entity\AccessLevelDto', $orgPermission->getAccessLevel () );		
    }
        
    /**
     *
     * Common Functions
     */

    public function createOrgPermissionSetDtoWithAllData()
    {
        $orgPermissionsetDto = new OrgPermissionSetDto();
        $orgPermissionsetDto->setPermissionTemplateName("MY Permission Template");
        $accessLevel = new AccessLevelDto();
        $accessLevel->setAggregateOnly(false);
        $accessLevel->setIndividualAndAggregate(true);
        $orgPermissionsetDto->setAccessLevel($accessLevel);
        $coursesAccess = new CoursesAccessDto();
        $coursesAccess->setViewCourses(true);
        $coursesAccess->setViewAllFinalGrades(false);
        $coursesAccess->setViewAllAcademicUpdateCourses(true);
        $coursesAccess->setCreateViewAcademicUpdate(true);
        $orgPermissionsetDto->setCoursesAccess($coursesAccess);
        $orgPermissionsetDto->setRiskIndicator(true);
        $orgPermissionsetDto->setIntentToLeave(false);
		$orgPermissionsetDto->setCurrentFutureIsq(false);
		$orgPermissionsetDto->setReportsAccess($this->createReportAccess());
        $orgPermissionsetDto->setProfileBlocks($this->createBlockDto(4, 1));
        $orgPermissionsetDto->setIsp($this->createISP());
        $orgPermissionsetDto->setSurveyBlocks($this->createBlockDto(4, 7));
        $orgPermissionsetDto->setIsq($this->createISQ());
        $orgPermissionsetDto->setFeatures($this->createFeatures());
        $orgPermissionsetDto->setOrganizationId($this->orgId);

        return $orgPermissionsetDto;
    }

	public function createReportAccess()
	{
		$return = [];
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

    public function createISP()
    {
        $return = [];
        for ($i = 1; $i < 2; $i ++) {
            $blockDto = new IspBlockDto();
            $blockDto->setId($i);
            $blockDto->setBlockSelection(true);
            $return[] = $blockDto;
        }
        return $return;
    }

    public function createISQ()
    {
        $return = [];
        for ($i = 1; $i < 3; $i ++) {
            $blockDto = new IsqBlockDto();
            $blockDto->setId($i);
            $blockDto->setSurveyId(1);
            if($i == 2)
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
            if ($i == 1) {
				$blockDto = new FeatureBlockDto();
				$blockDto->setId($i);
                $blockDto->setReceiveReferrals(true);
				$shareOPTDto  = new FeatureBlockDto();
				$shareOPTDto ->setId($i);
				$per = new PermissionValueDto();
				$per->setCreate(true);
				$per->setView(true);
				$shareOPTDto ->setPrivateShare($per);
				$per = new PermissionValueDto();
				$per->setCreate(true);
				$per->setView(true);
				$shareOPTDto ->setPublicShare($per);
				$per = new PermissionValueDto();
				$per->setCreate(true);
				$per->setView(true);
				$shareOPTDto ->setTeamsShare($per);	
				$blockDto ->setDirectReferral($shareOPTDto);
				$blockDto ->setReasonRoutedReferral($shareOPTDto);				
            } else {
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
			}
            $return[] = $blockDto;
        }
        return $return;
    }

    public function  testGetOrganizationPermissionsets(){
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $result = $this->orgPermissionSetService->getPermissionsetsByOrganizationId($this->orgId);
        $orgPermissionSetDto->setIntentToLeave(true);
        $orgPermissionSetDto->setRiskIndicator(false);
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->orgId, $result['organization_id']);
        $this->assertNotNull($result['organization_id']);
    }
    /**
     * @expectedException Synapse\CoreBundle\Exception\AccessDeniedException
     */
    public function  testGetOrganizationPermissionsetsInvalid(){
        $this->initializeRbac();
        $orgPermissionSetDto = $this->createOrgPermissionSetDtoWithAllData();
        $orgPermission = $this->orgPermissionSetService->create($orgPermissionSetDto,false);
        $orgPermission = $this->orgPermissionSetService->getPermissionsetsByOrganizationId(-1);
    }
    
    public function testFind()
    {
    	$orgPermission = $this->orgPermissionSetService->find(1);
    	$this->assertNotNull($orgPermission->getId());
    	$this->assertInstanceOf('Synapse\CoreBundle\Entity\Organization', $orgPermission->getOrganization());	
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testFindInvalid()
    {
    	$orgPermission = $this->orgPermissionSetService->find(-1);
    }
}