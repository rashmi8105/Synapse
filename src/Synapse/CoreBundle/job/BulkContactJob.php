<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\RestBundle\Entity\ReferralsDTO;

class BulkContactJob extends ContainerAwareJob
{

    public function run($args)
    {
       
        $contactDto = unserialize($args['contactDto']);
        $contactService = $this->getContainer()->get('contacts_service');
        $logger = $this->getContainer()->get('logger');
        try {
            $contactService->createContact($contactDto, true);
        } catch (\Exception $e) {
            $logger->error("Contact Creation Failed" . $e->getMessage());
        }
    }
}