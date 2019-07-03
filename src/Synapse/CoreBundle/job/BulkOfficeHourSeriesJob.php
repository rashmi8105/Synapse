<?php
namespace Synapse\CoreBundle\job;

use BCC\ResqueBundle\ContainerAwareJob;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\MapworksActionService;
use Synapse\CoreBundle\Service\Impl\OfficeHoursService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;

class BulkOfficeHourSeriesJob extends ContainerAwareJob
{

    public function run($args)
    {
        $jobService = $this->getContainer()->get(JobService::SERVICE_KEY);
        $officeHoursService = $this->getContainer()->get(OfficeHoursService::SERVICE_KEY);
        $mapworksActionService = $this->getContainer()->get(MapworksActionService::SERVICE_KEY);

        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        $officeHoursDto = unserialize($args['officeHoursDto']);
        $personId = $officeHoursDto->getPersonId();
        $jobNumber = $args['jobNumber'];

        try {
            $person = $personRepository->find($personId);
            $organization = $person->getOrganization();
            $organizationId = $organization->getId();

            $jobService->updateJobStatus($organizationId, 'BulkOfficeHourSeriesJob', SynapseConstant::JOB_STATUS_INPROGRESS, $jobNumber, $personId);
            if (empty($officeHoursDto->getOfficeHoursId())) {
                $officeHoursService->createOfficeHourSeries($officeHoursDto, true);
            } else if ($officeHoursDto->getOneToSeries()) {
                $officeHoursService->createOfficeHourSeries($officeHoursDto, true);
            } else {
                $officeHoursService->editOfficeHourSeries($officeHoursDto, true);
            }
            $jobService->updateJobStatus($organizationId, 'BulkOfficeHourSeriesJob', SynapseConstant::JOB_STATUS_SUCCESS, $jobNumber, $personId);
        } catch (SynapseException $synapseException) {
            $jobService->updateJobStatus($organizationId, 'BulkOfficeHourSeriesJob', SynapseConstant::JOB_STATUS_FAILURE, $jobNumber, $personId, $synapseException->getMessage());
            $tokenValues['$$event_id$$'] = $jobNumber;
            $mapworksActionService->sendCommunicationBasedOnMapworksAction($organizationId, 'failed', 'creator', 'bulk_office_hour', $personId, "Office hour series creation failed", NULL, NULL, $tokenValues);
        }
    }
}