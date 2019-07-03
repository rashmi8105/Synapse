<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\UploadBundle\Service\Impl\AcademicUpdateUploadService;

class UpdateAcademicUpdateDataFile extends ContainerAwareJob
{
    public function __construct()
    {
        $this->queue = 'dumpfiles';
    }

    /**
     * Generates the academic update download file.
     *
     * @param array $args - ['organizationId']
     */
    public function run($args)
    {
        $organizationId = $args['organizationId'];
        $academicUpdateUploadService = $this->getContainer()->get(AcademicUpdateUploadService::SERVICE_KEY);
        $academicUpdateUploadService->generateDumpCSV($organizationId);
    }
}