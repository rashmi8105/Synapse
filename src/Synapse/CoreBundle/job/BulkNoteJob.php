<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\RestBundle\Entity\ReferralsDTO;

class BulkNoteJob extends ContainerAwareJob
{

    public function run($args)
    {
       
        $noteDto = unserialize($args['noteDto']);
        $noteService = $this->getContainer()->get('notes_service');
        $logger = $this->getContainer()->get('logger');
        try {
            $noteService->createNote($noteDto, true);
        } catch (\Exception $e) {
            $logger->error("Note Creation Failed" . $e->getMessage());
        }
    }
}