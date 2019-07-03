<?php
use Codeception\Util\Stub;
use Synapse\CampusResourceBundle\EntityDto\CampusResourceDto;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;

class CampusResourceServiceTest extends \Codeception\TestCase\Test
{

    private $campusResourceId;

    private $campusResourceService;

    private $organizationId = 1;

    private $resourceName;

    private $staffId = 1;

    private $staffName = 'Alon Solly';

    private $resourcePhoneNumber = '9894865500';

    private $resourceEmail = 'dally.bab@gmail.com';

    private $resourceLocation = 'Mumbai';

    private $resourceUrl = 'http://facebook.com/dallybab';

    private $resourceDesc = 'Lorem ipsum dolor sit amet, consectetur adipising elit, sed do eiusmod tempor Incident';

    private $receiveReferals = 1;

    private $visibleToStudents = 1;

    private $studentId = 6;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->campusResourceService = $container->get('campusresource_service');
    }

    public function campusResourceDto()
    {
        $this->resourceName = uniqid("Academic Advising_", true);
        
        $resourceDto = new CampusResourceDto();
        $resourceDto->setOrganizationId($this->organizationId);
        $resourceDto->setResourceName($this->resourceName);
        $resourceDto->setStaffId($this->staffId);
        $resourceDto->setStaffName($this->staffName);
        $resourceDto->setResourcePhoneNumber($this->resourcePhoneNumber);
        $resourceDto->setResourceEmail($this->resourceEmail);
        $resourceDto->setResourceLocation($this->resourceLocation);
        $resourceDto->setResourceUrl($this->resourceUrl);
        $resourceDto->setResourceDescription($this->resourceDesc);
        $resourceDto->setReceiveReferals($this->receiveReferals);
        $resourceDto->setVisibleToStudents($this->visibleToStudents);
        return $resourceDto;
    }

    public function testCreateCampusResource()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $this->campusResourceId = $resource->getId();
        $this->assertGreaterThan(0, $this->campusResourceId);
        $this->assertEquals($this->organizationId, $resource->getOrganizationId());
        $this->assertEquals($this->resourceName, $resource->getResourceName());
        $this->assertEquals($this->staffId, $resource->getStaffId());
        $this->assertEquals($this->staffName, $resource->getStaffName());
        $this->assertEquals($this->resourcePhoneNumber, $resource->getResourcePhoneNumber());
        $this->assertEquals($this->resourceEmail, $resource->getResourceEmail());
        $this->assertEquals($this->resourceLocation, $resource->getResourceLocation());
        $this->assertEquals($this->resourceUrl, $resource->getResourceUrl());
        $this->assertEquals($this->resourceDesc, $resource->getResourceDescription());
        $this->assertEquals($this->receiveReferals, $resource->getReceiveReferals());
        $this->assertEquals($this->visibleToStudents, $resource->getVisibleToStudents());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateCreateCampusResourceAssingInvaliedOrgId()
    {
        $resourceDto = $this->campusResourceDto();
        $resourceDto->setOrganizationId(mt_rand());
        $this->campusResourceService->createCampusResource($resourceDto);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateCreateCampusResourceAssingInvaliedStaffId()
    {
        $resourceDto = $this->campusResourceDto();
        $resourceDto->setStaffId(mt_rand());
        $this->campusResourceService->createCampusResource($resourceDto);
    }

    public function testUpdateCampusResource()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $this->resourceName = uniqid("Academic Advising_", true);
        $resourceDto->setResourceName($this->resourceName);
        $updateResource = $this->campusResourceService->updateCampusResource($resourceDto, $resource->getId());
        
        $this->assertGreaterThan(0, $updateResource->getId());
        $this->assertEquals($this->resourceName, $updateResource->getResourceName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateCampusResourceInvalidId()
    {
        $resourceDto = $this->campusResourceDto();
        $updateResource = $this->campusResourceService->updateCampusResource($resourceDto, mt_rand());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateCampusResourceInvalidOrgId()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $resourceDto->setOrganizationId(mt_rand());
        $updateResource = $this->campusResourceService->updateCampusResource($resourceDto, $resource->getId());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateCampusResourceInvalidStaffId()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $resourceDto->setStaffId(mt_rand());
        $updateResource = $this->campusResourceService->updateCampusResource($resourceDto, $resource->getId());
    }

    public function testGetCampusResource()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $getResource = $this->campusResourceService->getCampusResources($this->organizationId);
        $resource = $getResource[0];
        $this->assertInternalType('object', $resource);
        $this->assertNotEmpty($resource);
        $this->assertObjectHasAttribute("resourceName", $resource);
        $this->assertObjectHasAttribute("organizationId", $resource);
        $this->assertObjectHasAttribute("staffId", $resource);
        $this->assertObjectHasAttribute("resourceEmail", $resource);
        $this->assertObjectHasAttribute("resourcePhoneNumber", $resource);
        $this->assertObjectHasAttribute("staffName", $resource);
        $this->assertObjectHasAttribute("receiveReferals", $resource);
        $this->assertObjectHasAttribute("visibleToStudents", $resource);
        $this->assertObjectHasAttribute("resourceUrl", $resource);
        $this->assertObjectHasAttribute("resourceLocation", $resource);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetCampusResourceInvalidOrgId()
    {
        $getResource = $this->campusResourceService->getCampusResources(mt_rand());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSingleCampusResourceDetailsInvalidResourceId()
    {
        $getResource = $this->campusResourceService->getCampusResourceDetails(mt_rand());
    }

    public function testGetSingleCampusResourceDetails()
    {
        $resourceDto = $this->campusResourceDto();
        $createResource = $this->campusResourceService->createCampusResource($resourceDto);
        $resource = $this->campusResourceService->getCampusResourceDetails($createResource->getId());
        $this->assertInternalType('object', $resource);
        $this->assertNotEmpty($resource);
        $this->assertObjectHasAttribute("resourceName", $resource);
        $this->assertObjectHasAttribute("organizationId", $resource);
        $this->assertObjectHasAttribute("staffId", $resource);
        $this->assertObjectHasAttribute("resourceEmail", $resource);
        $this->assertObjectHasAttribute("resourcePhoneNumber", $resource);
        $this->assertObjectHasAttribute("staffName", $resource);
        $this->assertObjectHasAttribute("receiveReferals", $resource);
        $this->assertObjectHasAttribute("visibleToStudents", $resource);
        $this->assertObjectHasAttribute("resourceUrl", $resource);
        $this->assertObjectHasAttribute("resourceLocation", $resource);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteCampusResourceWithInvalidId()
    {
        $deleteCampusResource = $this->campusResourceService->deleteCampusResource(mt_rand());
        $this->assertEmpty($deleteCampusResource);
    }

    public function testDeleteCampusResource()
    {
        $resourceDto = $this->campusResourceDto();
        $resource = $this->campusResourceService->createCampusResource($resourceDto);
        $deleteCampusResource = $this->campusResourceService->deleteCampusResource($resource->getId());
        $this->assertEmpty($deleteCampusResource);
    }

    public function testGetCampusResourceForStudent()
    {
        $resource = $this->campusResourceService->getCampusResourceForStudent($this->studentId);
        $this->assertInternalType('array', $resource);
        $this->assertNotEmpty($resource);
        $this->assertArrayHasKey("campus_resource_list", $resource);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetCampusResourceForStudenteWithInvalidId()
    {
        $resource = $this->campusResourceService->getCampusResourceForStudent(mt_rand());
        $this->assertEmpty($resource);
    }
}