<?php
use Codeception\Util\Stub;
use Synapse\HelpBundle\EntityDto\HelpDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class HelpServiceUpdateTest extends \Codeception\TestCase\Test
{

    private $helpService;

    private $orgId = 1;
    
    private $coordinatorId = 1;

    private $testTitle = 'Test Help1';

    private $testDesc = 'Test Help Description1';

    private $testLink = 'http://linktest.com';
    
    private $validHelpFile = 1;
    
    private $invalidHelp = -1;
    
    private $invalidOrgId = -100;

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

    public function testUpdateHelp()
    {
        $help = $this->createHelpDto();
        $help = $this->helpService->createHelp($help, $this->orgId, $this->coordinatorId);
        $this->assertGreaterThan(0, $help->getId());
        $help->setId($help->getId());
        $help->setTitle('Test Help Updated');
        $help = $this->helpService->updateHelp($help, $this->orgId, $this->coordinatorId);
        
        $this->assertGreaterThan(0, $help->getId());
        $this->assertEquals('Test Help Updated', $help->getTitle());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateHelpInvalidId()
    {
        $help = $this->createHelpDto();
        $help = $this->helpService->createHelp($help, $this->orgId, $this->coordinatorId);
        $help->setTitle('Test Help Updated');
        $help->setId($this->invalidHelp);
        $help = $this->helpService->updateHelp($help, $this->orgId, $this->coordinatorId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateHelpInvalidOrgId()
    {
        $help = $this->createHelpDto();
        $help = $this->helpService->createHelp($help, $this->orgId, $this->coordinatorId);
        $help->setTitle('Test Help Updated');
        $help->setId($help->getId());
        $help = $this->helpService->updateHelp($help, $this->invalidOrgId, $this->coordinatorId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     **/
    public function testUpdateHelpInvalidHelp()
    {
    	$helpDto = $this->createHelpDto();
    	$help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
    	$this->assertGreaterThan(0, $help->getId());
    	$this->assertEquals($this->testTitle, $help->getTitle());
    	$this->assertEquals($this->testDesc, $help->getDescription());
    	$this->assertEquals($this->testLink, $help->getLink());
    	$invalidTitle = $this->getSpecifiedSizeText(81);
    	$help->setTitle($invalidTitle);
    	$help = $this->helpService->updateHelp($help, $this->orgId, $this->coordinatorId);
    	
    }
    
    private function getSpecifiedSizeText($length = 10){
    	$randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, $length);
    	return $randomString;
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     **/
    public function testUpdateInvalidHelpFile()
    {
        $uploadFile = rand().'.txt';
        $invalidTitle = $this->getSpecifiedSizeText(81);
        $help = $this->helpService->updateHelpDoc($this->validHelpFile,$invalidTitle, $this->testDesc, $uploadFile, 'uploads',$this->orgId, $this->coordinatorId);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     **/
    public function testUpdateInvalidHelpFileId()
    {
    	$uploadFile = rand().'.txt';
    	$help = $this->helpService->updateHelpDoc($this->invalidHelp,$this->testTitle, $this->testDesc, $uploadFile, 'uploads',$this->orgId, $this->coordinatorId);
    }
    
}
