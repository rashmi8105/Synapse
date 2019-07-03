<?php
use Codeception\Util\Stub;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto;

class CampusServiceTest extends \Codeception\TestCase\Test
{

    private $campusService;
	
	private $primaryTierId = 128;
	
	private $secondaryTierId = 129;
	
	private $campusId = 130;
	
	private $sourceCampus = 130;
	
	private $destinationCampus = 133;
	
	private $requestedBy = 242;
	
	private $requestedFor = 243;

	private $changeRequestId = 9;
	
	public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->userService = $container->get('users_service');
		$this->tierService = $container->get('tier_service');
		$this->tierUserService = $container->get('tier_users_service');
		$this->campusService = $container->get('campus_service');
    }
	
	public function testCreateCampus()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);
		$this->assertEquals('campusname', $campus->getCampusName());
		$this->assertEquals('campusname', $campus->getCampusNickName());
		$this->assertEquals('123ABC', $campus->getCampusId());
		$this->assertEquals('Pacific', $campus->getTimezone());
		$this->assertEquals('mapworksabc', $campus->getSubdomain());
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */	
	public function testCreateCampuswithInvalidCampusName()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('campus-name');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);		
		$this->assertSame('{"errors": ["Name cannot contain special characters"],
			"data": [],
			"sideLoaded": []
			}',$campus);
	}

	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testCreateCampuswithDuplicateName()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('Campus');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);
		$this->assertSame('{"errors": ["Name already exists."],
			"data": [],
			"sideLoaded": []
			}',$campus);
	}
	
	public function testUpdateCampus()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);		
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksxyz');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('updatecampus');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campusDto->setType('edit');
		$campusDto->setId($campus->getId());		
		$campus = $this->campusService->updateMoveHierarchyCampus($this->secondaryTierId, $campusDto);		
		$this->assertEquals('campusname', $campus->getCampusName());
		$this->assertEquals('updatecampus', $campus->getCampusNickName());
		$this->assertEquals('123ABC', $campus->getCampusId());
		$this->assertEquals('Pacific', $campus->getTimezone());
		$this->assertEquals('mapworksxyz', $campus->getSubdomain());
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testCreateCampusWithInvalidId()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksxyz');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('updatecampus');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campusDto->setType('edit');
		$campusDto->setId(-100);		
		$campus = $this->campusService->updateMoveHierarchyCampus($this->secondaryTierId, $campusDto);
		$this->assertSame('{"errors": ["Campus not found"],
			"data": [],
			"sideLoaded": []
			}',$campus);		
	}
	
	public function testListHierarchyCampus()
	{
		$this->markTestSkipped("Fatal Error");
		$campus = $this->campusService->listCampuses($this->secondaryTierId);
		$this->assertInternalType ( 'object', $campus);
		$this->assertEquals($this->primaryTierId, $campus->getPrimaryTierId());
		$this->assertEquals($this->secondaryTierId, $campus->getSecondaryTierId());
		$this->assertInternalType('array', $campus->getCampus());
		$this->assertInternalType('object', $campus->getCampus()[0]);		
	}
	
	public function testListSoloCampus()
	{
		$soloCampus = $this->campusService->listSoloCampuses();		
		$this->assertInternalType('object', $soloCampus);
		$this->assertEquals('Solo Campus', $soloCampus->getCampus()[0]->getCampusName());
		$this->assertEquals('solocampus', $soloCampus->getCampus()[0]->getSubdomain());		
	}
	
	public function testDeleteHierarchyCampus()
	{
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);
		$delCampus = $this->campusService->deleteHierarchyCampus($this->secondaryTierId, $campus->getId());
		$this->assertEquals($campus->getId(), $delCampus);		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testDeleteInvalidHierarchyCampus()
	{	
		$campusDto = new CampusDto;
		$campusDto->setSubdomain('mapworksabc');
        $campusDto->setTimezone('Pacific');
        $campusDto->setCampusId('123ABC');
		$campusDto->setStatus('Active');
		$campusDto->setCampusNickName('campusname');
		$campusDto->setCampusName('campusname');
		$campusDto->setLangid(1);
		$campus = $this->campusService->createHierarchyCampus($this->secondaryTierId, $campusDto);
		$delCampus = $this->campusService->deleteHierarchyCampus(-100, $campus->getId());
		$this->assertSame('{"errors": ["Invalid Parent tier"],
			"data": [],
			"sideLoaded": []
			}',$delCampus);	
	}
	
	public function testCreateHomeCampusChangeRequest()
	{
		$changeRequestDto = new ChangeRequestDto;
		$changeRequestDto->setSourceCampus($this->sourceCampus);
		$changeRequestDto->setDestinationCampus($this->destinationCampus);
		$changeRequestDto->setRequestedBy($this->requestedBy);
		$changeRequestDto->setRequestedFor($this->requestedFor);
		$changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
		$this->assertEquals($this->sourceCampus, $changeRequest->getSourceCampus());
		$this->assertEquals($this->destinationCampus, $changeRequest->getDestinationCampus());
		$this->assertEquals($this->requestedBy, $changeRequest->getRequestedBy());
		$this->assertEquals($this->requestedFor, $changeRequest->getRequestedFor());
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateHomeCampusChangeRequestInvalidSource()
	{
		$changeRequestDto = new ChangeRequestDto;
		$changeRequestDto->setSourceCampus(-100);
		$changeRequestDto->setDestinationCampus($this->destinationCampus);
		$changeRequestDto->setRequestedBy($this->requestedBy);
		$changeRequestDto->setRequestedFor($this->requestedFor);
		$changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
		$this->assertSame('{"errors": ["Invalid source campus"],
			"data": [],
			"sideLoaded": []
			}',$changeRequest);	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateHomeCampusChangeRequestInvalidDestination()
	{
		$changeRequestDto = new ChangeRequestDto;
		$changeRequestDto->setSourceCampus($this->sourceCampus);
		$changeRequestDto->setDestinationCampus(-100);
		$changeRequestDto->setRequestedBy($this->requestedBy);
		$changeRequestDto->setRequestedFor($this->requestedFor);
		$changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
		$this->assertSame('{"errors": [" Invalid destination campus"],
			"data": [],
			"sideLoaded": []
			}',$changeRequest);	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateHomeCampusChangeRequestInvalidRequestedBy()
	{
		$changeRequestDto = new ChangeRequestDto;
		$changeRequestDto->setSourceCampus($this->sourceCampus);
		$changeRequestDto->setDestinationCampus($this->destinationCampus);
		$changeRequestDto->setRequestedBy(-100);
		$changeRequestDto->setRequestedFor($this->requestedFor);
		$changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
		$this->assertSame('{"errors": ["Invalid requested person"],
			"data": [],
			"sideLoaded": []
			}',$changeRequest);	
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	
	public function testCreateHomeCampusChangeRequestInvalidRequestedFor()
	{
		$changeRequestDto = new ChangeRequestDto;
		$changeRequestDto->setSourceCampus($this->sourceCampus);
		$changeRequestDto->setDestinationCampus($this->destinationCampus);
		$changeRequestDto->setRequestedBy($this->requestedBy);
		$changeRequestDto->setRequestedFor(-100);
		$changeRequest = $this->campusService->createChangeRequest($changeRequestDto);
		$this->assertSame('{"errors": ["Invalid requested person"],
			"data": [],
			"sideLoaded": []
			}',$changeRequest);	
	}
	
	public function testDeleteChangeRequest()
	{
		$delChangeRequest = $this->campusService->deleteChangeRequest($this->changeRequestId);
		$this->assertEquals($this->changeRequestId, $delChangeRequest);		
	}
	
	/**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
	public function testDeleteInvalidChangeRequest()
	{
		$delChangeRequest = $this->campusService->deleteChangeRequest(-100);
		$this->assertSame('{"errors": ["Invalid request ID"],
			"data": [],
			"sideLoaded": []
			}',$delChangeRequest);	
	}
	
	public function testListChangeRequestSent()
	{
		$listChangeRequest = $this->campusService->listChangeRequest('sent', $this->requestedBy, $this->campusId);
		$this->assertInternalType('array', $listChangeRequest);
		$this->assertInternalType('object', $listChangeRequest[0]);
		$this->assertEquals('shiju@mailinator.com', $listChangeRequest[0]->getEmail());
		$this->assertEquals('Adem', $listChangeRequest[0]->getLastName());
		$this->assertEquals('Siju', $listChangeRequest[0]->getFirstName());
		$this->assertEquals('SHIJU', $listChangeRequest[0]->getExternalId());
		$this->assertInternalType('object', $listChangeRequest[0]->getRequestedFrom());
	}
	
	public function testListChangeRequestReceived()
	{
		$listChangeRequest = $this->campusService->listChangeRequest('received', $this->requestedBy, $this->campusId);		
		$this->assertInternalType('array', $listChangeRequest);
		$this->assertInternalType('object', $listChangeRequest[0]);
		$this->assertEquals('shiju@mailinator.com', $listChangeRequest[0]->getEmail());
		$this->assertEquals('Adem', $listChangeRequest[0]->getLastName());
		$this->assertEquals('Siju', $listChangeRequest[0]->getFirstName());
		$this->assertEquals('SHIJU', $listChangeRequest[0]->getExternalId());
		$this->assertInternalType('object', $listChangeRequest[0]->getRequestedBy());
	}
	
	public function testUpdateChangeRequestInvalidPerson()
	{
		$this->markTestSkipped("Fatal error");
		$campusChangeRequest = new CampusChangeRequestDto;
		$campusChangeRequest->setRequestId($this->changeRequestId);
		$campusChangeRequest->setStatus('no');
		$updateChangeRequest = $this->campusService->updateChangeRequest($campusChangeRequest, $this->campusId);
		$this->assertEquals($this->changeRequestId, $updateChangeRequest->getRequestId());
		$this->assertEquals('no', $updateChangeRequest->getStatus());		
	}
	
	public function testListTierUsers()
	{
		$this->markTestSkipped("Fatal error");
		$tierUsers = $this->campusService->listTierUsersCampus(242);				
	}		
}