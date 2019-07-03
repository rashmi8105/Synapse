<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\EbiMetadataLang;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class EbiMetadataLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = "SynapseCoreBundle:EbiMetadataLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const EBIMETADATALANG_REPO = "SynapseCoreBundle:EbiMetadataLang";

    /**
     *
     * @param EbiMetadataLang $ebiMetadataLang
     * @return EbiMetadataLang
     */
    public function create(EbiMetadataLang $ebiMetadataLang)
    {
        $em = $this->getEntityManager();
        $em->persist($ebiMetadataLang);
        return $ebiMetadataLang;
    }

    public function getProfiles($status)
    {
        /*$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
            $qb->select('s.id','s.status','s.modifiedAt as modified_at','s.key as item_label', 'm.metaName as display_name', 'm.metaDescription as item_subtext', 's.metadataType as item_data_type', 's.noOfDecimals as decimal_points', 's.minRange as min_range', 's.maxRange as max_range', 's.sequence as sequence_no','CASE WHEN(dms.id IS NULL) THEN 0 ELSE dms.id END
             AS profile_block_id', 'CASE WHEN(dmsl.datablockDesc IS NULL) THEN \'\' ELSE dmsl.datablockDesc END as profile_block_name', 'pom.id as pom_id', 'au.id as au_id');
            $qb->from(self::EBIMETADATALANG_REPO, 'm');
            $qb->leftJoin('m.ebiMetadata', 's');
            $qb->leftJoin('SynapseCoreBundle:DatablockMetadata', 'dmdata', \Doctrine\ORM\Query\Expr\Join::WITH, 'dmdata.ebiMetadata = s.id');
            $qb->leftJoin('SynapseCoreBundle:DatablockMaster', 'dms', \Doctrine\ORM\Query\Expr\Join::WITH, 'dmdata.datablock = dms.id');
            $qb->leftJoin('SynapseCoreBundle:DatablockMasterLang', 'dmsl', \Doctrine\ORM\Query\Expr\Join::WITH, 'dmsl.datablock = dms.id');
            $qb->LEFTJoin('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata', 'au', \Doctrine\ORM\Query\Expr\Join::WITH, 'au.ebiMetadata = s.id');
            $qb->LEFTJoin('SynapseCoreBundle:PersonEbiMetadata', 'pom', \Doctrine\ORM\Query\Expr\Join::WITH, 'pom.ebiMetadata = s.id');

            $qb->where('m.ebiMetadata = s.id');
            $qb->where('s.definitionType = :definitionType AND s.deletedBy IS NULL');
            if(strtolower($status) == 'active'){
                $qb->andWhere('s.status = :status OR s.status IS NULL');
                $qb->setParameters(array(
                    'definitionType' => "E",
                    'status' => $status
                ));
            }elseif(strtolower($status) == 'archive'){
                $qb->andWhere('s.status = :status');
                $qb->setParameters(array(
                    'definitionType' => "E",
                    'status' => 'archived'
                ));
            }else{
                $qb->setParameters(array(
                    'definitionType' => "E"
                ));
            }
            $qb->orderBy('s.sequence', 'asc');
            $qb->groupBy('s.id');
            $query = $qb->getQuery();

        return $query->getArrayResult();  */

        $sql_where = "";

        if(strtolower($status) == 'active'){

            $sql_where = " AND (e0_.status = 'active' OR e0_.status IS NULL) ";

        }elseif(strtolower($status) == 'archive'){

            $sql_where = " AND (e0_.status = 'archived') ";
        }
        
        $sql = "
            SELECT 
                e0_.id,
                e0_.status AS status,
                e0_.modified_at AS modified_at,
                e0_.meta_key AS item_label,
                e1_.meta_name AS display_name,
                e1_.meta_description AS item_subtext,
                e0_.metadata_type AS item_data_type,
                e0_.definition_type AS definition_type,
                e0_.no_of_decimals AS decimal_points,
                e0_.min_range AS min_range,
                e0_.max_range AS max_range,
                e0_.sequence AS sequence_no,
                CASE
                    WHEN (d2_.id IS NULL) THEN 0
                    ELSE d2_.id
                END AS profile_block_id,
                CASE
                    WHEN (d3_.datablock_desc IS NULL) THEN ''
                    ELSE d3_.datablock_desc
                END AS profile_block_name,
                p4_.id AS pom_id,
                a5_.id AS au_id
            FROM
                ebi_metadata_lang e1_
                    LEFT JOIN
                ebi_metadata e0_ ON e1_.ebi_metadata_id = e0_.id
                    AND (e0_.deleted_at IS NULL)
                    LEFT JOIN
                datablock_metadata d6_ ON (d6_.ebi_metadata_id = e0_.id)
                    AND (d6_.deleted_at IS NULL)
                    LEFT JOIN
                datablock_master d2_ ON (d6_.datablock_id = d2_.id)
                    AND (d2_.deleted_at IS NULL)
                    LEFT JOIN
                datablock_master_lang d3_ ON (d3_.datablock_id = d2_.id)
                    AND (d3_.deleted_at IS NULL)
                    LEFT JOIN
                academic_update_request_metadata a5_ ON (a5_.ebi_metadata_id = e0_.id)
                    LEFT JOIN
                (SELECT 
                    ebi_metadata_id, id
                FROM
                    person_ebi_metadata
                GROUP BY ebi_metadata_id) p4_ ON (p4_.ebi_metadata_id = e0_.id)
            WHERE
                (e0_.definition_type = 'E'
                    AND e0_.deleted_by IS NULL)
                    AND (e1_.deleted_at IS NULL)
                    $sql_where
            GROUP BY e0_.id
            ORDER BY e0_.sequence
            ";
        try{
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);
        }catch(\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();
        return $records;
    }

    public function remove(EbiMetadataLang $ebiMetadataLang)
    {
        $em = $this->getEntityManager();
        $em->remove($ebiMetadataLang);
    }
}