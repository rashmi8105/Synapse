<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\OrgQuestionOptions;


class OrgQuestionOptionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgQuestionOptions';

    /**
     * Override find() to use PHPTyping
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|OrgQuestionOptions
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Override FindBy To Use PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgQuestionOptions[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    /**
     * Override FindOneBy To Use PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgQuestionOptions|null 
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


}