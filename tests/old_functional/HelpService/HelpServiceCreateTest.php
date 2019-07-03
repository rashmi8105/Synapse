<?php
use Codeception\Util\Stub;
use Synapse\HelpBundle\EntityDto\HelpDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class HelpServiceCreateTest extends \Codeception\TestCase\Test
{

    private $helpService;

    private $orgId = 1;

    private $coordinatorId = 1;

    private $testTitle = 'Test Help';

    private $testDesc = 'Test Help Description';

    private $testLink = 'http://linktest.com';

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->helpService = $container->get('help_service');
    }

    public function testCreateUpdateHelpFile()
    {
        $uploadFile = rand().'.txt';
        $help = $this->helpService->createHelpDoc($this->testTitle, $this->testDesc, $uploadFile, 'uploads',$this->orgId, $this->coordinatorId);
        $this->assertGreaterThan(0, $help->getId());
        $updateTitle = 'Test Help Updated';
        $help = $this->helpService->updateHelpDoc($help->getId(),$updateTitle, $this->testDesc, $uploadFile, 'uploads',$this->orgId, $this->coordinatorId);
        $this->assertGreaterThan(0, $help->getId());
        $this->assertEquals($updateTitle, $help->getTitle());
        $this->assertEquals($this->testDesc, $help->getDescription());
    }
    
    public function createHelpDto()
    {
        $help = new HelpDto();
        $help->setTitle($this->testTitle);
        $help->setDescription($this->testDesc);
        $help->setLink($this->testLink);
        return $help;
    }

    public function testCreateHelp()
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        $this->assertGreaterThan(0, $help->getId());
        $this->assertEquals($this->testTitle, $help->getTitle());
        $this->assertEquals($this->testDesc, $help->getDescription());
        $this->assertEquals($this->testLink, $help->getLink());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateHelpAssingInvaliedOrgId()
    {
        $this->helpService->createHelp($this->createHelpDto(), mt_rand(), $this->coordinatorId);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateHelpAssingInvaliedCoordId()
    {
        $this->helpService->createHelp($this->createHelpDto(), $this->orgId, mt_rand());
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    **/
    public function testCreateInvalidHelp()
    {
    	$helpDto = $this->createInvalidHelpDto();
    	$help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
    }
    
    private function createInvalidHelpDto()
    {
    	$help = new HelpDto();
    	$invalidTitle = $this->getSpecifiedSizeText(81);
    	$help->setTitle($invalidTitle);
    	$help->setDescription($this->testDesc);
    	$help->setLink($this->testLink);
    	return $help;
    }
    
    private function getSpecifiedSizeText($length = 10){
        $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, $length);
        return $randomString;
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
    **/
    public function testCreateInvalidHelpFile()
    {
        $uploadFile = rand().'.txt';
    	$invalidTitle = $this->getSpecifiedSizeText(81);
    	$help = $this->helpService->createHelpDoc($invalidTitle, $this->testDesc, $uploadFile, '/uploads',$this->orgId, $this->coordinatorId);
    }
}
