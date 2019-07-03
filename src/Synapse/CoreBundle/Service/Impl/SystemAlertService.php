<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\SystemAlerts;
use Synapse\CoreBundle\Service\SystemAlertServiceInterface;
use Synapse\RestBundle\Entity\SystemAlertDto;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("systemAlert_service")
 */
class SystemAlertService extends AbstractService implements SystemAlertServiceInterface
{

    const SERVICE_KEY = 'systemAlert_service';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const SYSTEM_ALERT_REPO = 'SynapseCoreBundle:SystemAlerts';

    /**
     *
     * @var systemAlertRepository
     */
    private $systemAlertRepository;
	
	   /**
     *
     * @var container
     */
    private $container;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\SystemAlertServiceInterface::createSystemAlert()
     */
    public function createSystemAlert(SystemAlertDto $systemAlertDto)
    {
	    $logContent = $this->container->get('loggerhelper_service')->getLog($systemAlertDto);
        $this->logger->debug(" Creating System Alert -  " . $logContent);
        
        $this->systemAlertRepository = $this->repositoryResolver->getRepository(self::SYSTEM_ALERT_REPO);
        $systemAlert = new SystemAlerts();
        $systemAlert->setDescription($systemAlertDto->getMessage());
        $isEnable = (int) $systemAlertDto->getIsEnabled();
        $systemAlert->setIsEnabled($isEnable);
        /**
         * if Start Date OR End Data not set, time automatically take now now+30
         */
        if (is_null($systemAlertDto->getStartDateTime()) || is_null($systemAlertDto->getEndDateTime())) {
            $startDate = new \DateTime('now');
            $endDate = new \DateTime('now');
            $endDate->add(new \DateInterval('P0DT0H30M0S'));
            $startDate->setTimezone(new \DateTimeZone('UTC'));
            $systemAlert->setStartDate($startDate);
            $endDate->setTimezone(new \DateTimeZone('UTC'));
            $systemAlert->setEndDate($endDate);
        } else {
            $startDate = $systemAlertDto->getStartDateTime();
            $startDate->setTimezone(new \DateTimeZone('UTC'));
            $systemAlert->setStartDate($startDate);
            
            $endDate = $systemAlertDto->getEndDateTime();
            $endDate->setTimezone(new \DateTimeZone('UTC'));
            $systemAlert->setEndDate($endDate);
        }
        $this->getSystemAlertRepository()->createSystemAlert($systemAlert);
        $this->systemAlertRepository->flush();
        $this->logger->info("create System Alert");
        return $systemAlert;
    }

    public function getSystemAlertRepository()
    {
        $this->logger->info("get SystemAlert Repo");
        $this->systemAlertRepository = $this->repositoryResolver->getRepository(self::SYSTEM_ALERT_REPO);
        return $this->systemAlertRepository;
    }
} 