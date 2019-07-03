<?php

/**
 * Class OfficeHoursSeriesRepositoryTest
 */

use Codeception\TestCase\Test;

class OfficeHoursSeriesRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository
     */
    private $officeHoursSeriesRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->officeHoursSeriesRepository = $this->repositoryResolver->getRepository(\Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository::REPOSITORY_KEY);
    }

    public function testGetFutureOfficeHoursSeriesModified()
    {
        $this->specify("Verify the functionality of the method getFutureOfficeHoursSeriesModified", function ($orgId, $personId, $expectedCount) {
            $startDate = new \DateTime('now');
            $slotEndDate = new \DateTime($startDate->format('Y-m-d H:i:s'));
            $modifiedTime = $slotEndDate->sub(new \DateInterval("P1D"))->format('Y-m-d H:i:s');
            $results = $this->officeHoursSeriesRepository->getFutureOfficeHoursSeriesModified($orgId, $personId, $modifiedTime);
            if (count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);
                foreach ($results as $officeHourSeries) {
                    verify($officeHourSeries)->notEmpty();
                    verify($officeHourSeries->getDays())->notEmpty();
                    verify($officeHourSeries->getLocation())->notEmpty();
                    verify($officeHourSeries->getSlotStart())->notEmpty();
                    verify($officeHourSeries->getSlotEnd())->notEmpty();
                    verify($officeHourSeries->getMeetingLength())->notEmpty();
                    verify($officeHourSeries->getRepeatPattern())->notEmpty();
                    verify($officeHourSeries->getRepeatEvery())->notEmpty();
                    verify($officeHourSeries->getOrganization()->getId())->equals($orgId);
                    verify($officeHourSeries->getPerson()->getId())->equals($personId);
                    verify($officeHourSeries->getGoogleMasterAppointmentId())->isEmpty();
                    verify($officeHourSeries->getLastSynced())->notEmpty();
                }
            } else {
                verify(count($results))->equals($expectedCount);
            }

        }, ["examples" =>
            [
                [203, 4878750, 0],
                [203, 4891668, 0]
            ]
        ]);
    }


    public function testGetSyncedOfficeHourSeries()
    {
        $this->specify("Verify the functionality of the method getSyncedOfficeHourSeries", function ($orgId, $personId, $expectedCount) {

            $results = $this->officeHoursSeriesRepository->getSyncedOfficeHourSeries($orgId, $personId);
            if (count($results) > 0) {
                verify(count($results))->greaterThan($expectedCount);
                foreach ($results as $officeHourSeries) {
                    verify($officeHourSeries)->notEmpty();
                    verify($officeHourSeries->getGoogleMasterAppointmentId())->notEmpty();
                }
            } else {
                verify(count($results))->equals($expectedCount);
            }
        }, ["examples" =>
            [
                [203, 4878750, 0]
            ]
        ]);
    }

    public function testGetOfficeHoursBySeriesId()
    {
        $this->specify("Verify the functionality of the method getOfficeHoursBySeriesId", function ($officeHourSeriesId, $expectedCount) {
            $results = $this->officeHoursSeriesRepository->getOfficeHoursBySeriesId($officeHourSeriesId);
            verify(count($results))->equals($expectedCount);
        }, ["examples" =>
            [
                [2237, 9],
                [2240, 3],
                [2247, 5]
            ]
        ]);
    }
}