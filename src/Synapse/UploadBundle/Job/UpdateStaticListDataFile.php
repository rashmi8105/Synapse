<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\UploadBundle\Service\Impl\StaticListUploadService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;

class UpdateStaticListDataFile extends ContainerAwareJob
{

    /**
     * @var StaticListUploadService
     */
    private $staticListUploadService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    /**
     * Runs the StaticListUploadService::generateDumpCSV function in a job format.
     * If the person given exists, then give them a notification
     *
     * @param Array $args -> ['orgId' => int, 'staticListId' => int, 'person' => int]
     */
    public function run($args)
    {
        $orgId = $args['orgId'];
        $staticListId = $args['staticListId'];
        $personId = $args['person'];

        $this->staticListUploadService = $this->getContainer()->get(StaticListUploadService::SERVICE_KEY);
        $file = $this->staticListUploadService->generateDumpCSV($orgId, $staticListId);

        if ($personId) {
            $this->personService = $this->getContainer()->get('person_service');
            $person = $this->personService->find($personId);

            $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
            $this->alertNotificationsService->createCSVDownloadNotification('Static_List_Data_Generated', 'data generation', $file, $person->getId());
        }
    }

}