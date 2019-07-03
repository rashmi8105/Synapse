<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;

class SendAppointmentReminderJob extends ContainerAwareJob
{

    public function run($args)
    {
        $appointmentService = $this->getContainer()->get('appointments_service');
        $appointmentService->getReminder($args['appointment']);
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
