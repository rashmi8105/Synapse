<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ReferralRoutingRules;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReferralRoutingRulesRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:ReferralRoutingRules';

    /**
     * Override function for PHP Typing
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|ReferralRoutingRules
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
     * @return ReferralRoutingRules[]
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
     * @return null|ReferralRoutingRules
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


}