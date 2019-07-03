<?php
use Codeception\Util\Stub;
use Synapse\MultiCampusBundle\EntityDto\TierDto;

class TierServiceTest extends \Codeception\TestCase\Test
{

    private $tierService;

    private $orgId = 1;

    private $coordinatorId = 1;
    
    private $invalidOrgId = -100;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->tierService = $container->get('tier_service');
    }
	
	private function createPrimaryTier()
	{
		$tierDto = new TierDto;
		$tierDto->setPrimaryTierId('CMPS1');
		$tierDto->setPrimaryTierName('Tier1');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('primary');
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);
		return $tierDetails;
	}
	
	public function testCreatePrimaryTier()
	{
		$tierDetails = $this->createPrimaryTier();
		$this->assertEquals ( 'Tier1', $tierDetails->getPrimaryTierName());
		$this->assertEquals ( 'primary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS1', $tierDetails->getPrimaryTierId());
		$this->assertEquals ( 'Description', $tierDetails->getDescription());
		$this->assertEquals ( '1', $tierDetails->getLangid());		
	}
	
	private function createSecondaryTier()
	{
		$primaryTierDetails = $this->createPrimaryTier();	
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('SecondTier');
		$tierDto->setDescription('Description secondary');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId($primaryTierDetails->getId());
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);
		return $tierDetails;
	}
	
	public function testCreateSecondaryTier()
	{
		$tierDetails = $this->createSecondaryTier();
		$this->assertEquals ( 'SecondTier', $tierDetails->getSecondaryTierName());
		$this->assertEquals ( 'secondary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS2', $tierDetails->getSecondaryTierId());
		$this->assertEquals ( 'Description secondary', $tierDetails->getDescription());
		$this->assertEquals ( '1', $tierDetails->getLangid());		
	}
	
	public function testUpdatePrimaryTier()
	{
		$tierDto = new TierDto;
		$tierDto->setPrimaryTierId('CMPS1');
		$tierDto->setPrimaryTierName('Tier1');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('primary');
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);
		$tierDto->setId($tierDetails->getId());
		$tierDto->setDescription('Tier Updated');
		$tierDetails = $this->tierService->updateTier($tierDto);
		$this->assertEquals ( 'Tier1', $tierDetails->getPrimaryTierName());
		$this->assertEquals ( 'primary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS1', $tierDetails->getPrimaryTierId());
		$this->assertEquals ( 'Tier Updated', $tierDetails->getDescription());		
	}
	
	public function testUpdateSecondaryTier()
	{
		$primaryTierDetails = $this->createPrimaryTier();	
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('SecondTier');
		$tierDto->setDescription('Description secondary');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId($primaryTierDetails->getId());
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);
		$tierDto->setId($tierDetails->getId());
		$tierDto->setDescription('Tier Updated');
		$tierDetails = $this->tierService->updateTier($tierDto);
		$this->assertEquals ( 'SecondTier', $tierDetails->getSecondaryTierName());
		$this->assertEquals ( 'secondary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS2', $tierDetails->getSecondaryTierId());
		$this->assertEquals ( 'Tier Updated', $tierDetails->getDescription());		
	}
	
	public function testViewPrimaryTier()
	{
		$primaryTier = $this->createPrimaryTier();
		$tierDetails = $this->tierService->viewTier($primaryTier->getId(), 'primary');
		$this->assertEquals ( 'Tier1', $tierDetails->getPrimaryTierName());
		$this->assertEquals ( 'primary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS1', $tierDetails->getPrimaryTierId());
		$this->assertEquals ( 'Description', $tierDetails->getDescription());		
	}
	
	public function testViewSecondaryTier()
	{
		$primaryTierDetails = $this->createPrimaryTier();	
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('SecondTier');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId($primaryTierDetails->getId());
		$tierDto->setLangid(1);
		$secondaryTier = $this->tierService->createTier($tierDto);
		$tierDetails = $this->tierService->viewTier($secondaryTier->getId(), 'secondary');
		$this->assertEquals ( 'SecondTier', $tierDetails->getSecondaryTierName());
		$this->assertEquals ( 'secondary', $tierDetails->getTierLevel());
		$this->assertEquals ( 'CMPS2', $tierDetails->getSecondaryTierId());
		$this->assertEquals ( 'Description', $tierDetails->getDescription());
	}
	
	public function testDeleteSecondaryTier()
	{
		$primaryTierDetails = $this->createPrimaryTier();	
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('SecondTier');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId($primaryTierDetails->getId());
		$tierDto->setLangid(1);
		$secondaryTier = $this->tierService->createTier($tierDto);
		$deleteTier = $this->tierService->deleteSecondaryTier($secondaryTier->getId(),'secondary');	
	}
	
	 /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
		
	public function testCreateEmptyPrimaryTier()
	{
		$tierDto = new TierDto;
		$tierDto->setPrimaryTierId('CMPS1');
		$tierDto->setPrimaryTierName('');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('primary');
		$tierDto->setLangid(1);
		$tierDetails = $this->tierService->createTier($tierDto);
		$this->assertSame('{"errors": ["Name field cannot be empty"],
			"data": [],
			"sideLoaded": []
			}',$tierDetails);
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateSecondaryTierWithInvalidTierId()
	{		
		$tierDto = new TierDto;
		$tierDto->setSecondaryTierId('CMPS2');
		$tierDto->setSecondaryTierName('SecondTier');
		$tierDto->setDescription('Description');
		$tierDto->setTierLevel('secondary');
		$tierDto->setPrimaryTierId('-120');
		$tierDto->setLangid(1);
		$secondaryTier = $this->tierService->createTier($tierDto);
		$this->assertSame('{"errors": ["Tier Not Found"],
			"data": [],
			"sideLoaded": []
			}',$secondaryTier);
		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testViewInvalidPrimaryTier()
	{
		$primaryTier = $this->createPrimaryTier();
		$tierDetails = $this->tierService->viewTier('-120', 'primary');
		$this->assertSame('{"errors": ["Tier Not Found"],
			"data": [],
			"sideLoaded": []
			}',$tierDetails);		
	}
	
	public function testListPrimaryTier()
	{
		$primaryTier = $this->createPrimaryTier();		
		$tierDetails = $this->tierService->listTier($primaryTier->getId(), 'primary');		
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testFindById()
	{
		$tierDetails = $this->tierService->find($this->invalidOrgId);
	}		
}
		
	