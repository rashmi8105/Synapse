<?php
namespace Synapse\CoreBundle\Repository;


use Synapse\CoreBundle\Entity\ActivityCategory;

class ActivityCategoryRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:ActivityCategory';

    /**
     * Override function for PHP Typing
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null | ActivityCategory
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Override function for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return ActivityCategory[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Override function for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|ActivityCategory
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITYCATEGORYLANG_REPO = "SynapseCoreBundle:ActivityCategoryLang";
    public function getActivityCategoryList($parent = NULL)
    {
        $resultSet = array();
        $em = $this->getEntityManager();
        if($parent == null){
            $qb = $em->createQueryBuilder()
            ->select('ac.id as group_item_key','acl.description as group_item_value')
            ->from('SynapseCoreBundle:ActivityCategory', 'ac')
            ->LEFTJoin(self::ACTIVITYCATEGORYLANG_REPO,'acl',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'acl.activityCategoryId = ac.id')
                    ->where ( 'ac.parentActivityCategoryId is NULL' )
                    ->getQuery ();
            $resultSet = $qb->getResult();
        }
        else{
            $qb = $em->createQueryBuilder()
            ->select('ac.id as subitem_key','acl.description as subitem_value','IDENTITY(ac.parentActivityCategoryId) as parent')
            ->from('SynapseCoreBundle:ActivityCategory', 'ac')
            ->LEFTJoin(self::ACTIVITYCATEGORYLANG_REPO,'acl',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'acl.activityCategoryId = ac.id')
                    ->where ( 'ac.parentActivityCategoryId is NOT NULL' )
                    ->getQuery ();
            $resultSet = $qb->getResult();
        }
        return $resultSet;


    }

    public function getActivityCategoryNameById($activityId){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('ac.description as name')
        ->from(self::ACTIVITYCATEGORYLANG_REPO, 'ac')
        ->where ( 'ac.activityCategoryId = :activityId' )
        ->setParameters ( array (
                'activityId' => $activityId) )
                ->getQuery ();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

}