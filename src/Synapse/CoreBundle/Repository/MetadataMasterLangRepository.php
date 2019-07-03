<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\MetadataMasterLang;
class MetadataMasterLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = "SynapseCoreBundle:MetadataMasterLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const METADATAMASTERLANG_REPO = "SynapseCoreBundle:MetadataMasterLang";
    
    const METADESC_ITEMSUBTXT = 'm.metaDescription as item_subtext';
    
    const METADATATYPE_ITEMDATATYPE =  's.metadataType as item_data_type';
    
    const SEQUENCE_SEQUENCENO = 's.sequence as sequence_no';
    
    const MODIFIED_AT = 's.modifiedAt as modified_at';
    
    const METANAME_ITEMLABEL = 'm.metaName as item_label';
    
    const METADATA  = 'm.metadata';
    
    const METADATA_EQUAL_ID = 'm.metadata = s.id';

    const DEF_TYPE = 'definitionType';
    
    const SEQUENCE = 's.sequence';
    /**
     *
     * @param MetadataMasterLang $metadataMasterLang
     * @return MetadataMasterLang
     */
    public function create(MetadataMasterLang $metadataMasterLang)
    {
        $em = $this->getEntityManager();
        $em->persist($metadataMasterLang);
        return $metadataMasterLang;
    }
    /**
     *
     * @param unknown $definitionType
     * @param unknown $organizationid
     */
    public function getProfiles($definitionType, $organizationid)
    {
        $em = $this->getEntityManager();
        if ($definitionType == 'E') {
            $qb = $em->createQueryBuilder()
            ->select('s.id', self::MODIFIED_AT, self::METANAME_ITEMLABEL, self::METADESC_ITEMSUBTXT, self::METADATATYPE_ITEMDATATYPE, self::SEQUENCE_SEQUENCENO)
            ->from(self::METADATAMASTERLANG_REPO, 'm')
            ->leftJoin(self::METADATA, 's')
            ->where(self::METADATA_EQUAL_ID)
            ->where('s.definitionType = :definitionType AND s.deletedBy IS NULL')
            ->setParameters(array(
                    self::DEF_TYPE => $definitionType
            ))
            ->orderBy(self::SEQUENCE, 'asc')
            ->getQuery();
        } elseif ($definitionType == 'A') {
            $qb = $em->createQueryBuilder()
            ->select(
                    's.id',
                    self::MODIFIED_AT,
                    self::METANAME_ITEMLABEL,
                    self::METADESC_ITEMSUBTXT,
                    self::METADATATYPE_ITEMDATATYPE,
                    self::SEQUENCE_SEQUENCENO,
                    's.noOfDecimals as no_of_decimals',
                    's.minRange as min_range',
                    's.maxRange as max_range'
            )
            ->from(self::METADATAMASTERLANG_REPO, 'm')
            ->leftJoin(self::METADATA, 's')
            ->where(self::METADATA_EQUAL_ID)
            ->where('s.organization = :organizationid OR s.definitionType = :definitionType AND s.deletedBy IS NULL')
            ->setParameters(array(
                    'organizationid' => $organizationid,
                    self::DEF_TYPE => 'E'
            ))
            ->orderBy(self::SEQUENCE, 'asc')
            ->getQuery();
        } else {
            $qb = $em->createQueryBuilder()
            ->select('s.id', self::MODIFIED_AT, self::METANAME_ITEMLABEL, self::METADESC_ITEMSUBTXT, self::METADATATYPE_ITEMDATATYPE, self::SEQUENCE_SEQUENCENO)
            ->from(self::METADATAMASTERLANG_REPO, 'm')
            ->leftJoin(self::METADATA, 's')
            ->where(self::METADATA_EQUAL_ID)
            ->where('s.organization = :organizationid AND s.definitionType = :definitionType AND s.deletedBy IS NULL')
            ->setParameters(array(
                    'organizationid' => $organizationid,
                    self::DEF_TYPE => $definitionType
            ))
            ->orderBy(self::SEQUENCE, 'asc')
            ->getQuery();
        }

        return $qb->getResult();
    }

    public function remove(MetadataMasterLang $metadataMasterLang)
    {
        $em = $this->getEntityManager();
        $em->remove($metadataMasterLang);

    }
}