<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\SystemAlertDto;

class SystemAlertServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\CoreBundle\Service\SystemAlertService
     */
    private $systemAlertService;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->systemAlertService = $this->container->get('systemAlert_service');
    }

    protected function createTestSystemAlert()
    {
        $createSystemAlertDTO = new SystemAlertDto();

        $createSystemAlertDTO->setMessage("System Alert Description");
        $createSystemAlertDTO->setEndDateTime(new \DateTime("2014-09-12 06:29 AM"));
        $createSystemAlertDTO->setStartDateTime(new \DateTime("2014-09-12 06:29 PM"));
        return $createSystemAlertDTO;
    }

    public function testCreateSystemAlert()
    {
        $createSystemAlert = $this->createTestSystemAlert();
        $systemAlert = $this->systemAlertService->createSystemAlert($createSystemAlert);
        $this->assertObjectHasAttribute('startDate',$systemAlert);
        $this->assertEquals("System Alert Description", $systemAlert->getDescription());
        $this->assertObjectHasAttribute('startDate',$systemAlert);
        $this->assertObjectHasAttribute('endDate',$systemAlert);
        $this->assertObjectHasAttribute('isEnabled',$systemAlert);
    }


    public function testCreateSystemAlertNoStartDate()
    {
        $createSystemAlert = $this->createTestSystemAlert();
        $createSystemAlert->setStartDateTime(null);
        $systemAlert = $this->systemAlertService->createSystemAlert($createSystemAlert);
        $this->assertEquals("System Alert Description", $systemAlert->getDescription());
        $this->assertEquals(new \DateTime("now"),$systemAlert->getStartDate());
        $this->assertObjectHasAttribute('startDate',$systemAlert);
        $this->assertObjectHasAttribute('endDate',$systemAlert);
        $this->assertObjectHasAttribute('isEnabled',$systemAlert);
    }



    /**
     * {@inheritDoc}
     */
    protected function _after()
    {}
} 