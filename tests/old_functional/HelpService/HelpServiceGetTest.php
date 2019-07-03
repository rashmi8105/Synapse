<?php
use Codeception\Util\Stub;
use Synapse\HelpBundle\EntityDto\HelpDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class HelpServiceGetTest extends \Codeception\TestCase\Test
{

    private $helpService;

    private $orgId = 1;

    private $coordinatorId = 1;

    private $testTitle = 'Test Help';

    private $testDesc = 'Test Help Description';

    private $testLink = 'http://linktest.com';
    
    private $invalidOrgId = -20;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->helpService = $container->get('help_service');
    }

    public function createHelpDto()
    {
        $help = new HelpDto();
        $help->setTitle($this->testTitle);
        $help->setDescription($this->testDesc);
        $help->setLink($this->testLink);
        return $help;
    }

    public function testGetHelp()
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        
        $help = $this->helpService->getHelps($this->orgId);
        $help = $help[0];
        
        $this->assertArrayHasKey('id', $help);
        $this->assertArrayHasKey('title', $help);
        $this->assertArrayHasKey('description', $help);
        $this->assertArrayHasKey('type', $help);
        $this->assertArrayHasKey('link', $help);
    }

    public function testGetMapWorksSupportHelp()
    {
        $help = $this->helpService->getMapWorksSupportContact($this->orgId);
        
        $this->assertArrayHasKey('demo_site_url', $help);
        $this->assertArrayHasKey('training_site_url', $help);
        $this->assertArrayHasKey('campus_name', $help);
        $this->assertArrayHasKey('mapworks_contact', $help);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetMapWorksSupportHelpOrgId()
    {
    	$profiles = $this->helpService->getMapWorksSupportContact($this->invalidOrgId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetHelpInvalidOrgId()
    {
        $profiles = $this->helpService->getHelps($this->invalidOrgId);
    }

    public function testGetSingleHelp()
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        
        $helpDetails = $this->helpService->getHelpDetails($this->orgId, $help->getId());
        $helpDetails = $helpDetails[0];
        $this->assertArrayHasKey('id', $helpDetails);
        $this->assertArrayHasKey('title', $helpDetails);
        $this->assertArrayHasKey('description', $helpDetails);
        $this->assertArrayHasKey('type', $helpDetails);
        $this->assertArrayHasKey('link', $helpDetails);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSingleHelpInvaliedOrgId()
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        
        $this->helpService->getHelpDetails(mt_rand(), $help->getId());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetSingleHelpInvaliedHelpId()
    {
        $this->helpService->getHelpDetails($this->orgId, mt_rand());
    }
    
    public function testGetSingleHelpTypeFile()
    {
        $idFileType =1;
        $helpDetails = $this->helpService->getHelpDetails($this->orgId, $idFileType);
        $helpDetails = $helpDetails[0];
        $this->assertArrayHasKey('id', $helpDetails);
        $this->assertArrayHasKey('type', $helpDetails);
     }
}
