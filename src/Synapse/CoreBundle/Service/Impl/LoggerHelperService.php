<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("loggerhelper_service")
 */
class LoggerHelperService extends AbstractService
{

    const SERVICE_KEY = 'loggerhelper_service';

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
        parent::__construct($repositoryResolver, $logger, $container);
        $this->container = $container;
    }
    
    public function getLog($obj)
    {
        $serializer = $this->container->get('jms_serializer');
        $logContent = $serializer->serialize($obj, 'json');
        return $logContent;
    }
}