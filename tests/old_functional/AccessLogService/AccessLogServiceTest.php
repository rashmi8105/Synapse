<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\SystemAlertDto;
use Synapse\RestBundle\Entity\AccessLogDto;

class AccessLogServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\AccessLogService
     */
    private $accessLogService;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->accessLogService = $this->container
            ->get('accesslog_service');
    }

    protected function createAccessLog()
    {
        $createAccessLogDto = new AccessLogDto();

        $createAccessLogDto->setOrganization(1);
        $createAccessLogDto->setBrowser('mozila');
        $createAccessLogDto->setEvent('setvice_test');
        $createAccessLogDto->setPerson(1);
        $accessDateTime = new \DateTime('now');
        $accessDateTime->setTimezone(new \DateTimeZone('UTC'));
        $createAccessLogDto->setDateTime($accessDateTime);
        $createAccessLogDto->setUserToken("user token");
        $createAccessLogDto->setApiToken("api token");
        return $createAccessLogDto;
    }


    public function testCreateAccessLog()
    {
        $createAccessLog = $this->createAccessLog();
        $accessLog = $this->accessLogService->createAccessLog($createAccessLog);
        $this->assertEquals(1, $accessLog->getOrganization()->getId());
        $this->assertEquals("mozila", $accessLog->getBrowser());
        $this->assertEquals("setvice_test", $accessLog->getEvent());
        $this->assertEquals(1, $accessLog->getPerson()->getId());
        $this->assertEquals("user token", $accessLog->getUserToken());
        $this->assertEquals("api token", $accessLog->getApiToken());
        $this->assertInstanceOf('Synapse\CoreBundle\Entity\AccessLog', $accessLog);
    }


    /**
     * {@inheritDoc}
     */
    protected function _after()
    {

    }
} 