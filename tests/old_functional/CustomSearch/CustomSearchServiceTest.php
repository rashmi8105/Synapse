<?php
use Codeception\Util\Stub;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Service\Impl\SearchService;

class CustomSearchServiceTest extends \Codeception\TestCase\Test
{

    private $container;

    private $customService;

    private $organization = 1;

    private $personId = 1;

    private $invalidPerson = - 1;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->searchService = $this->container->get('search_service');
    }

    public function testCreateCustomSearch()
    {
        $customSearchDto = $this->createCustomSearchDto();
        $customSearch = $this->searchService->createCustomSearch($customSearchDto, $this->personId, '', '');
     
        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchDto", $customSearch);
        $this->assertEquals($this->personId, $customSearch->getPersonId());
        $this->assertNotEmpty($customSearch->getSearchAttributes());
        $this->assertInternalType("array", $customSearch->getSearchAttributes());
    }

    private function createCustomSearchDto()
    {
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "all";
        $searchAttribute["contact_types"] = "all";
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    private function createCustomSearchDtoForCourse()
    {
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        /*$searchAttribute["courses"]['department_id'] = "IT";
        $searchAttribute["courses"]['subject_id'] = "SEC001";
        $searchAttribute["courses"]['course_ids'] = "0087";
        $searchAttribute["courses"]['section_ids'] = "SEC 1";*/
        
        $course1 = array();
        $course1['department_id'] = "IT";
        $course1['subject_id'] = "SEC001";
        $course1['course_ids'] = "0087";
        $course1['section_ids'] = "SEC 1";
        
        $searchAttribute["courses"][] = $course1;
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    public function testCreateCustomSearchForCourse()
    {
        $customSearchDto = $this->createCustomSearchDtoForCourse();
        $customSearch = $this->searchService->createCustomSearch($customSearchDto, $this->personId,'','');
        
        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchDto", $customSearch);
        $this->assertEquals($this->personId, $customSearch->getPersonId());
        $this->assertNotEmpty($customSearch->getSearchAttributes());
        $this->assertInternalType("array", $customSearch->getSearchAttributes());
    }

    private function createCustomSearchDtoForISP()
    {
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "";
        $searchAttribute["referral_status"] = "";
        $searchAttribute["contact_types"] = "";
        
        $course1 = array();
        $searchAttribute["courses"][] = $course1;
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
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    public function testCreateCustomSearchForISP()
    {
        $customSearchDto = $this->createCustomSearchDtoForISP();
        $customSearch = $this->searchService->createCustomSearch($customSearchDto, $this->personId,'','');
        
        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchDto", $customSearch);
        $this->assertEquals($this->personId, $customSearch->getPersonId());
        $this->assertNotEmpty($customSearch->getSearchAttributes());
        $this->assertInternalType("array", $customSearch->getSearchAttributes());
    }

    private function createCustomSearchDtoForEBI()
    {
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->organization);
        $searchAttribute["risk_indicator_ids"] = "1,2,3";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "'o','c'";
        $searchAttribute["contact_types"] = "1,2";
        
        $course1 = array();
        $searchAttribute["courses"][] = $course1;
        
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
        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    public function testCreateCustomSearchForEBI()
    {
        $customSearchDto = $this->createCustomSearchDtoForEBI();
        $customSearch = $this->searchService->createCustomSearch($customSearchDto, $this->personId,'','');
        
        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\SearchDto", $customSearch);
        $this->assertEquals($this->personId, $customSearch->getPersonId());
        $this->assertNotEmpty($customSearch->getSearchAttributes());
        $this->assertInternalType("array", $customSearch->getSearchAttributes());
    }
    
    public function testCreateCustomSearchForCSVDownload()
    {
    	$customSearchDto = $this->createCustomSearchDto();
    	$customSearch = $this->searchService->createCustomSearch($customSearchDto, $this->personId, '', '','any','','','csv');
    	$this->assertInternalType("array", $customSearch);
    	$this->assertEquals(array("You may continue to use Mapworks while your download completes. We will notify you when it is available."), $customSearch);
    }
}
