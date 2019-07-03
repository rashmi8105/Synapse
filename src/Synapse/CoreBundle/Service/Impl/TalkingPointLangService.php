<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\ContactsServiceInterface;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Entity\TalkingPointsLang;
use Synapse\RestBundle\Entity\TalkingPointsLangResponseDto;
use Synapse\CoreBundle\Entity\TalkingPoints;

/**
 * @DI\Service("talkingpointlang_service")
 */
class TalkingPointLangService extends AbstractService
{

    const SERVICE_KEY = 'talkingpointlang_service';

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->logger = $logger;
    }

    public function saveTalkingPoints($talkingPointsIDs, $descArr)
    {
        $this->logger->info(" Saving Talking Points by Talking Point Ids");
        $this->talkingPoints = $this->repositoryResolver->getRepository("SynapseCoreBundle:TalkingPoints");
        $this->talkingPointsLang = $this->repositoryResolver->getRepository("SynapseCoreBundle:TalkingPointsLang");
        $talkingPointsLangIDs = [];
        foreach ($talkingPointsIDs as $key => $talkingPointsID) {
           /* $talkingPointsLangObj = $this->talkingPointsLang->findOneBy(array(
                'talkingPoints' => $talkingPointsID
            ));*/
            //$talkingPointsLang = (! $talkingPointsLangObj) ? new TalkingPointsLang() : $talkingPointsLangObj;
            $talkingPointsLang = new TalkingPointsLang();
            $TalkingPointsID = $this->talkingPoints->findOneBy(array(
                'id' => $talkingPointsID
            ));
            
            /*
             * Added to save the language
             */
            $this->languageObj = $this->repositoryResolver->getRepository("SynapseCoreBundle:LanguageMaster");
            $langObj = $this->languageObj->findOneByLangcode('en_US');
            if($langObj){
                $talkingPointsLang->setLanguageMaster($langObj);
            }
            /*
             * End of changes to add Language
            */
            $talkingPointsLang->setTalkingPoints($TalkingPointsID);
            $talkingPointsLang->setDescription($descArr[$key]);
            (! $talkingPointsLangObj) ? $this->talkingPointsLang->persist($talkingPointsLang, false) : $this->talkingPointsLang->update($talkingPointsLang);
            $talkingPointsLangIDs[] = $talkingPointsLang->getID();
        }
      /*
       * Commented the below lines as we are saving into the talking points lang repo.
       */  
     //   $this->talkingPoints->flush();
     //   $this->talkingPoints->clear();
        
        $this->talkingPointsLang->flush();
        $this->talkingPointsLang->clear();
        $this->logger->info(" Saved Talking Points by Talking Point Ids");
        return $talkingPointsLangIDs;
    }
} 
