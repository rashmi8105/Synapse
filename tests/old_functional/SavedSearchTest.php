<?php
use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\EntityDto\SavedSearchesDto;
use Synapse\SearchBundle\Service\Impl\SavedSearchService;

class SavedSearchTest extends \Codeception\TestCase\Test
{

    private $container;

    private $savedService;

    private $organization = 1;

    private $personId = 1;

    private $invalidOrganization = -5;

    private $invalidPerson = - 1;

    private $savedSearchName = "My saved search name ";

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\OrganizationlangService
     */
    private $organizationLangService;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->savedService = $this->container->get('savedsearch_service');
        $this->organizationLangService = $this->container->get('organizationlang_service');
    }

    public function testCreateSavedSearch()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        if ($savedSearch) {
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals($this->savedSearchName, $savedSearch->getSavedSearchName());
            $this->assertNotNull($savedSearch->getSavedSearchId(), "Saved search id is not null");
        }
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSavedSearchWithInvalidOrganization()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $saveSearchDto->setOrganizationId($this->invalidOrganization);
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSavedSearchWithInvalidOrganization()
    {
        $saveSearchDto = $this->editSavedSearchDtoWithInvalidOrganization();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    }

    public function testCancelSavedSearch()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $newSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        $this->savedService->cancelSavedsearch($newSearch->getSavedSearchId(), $this->personId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelSavedSearchWithInvalidPerson()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $newSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        $this->savedService->cancelSavedsearch($newSearch->getSavedSearchId(), $this->invalidPerson);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCancelSavedSearchWithInvalidSearchId()
    {
        $this->savedService->cancelSavedsearch(- 1, $this->personId);
    }

    public function testGetSavedSearch()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $newSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        $getSearch = $this->savedService->getSavedSearch($newSearch->getSavedSearchId(), $this->organization);
        $this->assertEquals($newSearch->getSavedSearchId(), $getSearch->getSavedSearchId());
        $this->assertEquals($this->savedSearchName, $getSearch->getSavedSearchName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSavedSearchWithInvalidSearchId()
    {
        $this->savedService->getSavedSearch(- 1, $this->organization);
    }

    public function testListSavedSearch()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $organization = $this->organizationLangService->getOrganization($this->organization);
        $newSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        $listSerach = $this->savedService->listSavedSearch($this->personId, $this->organization, $organization['timezone']);
        foreach ($listSerach->getSavedSearches() as $list) {
            if ($list->getSavedSearchId() == $newSearch->getSavedSearchId()) {
                $this->assertEquals($newSearch->getSavedSearchId(), $list->getSavedSearchId());
                $this->assertEquals($this->savedSearchName, $list->getSearchName());
            }
        }
    }

    public function testListSavedSearchWithInvalidSearchId()
    {
        $organization = $this->organizationLangService->getOrganization($this->organization);
        $this->savedService->listSavedSearch($this->personId, $this->organization, $organization['timezone']);
    }

    private function createSavedSearchDto()
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSavedSearchName($this->savedSearchName);
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "10,20";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }

    private function editSavedSearchDtoWithInvalidOrganization()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $saveSearchDto->setOrganizationId($this->invalidOrganization);
        $saveSearchDto->setSavedSearchId(1);
        $saveSearchDto->setPersonId($this->personId);
        return $saveSearchDto;
    }

    private function createSavedSearchDtoForCourse()
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSavedSearchName($this->savedSearchName);
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $searchAttribute["courses"]['department_id'] = "IT";
        $searchAttribute["courses"]['subject_id'] = "SEC001";
        $searchAttribute["courses"]['course_ids'] = "0087";
        $searchAttribute["courses"]['section_ids'] = "SEC 1";
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }

    public function testCreateSavedSearchForCourse()
    {
        $saveSearchDto = $this->createSavedSearchDtoForCourse();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        if ($savedSearch) {
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals($this->savedSearchName, $savedSearch->getSavedSearchName());
            $this->assertNotNull($savedSearch->getSavedSearchId(), "Saved search id is not null");
        }
    }

    private function createSavedSearchDtoForISP()
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSavedSearchName($this->savedSearchName);
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $searchAttribute["courses"]['department_id'] = "IT";
        $searchAttribute["courses"]['subject_id'] = "SEC001";
        $searchAttribute["courses"]['course_ids'] = "0087";
        $searchAttribute["courses"]['section_ids'] = "SEC 1";
        $isp = array();
        $isps = array();
        $ispd = array();
        $categoryType = array();
        $ansVal = array();
        $ansVals = array();
        $isp['id'] = "1";
        $isp['item_data_type'] = "N";
        $isp['is_single'] = "false";
        $isp['min_digits'] = "30";
        $isp['max_digits'] = "40";
        
        $isps['id'] = "2";
        $isps['item_data_type'] = "S";
        $ansVal['answer'] = "BCA";
        $ansVal['value'] = "1";
        
        $ansVals['answer'] = "MCA";
        $ansVals['value'] = "2";
        $isps['category_type'][] = $ansVal;
        $isps['category_type'][] = $ansVals;
        $searchAttribute['isps'][] = $isp;
        $searchAttribute['isps'][] = $isps;
        
        $ispd['id'] = "3";
        $ispd['item_data_type'] = "D";
        $ispd['start_date'] = "2014-12-16";
        $ispd['end_date'] = "2015-12-16";
        $searchAttribute['isps'][] = $ispd;
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }

    public function testCreateSavedSearchForISP()
    {
        $saveSearchDto = $this->createSavedSearchDtoForISP();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        if ($savedSearch) {
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals($this->savedSearchName, $savedSearch->getSavedSearchName());
            $this->assertNotNull($savedSearch->getSavedSearchId(), "Saved search id is not null");
        }
    }

    private function createSavedSearchDtoForEBI()
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSavedSearchName($this->savedSearchName);
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $searchAttribute["courses"]['department_id'] = "IT";
        $searchAttribute["courses"]['subject_id'] = "SEC001";
        $searchAttribute["courses"]['course_ids'] = "0087";
        $searchAttribute["courses"]['section_ids'] = "SEC 1";
        $datablocks = array();
        $datablocksC = array();
        $profileItem = array();
        $profileItems = array();
        $ansVal = array();
        $ansVals = array();
        $profileItemD = array();
        $datablocksD = array();
        $profileItem['id'] = "4";
        $profileItem['item_data_type'] = "N";
        $profileItem['is_single'] = "false";
        $profileItem['min_digits'] = "80";
        $profileItem['max_digits'] = "90";
        $datablocks['profile_block_id'] = 1;
        $datablocks['profile_items'][] = $profileItem;
        
        $profileItems['id'] = "5";
        $profileItems['item_data_type'] = "S";
        $datablocksC['profile_block_id'] = 2;
        $ansVal['answer'] = "BCA";
        $ansVal['value'] = "1";
        $ansVals['answer'] = "MCA";
        $ansVals['value'] = "2";
        $profileItems['category_type'][] = $ansVal;
        $profileItems['category_type'][] = $ansVals;
        $datablocksC['profile_items'][] = $profileItems;
        $searchAttribute['datablocks'][] = $datablocks;
        $searchAttribute['datablocks'][] = $datablocksC;
        
        $profileItemD['id'] = "6";
        $profileItemD['item_data_type'] = "D";
        $profileItemD['start_date'] = "2014-12-16";
        $profileItemD['end_date'] = "2015-12-16";
        $datablocksD['profile_block_id'] = 3;
        $datablocksD['profile_items'][] = $profileItemD;
        $searchAttribute['datablocks'][] = $datablocksD;
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }

    public function testCreateSavedSearchForEBI()
    {
        $saveSearchDto = $this->createSavedSearchDtoForEBI();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        if ($savedSearch) {
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals($this->savedSearchName, $savedSearch->getSavedSearchName());
            $this->assertNotNull($savedSearch->getSavedSearchId(), "Saved search id is not null");
        }
    }

    private function editSavedSearchDtoForAll()
    {
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($this->organization);
        $saveSearchDto->setSavedSearchName("Search1");
        $saveSearchDto->setSavedSearchId("1");
        $saveSearchDto->setPersonId("1");
        $saveSearchDto->setOrganizationId("1");
        $searchAttribute["risk_indicator_ids"] = "2";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1,1299";
        $searchAttribute["referral_status"] = "open";
        $searchAttribute["contact_types"] = "interaction";
        $searchAttribute["courses"]['department_id'] = "IT";
        $searchAttribute["courses"]['subject_id'] = "SEC001";
        $searchAttribute["courses"]['course_ids'] = "0087";
        $searchAttribute["courses"]['section_ids'] = "SEC 1";
        $datablocks = array();
        $datablocksC = array();
        $profileItem = array();
        $profileItems = array();
        $ansVal = array();
        $ansVals = array();
        $profileItemD = array();
        $datablocksD = array();
        $profileItem['id'] = "4";
        $profileItem['item_data_type'] = "N";
        $profileItem['is_single'] = "false";
        $profileItem['min_digits'] = "80";
        $profileItem['max_digits'] = "90";
        $datablocks['profile_block_id'] = 1;
        $datablocks['profile_items'][] = $profileItem;
        
        $profileItems['id'] = "5";
        $profileItems['item_data_type'] = "S";
        $datablocksC['profile_block_id'] = 2;
        $ansVal['answer'] = "BCA";
        $ansVal['value'] = "1";
        $ansVals['answer'] = "MCA";
        $ansVals['value'] = "2";
        $profileItems['category_type'][] = $ansVal;
        $profileItems['category_type'][] = $ansVals;
        $datablocksC['profile_items'][] = $profileItems;
        $searchAttribute['datablocks'][] = $datablocks;
        $searchAttribute['datablocks'][] = $datablocksC;
        
        $profileItemD['id'] = "6";
        $profileItemD['item_data_type'] = "D";
        $profileItemD['start_date'] = "2014-12-16";
        $profileItemD['end_date'] = "2015-12-16";
        $datablocksD['profile_block_id'] = 3;
        $datablocksD['profile_items'][] = $profileItemD;
        $searchAttribute['datablocks'][] = $datablocksD;
        
        $isp = array();
        $isps = array();
        $ispd = array();
        $categoryType = array();
        $ansVal = array();
        $ansVals = array();
        $isp['id'] = "1";
        $isp['item_data_type'] = "N";
        $isp['is_single'] = "false";
        $isp['min_digits'] = "30";
        $isp['max_digits'] = "40";
        $isps['id'] = "2";
        $isps['item_data_type'] = "S";
        $ansVal['answer'] = "BCA";
        $ansVal['value'] = "1";
        $ansVals['answer'] = "MCA";
        $ansVals['value'] = "2";
        $isps['category_type'][] = $ansVal;
        $isps['category_type'][] = $ansVals;
        $searchAttribute['isps'][] = $isp;
        $searchAttribute['isps'][] = $isps;
        $ispd['id'] = "3";
        $ispd['item_data_type'] = "D";
        $ispd['start_date'] = "2014-12-16";
        $ispd['end_date'] = "2015-12-16";
        $searchAttribute['isps'][] = $ispd;
        
        $saveSearchDto->setSearchAttributes($searchAttribute);
        return $saveSearchDto;
    }

    public function testEditSavedSearchForAll()
    {
        $saveSearchDto = $this->editSavedSearchDtoForAll();
        $savedSearch = $this->savedService->editSavedSearches($saveSearchDto);
        if ($savedSearch) {
            $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
            $this->assertEquals("Search1", $savedSearch->getSavedSearchName());
            $this->assertNotNull($savedSearch->getSavedSearchId(), "Saved search id is not null");
        }
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSavedSearchWithEmptyName()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $saveSearchDto->setSavedSearchName("");
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateSavedSearchWithNameLengthMoreThanAllowed()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $searchName = '';
        for($i = 0;$i<14;$i++){
            $searchName .= uniqid("SavedSearch",true);
        }
        $saveSearchDto->setSavedSearchName($searchName);
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    }
    
    public function testCreateSavedSearchWithNoContactTypes()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $searchAttributes = $saveSearchDto->getSearchAttributes();
        $searchAttributes["contact_types"] = "";
        $saveSearchDto->setSearchAttributes($searchAttributes);
        
        $saveSearchDto->setSavedSearchName(uniqid("SavedSearch_", true));
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        
        $this->assertEquals($this->organization, $savedSearch->getOrganizationId());
        $this->assertNotEmpty($savedSearch->getSavedSearchId());
        $this->assertNotEmpty($savedSearch->getPersonId());
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSavedSearchWithEmptyName()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
        
        $editSearchDto = $this->editSavedSearchDtoForAll();
        $editSearchDto->setSavedSearchId($savedSearch->getSavedSearchId());
        $editSearchDto->setSavedSearchName("");
        
        $editSearch = $this->savedService->editSavedSearches($editSearchDto);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testEditSavedSearchWithNameLengthMoreThanAllowed()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    
        $editSearchDto = $this->editSavedSearchDtoForAll();
        $editSearchDto->setSavedSearchId($savedSearch->getSavedSearchId());
        
        $searchName = '';
        for($i = 0;$i<14;$i++){
            $searchName .= uniqid("SavedSearch",true);
        }
        $editSearchDto->setSavedSearchName($searchName);
    
        $editSearch = $this->savedService->editSavedSearches($editSearchDto);
    }
    
    public function testEditSavedSearchWithNoContactTypes()
    {
        $saveSearchDto = $this->createSavedSearchDto();
        $savedSearch = $this->savedService->createSavedSearches($saveSearchDto, $this->personId);
    
        $editSearchDto = $this->editSavedSearchDtoForAll();
        $editSearchDto->setSavedSearchId($savedSearch->getSavedSearchId());
        $editSearchDto->setSavedSearchName(uniqid("SavedSearch",true));
        
        $searchAttributes = $editSearchDto->getSearchAttributes();
        $searchAttributes["contact_types"] = "";
        $editSearchDto->setSearchAttributes($searchAttributes);
        
        $editSearch = $this->savedService->editSavedSearches($editSearchDto);
        
        $this->assertEquals($this->organization, $editSearch->getOrganizationId());
        $this->assertEquals($savedSearch->getSavedSearchId(), $editSearch->getSavedSearchId());
        $this->assertNotEmpty($editSearch->getPersonId());
        $this->assertNotEmpty($editSearch->getSearchAttributes());
    }
}