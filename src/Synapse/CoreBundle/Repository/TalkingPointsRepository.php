<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\TalkingPoints;

/**
 * TalkingPointsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TalkingPointsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:TalkingPoints';

    /**
     *
     * @param TalkingPoints $talkingPoints            
     * @return TalkingPoints
     */
    public function create(TalkingPoints $talkingPoints)
    {
        $em = $this->getEntityManager();
        $em->persist($talkingPoints);
        return $talkingPoints;
    }

    /**
     *
     * @param TalkingPoints $talkingPoints            
     */
    public function remove(TalkingPoints $talkingPoints)
    {
        $em = $this->getEntityManager();
        $em->remove($talkingPoints);
    }

    public function getAllTalkingPoints($langId = 1)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('EQL.questionText,TP.minRange,TP.maxRange,TP.type,TP.talkingPointsType,IDENTITY(TP.ebiQuestion) as qusetonid
            ,IDENTITY(TP.ebiMetadata) as metadateid,EM.key as itemName,EML.metaName,TPL.description')
            ->from('SynapseCoreBundle:TalkingPoints', 'TP')
            ->LEFTJoin('SynapseCoreBundle:TalkingPointsLang', 'TPL', \Doctrine\ORM\Query\Expr\Join::WITH, 'TP.id = TPL.talkingPoints')
            ->LEFTJoin('SynapseCoreBundle:EbiQuestion', 'EQ', \Doctrine\ORM\Query\Expr\Join::WITH, 'TP.ebiQuestion = EQ.id')
            ->LEFTJoin('SynapseCoreBundle:EbiQuestionsLang', 'EQL', \Doctrine\ORM\Query\Expr\Join::WITH, 'EQ.id = EQL.ebiQuestion')
            ->LEFTJoin('SynapseCoreBundle:EbiMetadata', 'EM', \Doctrine\ORM\Query\Expr\Join::WITH, 'TP.ebiMetadata = EM.id')
            ->LEFTJoin('SynapseCoreBundle:EbiMetadataLang', 'EML', \Doctrine\ORM\Query\Expr\Join::WITH, 'EM.id = EML.ebiMetadata')
            ->where('EQL.lang = :lang OR EQL.lang IS NULL')
            ->where('EML.lang = :lang OR EML.lang IS NULL')
            ->where('TPL.languageMaster = :lang OR TPL.languageMaster IS NULL')
            ->orderBy('TP.createdAt', 'desc')
            ->setParameters(array(
            'lang' => $langId
        ));
        
        $qb = $qb->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }
}