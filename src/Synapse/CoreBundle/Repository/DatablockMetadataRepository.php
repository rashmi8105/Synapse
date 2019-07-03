<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\DatablockMetadata;
use Synapse\CoreBundle\Repository\SynapseRepository;

class DatablockMetadataRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:DatablockMetadata';

    public function removeDatablockMap($datablockMetadata)
    {
        $em = $this->getEntityManager();
        $em->remove($datablockMetadata);
    }

    public function getMappedBlocks($blockId, $langId, $exclude, $excludeType )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('em.id', 'em.modifiedAt as modified_at', 'em.key as item_label', 'eml.metaDescription as item_subtext','eml.metaName as display_name', 'em.metadataType as item_data_type', 'em.sequence as sequence_no', 'em.noOfDecimals as decimal_points', 'em.minRange as min_digits', 'em.maxRange as max_digits','em.scope as calendar_assignment');
        $qb->from('SynapseCoreBundle:DatablockMetadata', 'dm');
        $qb->join('SynapseCoreBundle:EbiMetadata', 'em', \Doctrine\ORM\Query\Expr\Join::WITH, 'dm.ebiMetadata = em.id');
        $qb->join('SynapseCoreBundle:EbiMetadataLang', 'eml', \Doctrine\ORM\Query\Expr\Join::WITH, 'eml.ebiMetadata = em.id');
        $qb->where('dm.datablock = :datablock AND eml.lang = :lang');
        if ($exclude == "text") {
            $qb->andWhere('em.metadataType != :metadataType');
            if($excludeType){
                $qb->andWhere('(em.scope != :excludetype OR em.scope IS NULL )');
                $paramArr = array(
                    'datablock' => $blockId,
                    'lang' => $langId,
                    'metadataType' => 'T',
                    'excludetype' =>  $excludeType
                );
            }else{
                $paramArr = array(
                    'datablock' => $blockId,
                    'lang' => $langId,
                    'metadataType' => 'T'
                );

            }

            $qb->setParameters($paramArr);
        } else {

            if($excludeType){
                $qb->andWhere('(em.scope != :excludetype OR em.scope IS NULL )');
                $paramArr = array(
                    'datablock' => $blockId,
                    'lang' => $langId,
                    'excludetype' =>  $excludeType
                );
            }else{
                $paramArr = array(
                    'datablock' => $blockId,
                    'lang' => $langId
                );
            }
            $qb->setParameters($paramArr);
        }

        $query = $qb->getQuery();
        return $query->getArrayResult();
    }

}