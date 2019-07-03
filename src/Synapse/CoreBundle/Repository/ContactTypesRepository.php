<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ContactTypes;
use Synapse\CoreBundle\Entity\ContactTypesLang;

class ContactTypesRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:ContactTypes';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const CONTACTTYPELANG_REPO = "SynapseCoreBundle:ContactTypesLang";

    public function getContactTypeGroupList(){
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
        ->select('ct.id as group_item_key','ctl.description as group_item_value')
        ->from('SynapseCoreBundle:ContactTypes', 'ct')
        ->LEFTJoin(self::CONTACTTYPELANG_REPO,'ctl',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'ctl.contactTypesId = ct.id')
                ->where ( 'ct.isActive = 1' )
                ->where('ct.parentContactTypesId is NULL')
                ->orderBy('ct.displaySeq')
                ->getQuery ();
        $resultSet = $qb->getResult();
         
        return $resultSet;
    }

    Public function getContactTypeSubItemList(){
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
        ->select('ct.id as subitem_key','ctl.description as subitem_value','IDENTITY(ct.parentContactTypesId) as parent')
        ->from('SynapseCoreBundle:ContactTypes', 'ct')
        ->LEFTJoin(self::CONTACTTYPELANG_REPO,'ctl',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'ctl.contactTypesId = ct.id')
                ->where ( 'ct.isActive = 1' )
                ->where('ct.parentContactTypesId is NOT NULL')
                ->orderBy('ct.displaySeq')
                ->getQuery ();
        $resultSet = $qb->getResult();
         
        return $resultSet;
    }

    public function getContactTypeDescription($id){
         
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('ctl.description')
        ->from(self::CONTACTTYPELANG_REPO, 'ctl')
        ->where('ctl.contactTypesId = :id ')
        ->setParameters ( array (
                'id' => $id ))
                ->getQuery ();
        $resultSet = $qb->getResult();
        return $resultSet;
         
    }
}