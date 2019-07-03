<?php
namespace Synapse\CoreBundle\Service;

interface LanguageMasterServiceInterface
{

    public function getLanguageById($langid);

    /**
     *
     * @param int $langId            
     */
    public function getLangReferance($langId);
}