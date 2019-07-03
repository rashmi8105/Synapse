<?php

namespace Synapse\UploadBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use BCC\ResqueBundle\Resque;
use Doctrine\Common\Cache\RedisCache;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\PersonRepository;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Stopwatch\Stopwatch;
use Synapse\CoreBundle\job\InactiveFacultyJob;


/**
 * Handle faculty uploads
 *
 * @DI\Service("faculty_upload_service")
 */
class FacultyUploadService extends AbstractService
{

    const SERVICE_KEY = 'faculty_upload_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RedisCache
     */
    private $cache;

    private $doctrine;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var Resque
     */
    private $resque;


    /**
     * @param $repositoryResolver
     * @param $logger
     * @param $doctrine
     * @param $container
     * @param $cache
     * @param $resque
     *
     * @DI\InjectParams({
     *      "repositoryResolver" = @DI\Inject("repository_resolver"),
     *      "logger" = @DI\Inject("logger"),
     *      "doctrine" = @DI\Inject("doctrine"),
     *      "container" = @DI\Inject("service_container"),
     *      "cache" = @DI\Inject("synapse_redis_cache"),
     *      "resque" = @DI\Inject("bcc_resque.resque")
     * })
     */
    public function __construct($repositoryResolver, $logger, $doctrine, $container, $cache, $resque)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->doctrine = $doctrine;
        $this->container = $container;
        $this->cache = $cache;
        $this->resque = $resque;

        //Repository
        $this->personRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");


    }


    /**
     * Using the data in the rows from the uploaded CSV file, determines if any faculty members have had their status set to inactive.  If so,
     * creates and places a job on the background queue to run the InactivateFacultyJob for all faculty that have been
     * inactivated.  This job removes the faculty member from appointments, referrals, course ,etc (see the InactivateFacultyJob
     * class for details.
     *
     * @param array $updatedRows
     * @param int $orgId
     */
    public function removeActivitiesForFacultyInactivatedByThisUpload($updatedRows, $orgId)
    {
        $facultyStatus = array_column($updatedRows, 'isactive', 'externalid');

        $inactivatedFacultyIds = [];
        foreach ($facultyStatus as $externalId => $status) {
            if ($status === 0 || $status === "0") {
                $person = $this->personRepository->findOneBy(['externalId' => $externalId, 'organization' => $orgId]);
                if (!empty($person)) {
                    $inactivatedFacultyIds[] = $person->getId();
                }
            }
        }

        if (count($inactivatedFacultyIds) > 0) {
            $job = new InactiveFacultyJob();
            $jobNumber = uniqid();
            $job->args = array(
                'jobNumber' => $jobNumber,
                'orgId' => $orgId,
                'facultyIds' => $inactivatedFacultyIds
            );

            $this->resque->enqueue($job, true);
        }
    }

}