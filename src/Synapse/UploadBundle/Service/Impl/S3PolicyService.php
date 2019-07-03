<?php
namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\UploadBundle\Service\S3PolicyServiceInterface;
use Synapse\CoreBundle\Util\S3Helper;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * @DI\Service("s3_policy_service")
 */
class S3PolicyService extends AbstractService implements S3PolicyServiceInterface
{

    const SERVICE_KEY = 's3_policy_service';

    private $ebiConfigService;
    private $s3Helper;
    private $getExpireTime;
    private $putExpireTime;

    /**
     *
     * @param $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     * })
     */
    public function __construct($repositoryResolver, $logger, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->ebiConfigService = $ebiConfigService;
        $this->s3Helper = new S3Helper(
            $this->ebiConfigService->get('AWS_Key'),
            $this->ebiConfigService->get('AWS_Secret'),
            $this->ebiConfigService->get('AWS_Region'),
            $this->ebiConfigService->get('AWS_Bucket')
        );
        $this->getExpireTime = $this->ebiConfigService->get('AWS_Get_Expire_Time');
        $this->putExpireTime = $this->ebiConfigService->get('AWS_Put_Expire_Time');
    }

    public function getSecureUrl($file)
    {
        $secureUrl = $this->s3Helper->getSecureUrl($file, $this->getExpireTime);

        return $secureUrl;
    }

    public function getSecureUploadUrl($file)
    {
        $secureUrl = $this->s3Helper->getSecureUploadUrl($file, $this->putExpireTime);

        return $secureUrl;
    }

}