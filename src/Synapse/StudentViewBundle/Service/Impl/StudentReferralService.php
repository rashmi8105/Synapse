<?php
namespace Synapse\StudentViewBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\StudentViewBundle\Service\StudentReferralServiceInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StudentViewBundle\Util\Constants\StudentViewErrorConstants;
use Synapse\StudentViewBundle\EntityDto\StudentOpenReferralsDto;
use Synapse\CoreBundle\Util\Helper;

/**
 * @DI\Service("studentreferral_service")
 */
class StudentReferralService extends AbstractService implements StudentReferralServiceInterface
{

    const SERVICE_KEY = 'studentreferral_service';

    private $orgPersonStudentRepo;

    private $referralRepository;

    const REFERRAL = 'referral';

    const ASSIGN_TO = 'assignTo';

    const ASSIGN_TO_EMAIL = 'assignToEmail';

    const ASSIGN_TO_ROLE = 'assignToRole';

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

    public function getStudentOpenReferrals($loggedInUser)
    {
        
        $this->logger->debug(" Get Student Open Referrals for Logged In User ");
        $referrals = array();
        $this->orgPersonStudentRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgPersonStudent");
        $orgStudent = $this->orgPersonStudentRepo->findByPerson($loggedInUser);
        $this->isObjectExist($orgStudent, StudentViewErrorConstants::STUDENT_VIEW_102, StudentViewErrorConstants::STUDENT_VIEW_102);
        $this->referralRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Referrals");
        $studentOpenReferrals = $this->referralRepository->getStudentOpenReferrals($loggedInUser);
        foreach ($studentOpenReferrals as $openRef) {
            $openReferrals = new StudentOpenReferralsDto();
            $openReferrals->setReferralId($openRef[self::REFERRAL]->getId());
            $openReferrals->setOrganizationId($openRef[self::REFERRAL]->getOrganization()
                ->getId());
            $openReferrals->setCampusName($openRef['org_name']);
            $timeZone = $this->getOrganizationTimeZone($openRef[self::REFERRAL]->getOrganization());
            //Helper::getOrganizationDate($openRef[self::REFERRAL]->getReferralDate(), $timeZone);
            $openReferrals->setReferralDate($openRef[self::REFERRAL]->getReferralDate());
            $createdBy = $openRef[self::REFERRAL]->getPersonIdFaculty()->getLastname() . ', ' . $openRef[self::REFERRAL]->getPersonIdFaculty()->getFirstname();
            $openReferrals->setCreatedBy($createdBy);
            $openReferrals->setCreatedByEmail($openRef[self::REFERRAL]->getPersonIdFaculty()
                ->getContacts()[0]
                ->getPrimaryEmail());
            $openReferrals->setCreatedByRole($openRef[self::REFERRAL]->getPersonIdFaculty()
                ->getTitle());
            if (is_object($openRef[self::REFERRAL]->getPersonAssignedTo())) {
                $assignedTo = $openRef[self::REFERRAL]->getPersonAssignedTo()->getFirstname() . ' ' . $openRef[self::REFERRAL]->getPersonAssignedTo()->getLastname();
                $openReferrals->setAssignedTo($assignedTo);
                $openReferrals->setAssignedToEmail($openRef[self::REFERRAL]->getPersonAssignedTo()
                    ->getContacts()[0]
                    ->getPrimaryEmail());
                $openReferrals->setAssignedToRole($openRef[self::REFERRAL]->getPersonAssignedTo()
                    ->getTitle());
            } else {
                $assignToDetails = $this->getCentralReferralDetails($openRef[self::REFERRAL]->getOrganization()
                ->getId(), $openRef[self::REFERRAL]->getActivityCategory(), $openRef['langId']);
                $openReferrals->setAssignedTo($assignToDetails[self::ASSIGN_TO]);
                $openReferrals->setAssignedToEmail($assignToDetails[self::ASSIGN_TO_EMAIL]);
                $openReferrals->setAssignedToRole($assignToDetails[self::ASSIGN_TO_ROLE]);
            }
            $openReferrals->setDescription($openRef[self::REFERRAL]->getNote());
            $reason = (! empty($openRef[self::REFERRAL]->getActivityCategory())) ? $openRef[self::REFERRAL]->getActivityCategory()->getShortName() : '';
            $openReferrals->setReason($reason);
            $referrals['referrals'][] = $openReferrals;
            $this->logger->info("Listing of Student open referrals completed");
        }
        return $referrals;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object) || empty($object)) {
            $this->logger->error("Student View - Referrals List - " . $message);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function getOrganizationTimeZone($organization)
    {
        $timeZone = '';
        $timezone = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($organization->getTimezone());
        if ($timezone) {
            $timeZone = $timezone[0]->getListValue();
        }
        return $timeZone;
    }

    private function getCentralReferralDetails($orgId, $activityCategory, $orgLangId)
    {
        $personService = $this->container->get('person_service');
        $referralRoutingRuleRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:ReferralRoutingRules");
        $activityRef = $referralRoutingRuleRepo->findOneBy(array(
            'organization' => $orgId,
            'activityCategory' => $activityCategory
        ));
        $details = array();
        if($activityRef){
            if($activityRef->getIsPrimaryCoordinator()){
                $primaryCoordinators = $personService->getAllPrimaryCoordinators($orgId, $orgLangId, 'Primary coordinator');
                $assignTo = $primaryCoordinators[0]->getPerson();
                $details[self::ASSIGN_TO] = $assignTo->getFirstname().' '.$assignTo->getLastname();
                $details[self::ASSIGN_TO_EMAIL] = $assignTo->getContacts()[0]->getPrimaryEmail();
                $details[self::ASSIGN_TO_ROLE] = $assignTo->getTitle();
            }else{
                if($activityRef && !empty($activityRef->getPerson())){
                    $assignTo = $personService->findPerson($activityRef->getPerson()->getId());
                    $details[self::ASSIGN_TO] = "Faculty/Staff name was not shared";
                    $details[self::ASSIGN_TO_EMAIL] = $assignTo->getContacts()[0]->getPrimaryEmail();
                    $details[self::ASSIGN_TO_ROLE] = $assignTo->getTitle();
               }
            }
        }
        return $details;
    }
}