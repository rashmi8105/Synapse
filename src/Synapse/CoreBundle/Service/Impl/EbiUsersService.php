<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Service\EbiUsersServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("ebiusers_service")
 */
class EbiUsersService extends AbstractService implements EbiUsersServiceInterface
{

    const SERVICE_KEY = 'ebiusers_service';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger")
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    public function hasEbiUsersAccess($loggedInUser)
    {
        $this->logger->info(">>>>Has EBI Users Access");
    }
}