<?php

namespace Synapse\SurveyBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\SurveyBundle\Entity\QuestionBankMap;

class QuestionBankMapRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseSurveyBundle:QuestionBankMap';

    /**
     * Override find() to use PHP typing
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|QuestionBankMap
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Override findBy() to use PHP typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return QuestionBankMap[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset); // TODO: Change the autogenerated stub
    }


    /**
     * Finds a single entity by a set of criteria.
     * Override added to inform PhpStorm about the return type.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return QuestionBankMap|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


}