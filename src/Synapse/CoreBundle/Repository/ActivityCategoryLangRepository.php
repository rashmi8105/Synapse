<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ActivityCategoryLang;

class ActivityCategoryLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:ActivityCategoryLang';

    /**
     * @param array $criteria
     * @param array|null|null $orderBy
     * @return ActivityCategoryLang|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


}