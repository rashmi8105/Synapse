<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\EmailTemplateLang;
use Synapse\CoreBundle\Entity\LanguageMaster;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RestBundle\Exception\ValidationException;

class EmailTemplateLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:EmailTemplateLang';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $emailLangRepository = "SynapseCoreBundle:EmailTemplateLang";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $emailRepository = "SynapseCoreBundle:EmailTemplate";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $langRepository = "SynapseCoreBundle:LanguageMaster";

    /**
     * Doctrine helper for finding an EmailTemplateLang object by given criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return EmailTemplateLang|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * TODO:Move this function to a service.
     *
     * @param string $emailKey
     * @param int $languageId
     * @return EmailTemplateLang|null
     */
    public function getEmailTemplateByKey($emailKey, $languageId = 1)
    {
        $em = $this->getEntityManager();

        $language = $em->getRepository(LanguageMaster::REPOSITORY_KEY)->find($languageId);
        if (!$language) {
            throw new SynapseDatabaseException("Language $languageId not found in language_master");
        }

        $emailTemplate = $em->getRepository(EmailTemplateRepository::REPOSITORY_KEY)->findOneBy(['emailKey' => $emailKey]);
        if (!$emailTemplate) {
            throw new SynapseDatabaseException("Email template for key $emailKey not found in email_template");
        }

        $emailTemplateLang = $this->findOneBy(['emailTemplate' => $emailTemplate, 'language' => $language]);
        if (!$emailTemplateLang) {
            throw new SynapseDatabaseException("Email template language for key $emailKey not found in email_template_lang");
        }

        return $emailTemplateLang;

    }

}