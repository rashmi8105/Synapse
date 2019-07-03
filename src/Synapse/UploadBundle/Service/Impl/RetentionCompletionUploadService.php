<?php
namespace Synapse\UploadBundle\Service\Impl;

use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * RetentionCompletionUploadService
 *
 * @DI\Service("retention_completion_upload_service")
 */
class RetentionCompletionUploadService extends AbstractService
{

    const SERVICE_KEY = 'retention_completion_upload_service';

    //scaffolding
    /**
     * @var Container
     */
    private $container;


    //repositories

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;

    /**
     * RetentionCompletionUploadService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;

        //Repositories
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
    }

    /**
     * Gets the data for the Retention and Completion Download.
     *
     * @param integer $organizationId
     * @return array
     */
    public function getRetentionCompletionDownloadData($organizationId)
    {

        $retentionCompletionData = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionCompletionVariablesByOrganization($organizationId);
        return $retentionCompletionData;
    }

}