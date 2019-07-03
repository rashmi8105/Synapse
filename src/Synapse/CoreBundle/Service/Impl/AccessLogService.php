<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\AccessLog;
use Synapse\RestBundle\Entity\AccessLogDto;
use Synapse\CoreBundle\Service\AccessLogServiceInterface;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("accesslog_service")
 */
class AccessLogService extends AbstractService implements AccessLogServiceInterface
{

    const SERVICE_KEY = 'accesslog_service';

    /**
     *
     * @var accessLogRepository
     */
    private $accessLogRepository;

    private $orgService;

    private $personService;

    /**
     *
     * @var container
     */
    private $container;

    const ACCESSLOG_REPO = "SynapseCoreBundle:AccessLog";

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "orgService" = @DI\Inject("org_service"),
     *            "personService" = @DI\Inject("person_service"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $orgService, $personService, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->orgService = $orgService;
        $this->personService = $personService;
        $this->container = $container;
    }

    public function getAccessLogRepository()
    {
        $this->logger->info(">>>> Get AccessLog Repository");
        $this->accessLogRepository = $this->repositoryResolver->getRepository(self::ACCESSLOG_REPO);
        return $this->accessLogRepository;
    }

    public function createAccessLog(AccessLogDto $accessLogDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($accessLogDto);
        $this->logger->debug("Creating Access Log " . $logContent);
        
        $this->accessLogRepository = $this->repositoryResolver->getRepository(self::ACCESSLOG_REPO);
        $accesslog = new AccessLog();
        $organization = $this->orgService->find($accessLogDto->getOrganization());
        $accesslog->setOrganization($organization);
        
        $person = $this->personService->findPerson($accessLogDto->getPerson());
        
        $accesslog->setSourceIP($accessLogDto->getSourceip());
        $accesslog->setPerson($person);
        $accesslog->setEvent($accessLogDto->getEvent());
        $accesslog->setEventId($accessLogDto->getEventId());
        
        // Setting datetime in UTC format
        $accessDateTime = new \DateTime('now');
        $accessDateTime->setTimezone(new \DateTimeZone('UTC'));
        $accesslog->setDateTime($accessDateTime);
        
        $accesslog->setBrowser($accessLogDto->getBrowser());
        $accesslog->setUserToken($accessLogDto->getUserToken());
        $accesslog->setApiToken($accessLogDto->getApiToken());
        
        $log = $this->accessLogRepository->createAccessLog($accesslog);
        $this->getAccessLogRepository()->flush();
        $this->logger->info(">>>> Created Access Log");
        
        return $log;
    }
}