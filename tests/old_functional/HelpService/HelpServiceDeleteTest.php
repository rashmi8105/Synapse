<?php
use Codeception\Util\Stub;
use Synapse\HelpBundle\EntityDto\HelpDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class HelpServiceDeleteTest extends \Codeception\TestCase\Test
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

    public function createHelpDto()
    {
        $help = new HelpDto();
        $help->setTitle($this->testTitle);
        $help->setDescription($this->testDesc);
        $help->setLink($this->testLink);
        return $help;
    }

    public function testDeleteHelp()
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        $deleteHelp = $this->helpService->deleteHelp($this->orgId, $help->getId(), $this->coordinatorId);
        $this->assertEmpty($deleteHelp);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteHelpWithInvalidId()
    {
        $deleteHelp = $this->helpService->deleteHelp($this->orgId, - 1, $this->coordinatorId);
        $this->assertEmpty($deleteHelp);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteHelpWithInvalidCoordId()
    
    {
        $helpDto = $this->createHelpDto();
        $help = $this->helpService->createHelp($helpDto, $this->orgId, $this->coordinatorId);
        $deleteHelp = $this->helpService->deleteHelp($this->orgId, $help->getId(), -1);
        $this->assertEmpty($deleteHelp);
    }
	
}
