<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\RestBundle\Entity\EmailDto;

class BulkEmailJob extends ContainerAwareJob
{

    public function run($args)
    {
       
        $emailDto = unserialize($args['emailDto']);
       
        $emailService = $this->getContainer()->get('email_activity_service');
        $logger = $this->getContainer()->get('logger');
        try {
            $emailService->createEmail($emailDto, true);
        } catch (\Exception $e) {
            $logger->error("Email Creation Failed" . $e->getMessage());
        }
        $this->flushSpooler();
    }
    
    public function flushSpooler()
    {
        $mailer = $this->getContainer()->get('mailer');
        $transport = $mailer->getTransport();
        if (! $transport instanceof \Swift_Transport_SpoolTransport) {
            return;
        }
        $spool = $transport->getSpool();
        if (! $spool instanceof \Swift_MemorySpool) {
            return;
        }
    
        $spool->flushQueue($this->getContainer()
            ->get('swiftmailer.transport.real'));
    }
}