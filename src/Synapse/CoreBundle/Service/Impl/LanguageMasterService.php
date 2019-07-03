<?php
namespace Synapse\CoreBundle\Service\Impl;

use Synapse\CoreBundle\Service\LanguageMasterServiceInterface;
use Synapse\CoreBundle\Entity\LanguageMaster;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("lang_service")
 */
class LanguageMasterService extends AbstractService implements LanguageMasterServiceInterface
{

    const SERVICE_KEY = 'lang_service';

    /**
     *
     * @var orgRepository
     */
    private $langRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->langRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:LanguageMaster");
    }

    public function getLangRepository()
    {
        $this->logger->info(">>>> Get Lang Repository");
        return $this->langRepository;
    }

    public function getLanguageById($langid)
    {
        $this->logger->debug(">>>> Get Language  By Id for" . $langid);
        $language = $this->langRepository->find($langid);
        return $language;
    }

    public function getLangReferance($langId)
    {
        $this->logger->info(">>>> get Language Reference for given Lang");
        return $this->langRepository->getLangReferance($langId);
    }
}
