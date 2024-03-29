<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\TalkingPointsLang;

/**
 * TalkingPointsLangRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TalkingPointsLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:TalkingPointsLang';

    /**
     *
     * @param TalkingPointsLang $talkingPointsLang            
     * @return TalkingPointsLang
     */
    public function create(TalkingPointsLang $talkingPointsLang)
    {
        $em = $this->getEntityManager();
        $em->persist($talkingPointsLang);
        return $talkingPointsLang;
    }

    /**
     *
     * @param TalkingPointsLang $talkingPointsLang            
     */
    public function remove(TalkingPointsLang $talkingPointsLang)
    {
        $em = $this->getEntityManager();
        $em->remove($talkingPointsLang);
    }
}