<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\ReferralsTeams;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\ReferralsTeamsRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Util\Constants\ReferralConstant;
use Synapse\CoreBundle\Util\Constants\TeamsConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\ReferralsDTO;
use Synapse\RestBundle\Exception\ValidationException;

class ReferralHelperService extends AbstractService
{

    /**
     * @var ReferralsTeamsRepository
     */
    private $referralsTeamsRepository;

    /**
     * @var TeamsRepository
     */
    private $teamsRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;


    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->referralsTeamsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:ReferralsTeams');
        $this->teamsRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Teams");
        $this->teamMembersRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:TeamMembers");
    }


    /**
     * For each selected team in $teamsArray, creates a record in the referrals_teams table
     * in order to specify which teams have access to a referral with Team Sharing Option.
     * Throws an exception if no teams are selected.
     *
     * @param Referrals $referral
     * @param array $teamsArray
     */
    protected function addTeams($referral, $teamsArray)
    {
        $teamsAreSelected = false;
        foreach ($teamsArray as $team) {
            if ($team->getIsTeamSelected()) {
                $referralTeam = new ReferralsTeams();
                $team = $this->teamsRepository->find($team->getId());
                $referralTeam->setReferrals($referral);
                $referralTeam->setTeams($team);
                $this->referralsTeamsRepository->createReferralsTeams($referralTeam);
                $teamsAreSelected = true;
            }
        }

        if (!$teamsAreSelected) {
            throw new ValidationException([TeamsConstant::TEAM_SHARING_ERROR_MESSAGE], TeamsConstant::TEAM_SHARING_ERROR_MESSAGE);
        }
    }


    /**
     * When a user edits a referral's sharing option and chooses team sharing, makes sure that the teams the user chose
     * to share with (in $referralsDTO) are saved correctly in the referrals_teams table.  Soft-deletes any records
     * for teams not in the user's request, and adds records for any teams in the user's request not already in the table.
     *
     * @param Referrals $referral
     * @param ReferralsDTO $referralsDTO
     * @param INT $loggedInUserId
     */
    protected function reconcileTeams($referral, $referralsDTO, $loggedInUserId)
    {
        // The DTO (from the POST JSON) contains all teams available to the user.
        // Of these, get the ids of the teams the user wants to share the referral with (which have the "is_team_selected" attribute set to true).
        $availableTeams = $referralsDTO->getShareOptions()[0]->getTeamIds();
        $requestedTeamIds = [];
        foreach ($availableTeams as $availableTeam) {
            if ($availableTeam->getIsTeamSelected()) {
                $requestedTeamIds[] = $availableTeam->getId();
            }
        }

        // Make sure the user is a member of all the teams he/she is trying to share the referral with; throw an exception if not.
        $teamsForUser = $this->teamMembersRepository->findBy(['person' => $loggedInUserId]);
        $teamIdsForUser = [];
        foreach ($teamsForUser as $team) {
            $teamIdsForUser[] = $team->getTeamId()->getId();
        }

        // If the user has chosen team sharing but hasn't chosen any teams, throw an exception.
        if (empty($requestedTeamIds)) {
            throw new ValidationException([TeamsConstant::TEAM_SHARING_ERROR_MESSAGE], TeamsConstant::TEAM_SHARING_ERROR_MESSAGE);
        }

        // Find any existing records for this referral in the referrals_teams table.
        $existingReferralsTeamsRecords = $this->referralsTeamsRepository->findBy(['referrals' => $referral->getId()]);

        // Get the team ids from these existing records.
        $existingTeamIds = [];
        foreach ($existingReferralsTeamsRecords as $existingReferralsTeamsRecord) {
            $existingTeamIds[] = $existingReferralsTeamsRecord->getTeams()->getId();
        }

        // Throw an error in the event that the user is trying to share the referral with a team that they are not a member of
        // and that was not previously on the referral.
        $requestedTeamIdsWithoutLoggedInUserMembership = array_diff($requestedTeamIds, $teamIdsForUser);
        $inaccessibleTeamIds = array_diff($requestedTeamIdsWithoutLoggedInUserMembership, $existingTeamIds);
        if (!empty($inaccessibleTeamIds)) {
            throw new AccessDeniedException("Access Denied: You're trying to share a referral with a team you're not a member of.");
        }

        // Determine which teams need to be removed and which teams need to be added.
        $teamIdsToRemove = array_diff($existingTeamIds, $requestedTeamIds);
        $teamIdsToAdd = array_diff($requestedTeamIds, $existingTeamIds);

        // Soft-delete any referrals_teams records which are not in the user's request.
        foreach ($teamIdsToRemove as $teamIdToRemove) {
            foreach ($existingReferralsTeamsRecords as $existingReferralsTeamsRecord) {
                $existingTeamId = $existingReferralsTeamsRecord->getTeams()->getId();
                if ($existingTeamId == $teamIdToRemove) {
                    $this->referralsTeamsRepository->removeReferralsTeam($existingReferralsTeamsRecord);
                }
            }
        }

        // Add any teams in the user's request which are not already in the referrals_teams table.
        $teamsToAdd = [];
        foreach ($teamIdsToAdd as $teamIdToAdd) {
            foreach ($availableTeams as $availableTeam) {
                $availableTeamId = $availableTeam->getId();
                if ($availableTeamId == $teamIdToAdd) {
                    $teamsToAdd[] = $availableTeam;
                }
            }
        }

        if (!empty($teamsToAdd)) {
            $this->addTeams($referral, $teamsToAdd);
        }
    }


    protected function getAssignee($assigneesDTO, $orgPermission, $facultyInfo)
    {
        $orgFeaturePermissionRepo = $this->repositoryResolver->getRepository(ReferralConstant::ORG_PERMISSION_FEATURES_REPO);
        if ($orgPermission) {
            /**
             * 1 is referral feature id
             */
            $featurePermissions = $orgFeaturePermissionRepo->getPermissionSetsByFeatures($orgPermission->getId(), null, [
                1
            ]);
            if (! empty($featurePermissions)) {
                if ($featurePermissions[0]['receiveReferral']) {
                    $assigneesDTO->addAssignedToUsers($facultyInfo);
                }
            }
        }
        return $assigneesDTO;
    }

    /**
     * @param $refId
     * @return Referrals
     */
    protected function findReferral($refId)
    {
        $referralRepository = $this->repositoryResolver->getRepository(ReferralConstant::REFERRALS_REPO);
        $referral = $referralRepository->find($refId);
        if (!$referral) {
            throw new SynapseValidationException("Referral not found");
        } else {
            return $referral;
        }
    }

    protected function getEbiSearchQuery($searchKey, $tokenValues)
    {
        $searchRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiSearch");
        $query_by_key = $searchRepository->findOneByQueryKey($searchKey);
        if ($query_by_key) {
            $returnQuery = $query_by_key->getQuery();
        }
        $returnQuery = Helper::generateQuery($returnQuery, $tokenValues);
        return $returnQuery;
    }

    /**
     * Determines whether Referral Content has changes
     * This function ignores adding/removing interested parties or reassignment of assignee changes
     *
     * @param ReferralsDTO $referralDTO
     * @param Referrals $referral
     * @return bool
     */
    public function didReferralContentChange($referralDTO, $referral)
    {
        if ($referral->getNote() != $referralDTO->getComment()) {
            return true;
        }

        if ($referral->getIsDiscussed() != $referralDTO->getIssueDiscussedWithStudent()) {
            return true;
        }

        if ($referral->getIsHighPriority() != $referralDTO->getHighPriorityConcern()) {
            return true;
        }

        if ($referral->getReferrerPermission() != $referralDTO->getIssueRevealedToStudent()) {
            return true;
        }

        if ($referral->getIsLeaving() != $referralDTO->getStudentIndicatedToLeave()) {
            return true;
        }

        if ($referral->getNotifyStudent() != $referralDTO->getNotifyStudent()) {
            return true;
        }

        foreach ($referralDTO->getShareOptions() as $shareOptionDto) {
            if ($shareOptionDto->getPublicShare() != $referral->getAccessPublic()) {
                return true;
            }

            if ($shareOptionDto->getPrivateShare() != $referral->getAccessPrivate()) {
                return true;
            }

            if ($shareOptionDto->getTeamsShare() != $referral->getAccessTeam()) {
                return true;
            }
        }

        //DID THE TEAMS CHANGE
        if ($this->didReferralTeamsChange($referralDTO, $referral)) {
            return true;
        }

        return false;
    }

    /**
     * Determine referral team changes
     *
     * @param ReferralsDTO $referralsDTO
     * @param Referrals $referral
     * @return bool
     */
    private function didReferralTeamsChange($referralsDTO, $referral)
    {
        $availableTeams = $referralsDTO->getShareOptions()[0]->getTeamIds();
        $requestedTeamIds = [];
        foreach ($availableTeams as $availableTeam) {
            if ($availableTeam->getIsTeamSelected()) {
                $requestedTeamIds[] = $availableTeam->getId();
            }
        }

        // Find any existing records for this referral in the referrals_teams table.
        $existingReferralsTeamsRecords = $this->referralsTeamsRepository->findBy(['referrals' => $referral->getId()]);

        // Get the team ids from these existing records.
        $existingTeamIds = [];
        foreach ($existingReferralsTeamsRecords as $existingReferralsTeamsRecord) {
            $existingTeamIds[] = $existingReferralsTeamsRecord->getTeams()->getId();
        }

        $removedTeams = array_diff($existingTeamIds, $requestedTeamIds);
        $addedTeams = array_diff($requestedTeamIds, $existingTeamIds);

        //If we are not adding or subtracting, things didn't change
        $didTeamsChange = !empty($removedTeams) || !empty($addedTeams);

        return $didTeamsChange;
    }
}
