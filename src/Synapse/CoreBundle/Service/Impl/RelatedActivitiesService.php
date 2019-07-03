<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\RelatedActivities;
use Synapse\CoreBundle\Entity\ActivityLog;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Service\RelatedActivitiesServiceInterface;
use Synapse\RestBundle\Entity\RelatedActivitiesDto;
use Synapse\CoreBundle\Util\Helper;

/**
 * @DI\Service("relatedactivities_service")
 */
class RelatedActivitiesService extends AbstractService implements RelatedActivitiesServiceInterface
{
    const SERVICE_KEY = 'relatedactivities_service';

    private $relatedActivitiesRepository;

    private $activityLogRepository;

    private $contactsRepository;

    private $noteRepository;

    private $organizationRepository;
    
    private $emailRepository;
	   /**
     *
     * @var container
     */
    private $container;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
	 *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
		$this->container = $container;
    }

    public function createRelatedActivities(RelatedActivitiesDto $relatedActivitiesDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($relatedActivitiesDto);
        $this->logger->debug(" Creating Referral Activities -  " . $logContent);
        
        $this->relatedActivitiesRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:RelatedActivities");
        $this->activityLogRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:ActivityLog");
        $this->contactsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Contacts");
        $this->noteRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Note");
        $this->appointmentRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Appointments");
        $this->referralRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Referrals");
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
        $this->emailRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Email");
        $relatedActivitiesObj = new RelatedActivities();
        $date_today = Helper::getUtcDate();
        $relatedActivitiesObj->setCreatedOn($date_today);
        if (! is_null($relatedActivitiesDto->getActivityLog())) {
            
            $activityLog = $this->activityLogRepository->findById($relatedActivitiesDto->getActivityLog());
            if ($activityLog) {
                $relatedActivitiesObj->setActivityLog($activityLog[0]);
            }
        }
        if (! is_null($relatedActivitiesDto->getContacts())) {
            
            $contacts = $this->contactsRepository->findById($relatedActivitiesDto->getContacts());
            if ($contacts) {
                $relatedActivitiesObj->setContacts($contacts[0]);
            }
        }
        if (! is_null($relatedActivitiesDto->getNote())) {
            $note = $this->noteRepository->findById($relatedActivitiesDto->getNote());
            if ($note) {
                $relatedActivitiesObj->setNote($note[0]);
            }
        }
        
        /*
         * For appointements and referrals
         * 
         */
        
        if (! is_null($relatedActivitiesDto->getAppointment())) {
            $app = $this->appointmentRepository->findById($relatedActivitiesDto->getAppointment());
            if ($app) {
                $relatedActivitiesObj->setAppointment($app[0]);
            }
        }
        
        
        if (! is_null($relatedActivitiesDto->getReferrals())) {
            $ref = $this->referralRepository->findById($relatedActivitiesDto->getReferrals());
            if ($ref) {
                $relatedActivitiesObj->setReferral($ref[0]);
            }
        }
        
        if (! is_null($relatedActivitiesDto->getEmail())) {
            $email = $this->emailRepository->findById($relatedActivitiesDto->getEmail());
            if ($email) {
                $relatedActivitiesObj->setEmail($email[0]);
            }
        }
        
        /*
        * End of changes
        *
        */
        
        
        if (! is_null($relatedActivitiesDto->getOrganization())) {
            $org = $this->organizationRepository->findById($relatedActivitiesDto->getOrganization());
            if ($org) {
                $relatedActivitiesObj->setOrganization($org[0]);
            }
        }
        $relatedActivities = $this->relatedActivitiesRepository->createRelatedActivities($relatedActivitiesObj);
        $this->relatedActivitiesRepository->flush();
        $this->logger->info("Created Related Activities");
        return $relatedActivities;
    }
}
