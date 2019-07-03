<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\UploadBundle\Service\Impl\SynapseUploadService;

class UpdateStudentDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    /**
     * creates the student data dump for uploads.
     *
     * @param array $args
     */
    public function run($args)
    {
        $organizationId = $args['orgId'];
        $personId = isset($args['person']) ? $args['person'] : null;

        $synapseUploadService = $this->getContainer()->get(SynapseUploadService::SERVICE_KEY);

        $file = $synapseUploadService->generateDumpCSV($organizationId);

        if ($personId) {
            $repositoryResolver = $this->getContainer()->get('repository_resolver');
            $personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
            $person = $personRepository->find($personId);
            $this->sendNotification($person, $file);
        }
    }

    /**
     * calls AlertNotificationService::createNotification.
     *
     * @param person $person
     * @param String $filename
     */
    private function sendNotification($person, $filename) {
        $alertService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $alertService->createNotification('Student_Data_Generated', 'data generation', $person, null, null, null, $filename, null, null, null, true);
    }

}
