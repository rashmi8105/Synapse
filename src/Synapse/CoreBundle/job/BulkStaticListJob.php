<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\StaticListBundle\Service\Impl\StaticListStudentsService;

class BulkStaticListJob extends ContainerAwareJob
{

    public function run($args)
    {

        $organizationId = $args['organizationId'];
        $facultyId = $args['faculty'];
        $studentIds = $args['studentIds'];
        $staticListId = $args['staticListId'];
        $action = $args['action'];
        $staticListService = $this->getContainer()->get(StaticListStudentsService::SERVICE_KEY);
        $personRepository = $this->getContainer()->get('repository_resolver')->getRepository(PersonRepository::REPOSITORY_KEY);
        $faculty = $personRepository->find($facultyId);

        if ($action == 'add' || $action == 'share') {
            $staticListService->addStudentsToStaticList($organizationId, $faculty, $staticListId, $studentIds);
        } else {
            $staticListService->removeStudentsFromStaticList($organizationId, $faculty, $staticListId, $studentIds);
        }
    }
}