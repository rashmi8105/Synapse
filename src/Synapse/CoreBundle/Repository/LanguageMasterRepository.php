<?php
// LanguageMasterRepository
namespace Synapse\CoreBundle\Repository;

class LanguageMasterRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:LanguageMaster';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const LANG_MASTER_ENT = 'SynapseCoreBundle:LanguageMaster';
    
    public function getLangReferance($langId)
    {
        $em = $this->getEntityManager();
        return $em->getReference(self::LANG_MASTER_ENT, $langId);
    }
}