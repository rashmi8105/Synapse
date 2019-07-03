<?php

use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SharedSearchDto;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

class SharedSearchServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\SearchBundle\Service\Impl\SharedSearchService
     */
    private $sharedSearchService;
    
    private $savedSearchService;

    private $organizationId = 1;
    private $savedSearchId = 1;
    private $personId = 1;
    private $personIdSharedWith = 2;
    private $timezone = 'Pacific';
    private $invalidOrg = -5;

    public function _before()
    {
    	$this->container = $this->getModule('Symfony2')->kernel->getContainer();
    	$this->sharedSearchService = $this->container
    	->get('sharedsearch_service');
    	
    	$this->savedSearchService = $this->container
    	->get('savedsearch_service');
    }

    private function createSharedSearchDto()
    {
        $sharedSearchDto = new SharedSearchDto();
        $sharedSearchDto->setOrganizationId($this->organizationId);
        $sharedSearchDto->setSavedSearchId($this->savedSearchId);
        $sharedSearchDto->setSavedSearchName('Search1');
        $sharedSearchDto->setSharedByPersonId($this->personId);
        $sharedSearchDto->setsharedWithPersonIds($this->personIdSharedWith);
        return $sharedSearchDto;
    }

    private function createSavedSearchDto()
    {
    	$saveSearchDto = new SaveSearchDto();
    	$saveSearchDto->setOrganizationId($this->organizationId);
    	$saveSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
    	$saveSearchDto->setPersonId($this->personId);
    	$searchAttribute["risk_indicator_ids"] = "2";
    	$searchAttribute["intent_to_leave_ids"] = "10,20";
    	$searchAttribute["group_ids"] = "1,1299";
    	$searchAttribute["referral_status"] = "open";
    	$searchAttribute["contact_types"] = "interaction";
    	$saveSearchDto->setSearchAttributes($searchAttribute);
    	return $saveSearchDto;
    }
    
    private function createSharedSearchDtoWithSearchAtt()
    {
        $sharedSearchDto = new SharedSearchDto();
        $sharedSearchDto->setOrganizationId($this->organizationId);
        $sharedSearchDto->setSavedSearchId($this->savedSearchId);
        $sharedSearchDto->setSavedSearchName('Search1');
        $sharedSearchDto->setSharedByPersonId($this->personId);
        $sharedSearchDto->setsharedWithPersonIds($this->personIdSharedWith);
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "10,20";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $sharedSearchDto->setSearchAttributes($searchAttribute);
        return $sharedSearchDto;
    }

    public function testCreateSharedSearchWithSameName()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SharedSearchDto', $sharedSearch);
        $this->assertEquals('Search1', $sharedSearch->getSavedSearchName());
        $this->assertEquals($this->organizationId, $sharedSearch->getOrganizationId());
        $this->assertEquals($this->savedSearchId, $sharedSearch->getSavedSearchId());
        $this->assertEquals($this->personId, $sharedSearch->getSharedByPersonId());
        $this->assertEquals($this->personIdSharedWith, $sharedSearch->getsharedWithPersonIds());
    }

    public function testCreateSharedSearchWithDifferentName()
    {
    	$sharedSearchDto = $this->createSharedSearchDto();
    	$sharedSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
    	$sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
    	$this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SharedSearchDto', $sharedSearch);
    	$this->assertEquals($this->organizationId, $sharedSearch->getOrganizationId());
    	$this->assertEquals($this->savedSearchId, $sharedSearch->getSavedSearchId());
    	$this->assertEquals($this->personId, $sharedSearch->getSharedByPersonId());
    	$this->assertEquals($this->personIdSharedWith, $sharedSearch->getsharedWithPersonIds());
    }

    public function testGetSharedSearches()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        $sharedSearchList = $this->sharedSearchService->getSharedSearches($this->personId, $this->timezone, $this->organizationId);
        $this->assertInternalType('array', $sharedSearchList);
        $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SharedSearchListResponseDto', $sharedSearchList['shared_searches'][0]);
    }

    public function testEditSharedSearch()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        $editSharedSearchDto = $this->createSavedSearchDto();
        $editSharedSearchDto->setSavedSearchId($sharedSearch->getId());
        $editedSearch = $this->sharedSearchService->edit($editSharedSearchDto, $this->personId);
        $this->assertInstanceOf('Synapse\SearchBundle\Entity\OrgSearch', $editedSearch);
        $this->assertEquals($this->organizationId, $editedSearch->getOrganization()->getId());
        $this->assertEquals($sharedSearch->getId(), $editedSearch->getId());
    }

    public function testDeleteSharedSearch()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        $search = $this->sharedSearchService->delete(($sharedSearch->getId() - 1), $sharedSearch->getId(), $this->personId);
        $this->assertEquals($sharedSearch->getId(), $search);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSharedSearchWithInvalidSearch()
    {
    	$sharedSearchDto = $this->createSharedSearchDto();
    	$sharedSearchDto->setSavedSearchId(-2);
    	$sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSharedSearchWithInvalidOrganization()
    {
    	$sharedSearchDto = $this->createSharedSearchDto();
    	$sharedSearchDto->setOrganizationId($this->invalidOrg);
    	$sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditInvalidSearch()
    {
    	$sharedSearchDto = $this->createSharedSearchDto();
    	$sharedSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
    	$sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
    	$editSharedSearchDto = $this->createSavedSearchDto();
    	$editSharedSearchDto->setSavedSearchId($sharedSearch->getId());
    	$editSharedSearchDto->setSavedSearchId(-1);
    	$editedSearch = $this->sharedSearchService->edit($editSharedSearchDto, $this->personId);

    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSharedSearchWithInvalidOrganization()
    {
    	$sharedSearchDto = $this->createSharedSearchDto();
    	$sharedSearchDto->setSavedSearchName(uniqid("SharedSearch",true));
    	$sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
    	$editSharedSearchDto = $this->createSavedSearchDto();
    	$editSharedSearchDto->setSavedSearchId($sharedSearch->getId());
    	$editSharedSearchDto->setOrganizationId(-1);
    	$editedSearch = $this->sharedSearchService->edit($editSharedSearchDto, $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteSharedSearchInvalid()
    {
    	$search = $this->sharedSearchService->delete(1,-1, $this->personId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSharedSearchFromCustomSearchTabWithSameName()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearchDto->setSavedSearchName(uniqid("SharedSearch_",true));
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        
        $sharedSearchDtoNew = $this->createSharedSearchDtoWithSearchAtt();
        $sharedSearchDtoNew->setSavedSearchName($sharedSearch->getSavedSearchName());
        $sharedSearchDtoNew->setSavedSearchId(-1);
        $sharedSearchDirect = $this->sharedSearchService->create($sharedSearchDtoNew);
    }
    
    public function testCreateSharedSearchFromCustomSearchTabWithDifferentName()
    {
        $sharedSearchDtoNew = $this->createSharedSearchDtoWithSearchAtt();
        $sharedSearchDtoNew->setSavedSearchName(uniqid("SharedSearch_",true));
        $sharedSearchDtoNew->setSavedSearchId(-1);
        $sharedSearchDirect = $this->sharedSearchService->create($sharedSearchDtoNew);
        
        $this->assertInstanceOf('Synapse\SearchBundle\EntityDto\SharedSearchDto', $sharedSearchDirect);
        $this->assertEquals($sharedSearchDirect->getOrganizationId(), $this->organizationId);
        $this->assertEquals($sharedSearchDirect->getSharedByPersonId(), $this->personId);
        $this->assertEquals($sharedSearchDirect->getsharedWithPersonIds(), $this->personIdSharedWith);
        $this->assertNotEmpty($sharedSearchDirect->getId());
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSharedSearchFromSharedTabWithSameName()
    {
        $savedSearchDto = $this->createSavedSearchDto();
        $savedSearchDto->setSavedSearchName(uniqid("SharedSearch_",true));
        $savedSearch = $this->savedSearchService->createSavedSearches($savedSearchDto, $this->personId);

        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearchDto->setSavedSearchName($savedSearch->getSavedSearchName());
        $sharedSearchDto->setSavedSearchId($savedSearch->getSavedSearchId());
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);

        $searchId = (int)$sharedSearch->getId() - 1;
        $sharedSearchDtoNew = $this->createSharedSearchDto();
        $sharedSearchDtoNew->setSavedSearchId($searchId);
        $sharedSearchDtoNew->setSavedSearchName($sharedSearch->getSavedSearchName());

        $sharedSearchNew = $this->sharedSearchService->create($sharedSearchDto);
    }
    
    public function testDeleteSharedSearchWithoutSharedBy()
    {
        $sharedSearchDto = $this->createSharedSearchDto();
        $sharedSearchDto->setSavedSearchName(uniqid("SharedSearch_",true));
        $sharedSearch = $this->sharedSearchService->create($sharedSearchDto);
        $search = $this->sharedSearchService->delete(($sharedSearch->getId() - 1));
    }
}