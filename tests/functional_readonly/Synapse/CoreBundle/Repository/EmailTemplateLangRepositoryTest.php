<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EmailTemplateLangRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Exception\SynapseException;

class EmailTemplateLangRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var EmailTemplateLangRepository
     */
    private $emailTemplateLangRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->emailTemplateLangRepository = $this->repositoryResolver->getRepository(EmailTemplateLangRepository::REPOSITORY_KEY);
    }

    public function testGetEmailTemplateByKey()
    {
        $this->specify('Test getting email template by key for templates that should exist', function ($emailTemplateKey, $languageId, $expectedId) {
            $resultEmailTemplateLangObject = $this->emailTemplateLangRepository->getEmailTemplateByKey($emailTemplateKey, $languageId);
            verify($resultEmailTemplateLangObject)->notEmpty();
            verify($resultEmailTemplateLangObject->getId())->equals($expectedId);
            verify($resultEmailTemplateLangObject->getEmailTemplate()->getEmailKey())->equals($emailTemplateKey);

        }, ['examples' =>
            [
                ['Email_PDF_Report_Student', 1, 41],
                ['Academic_Update_Cancel_to_Faculty', 1, 22],
                ['Academic_Update_Notification_Student', 1, 25],
                ['Academic_Update_Reminder_to_Faculty', 1, 23],
                ['Academic_Update_Request_Staff', 1, 24],
                ['Academic_Update_Request_Staff_Closed', 1, 40],
                ['AcademicUpdate_Upload_Notification', 1, 26],
                ['Accept_Change_Request', 1, 32]
            ]
        ]);

        $this->specify('Test getting email template by key for templates that should not exist throws an exception', function ($emailTemplateKey, $languageId, $exceptionClass, $exceptionMessage) {
            try {
                $this->emailTemplateLangRepository->getEmailTemplateByKey($emailTemplateKey, $languageId);
            } catch (SynapseException $exception) {
                verify($exception)->isInstanceOf($exceptionClass);
                verify($exception->getMessage())->equals($exceptionMessage);
            }
        }, ['examples' =>
            [
                ['Bob_Is_Your_Uncle', 1, 'Synapse\CoreBundle\Exception\SynapseDatabaseException', "Email template for key Bob_Is_Your_Uncle not found in email_template"],
                ['Academic_Update_Cancel_to_Faculty', 41451510, 'Synapse\CoreBundle\Exception\SynapseDatabaseException', 'Language 41451510 not found in language_master'],
            ]
        ]);

    }
}