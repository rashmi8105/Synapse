<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\RestBundle\Entity\ReferralsDTO;

class BulkReferralJob extends ContainerAwareJob
{

    public function run($args)
    {
       
        $referralDto = unserialize($args['referralDto']);
       
        $referralService = $this->getContainer()->get('referral_service');
        $logger = $this->getContainer()->get('logger');
        try {
            $referralService->createReferral($referralDto, true);

            // Flush the email spooler so that the emails that were added are sent.
            $mailer = $this->getContainer()->get('mailer');
            $transport = $mailer->getTransport();
            if ($transport instanceof \Swift_Transport_SpoolTransport) {
                $spool = $transport->getSpool();
                if ($spool instanceof \Swift_MemorySpool) {
                    $spool->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
                }
            }

        } catch (\Exception $e) {
            $logger->error("Referal Creation Failed" . $e->getMessage());
        }
    }
}