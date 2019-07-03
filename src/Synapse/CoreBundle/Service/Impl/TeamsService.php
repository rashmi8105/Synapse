<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\TeamMembers;
use Synapse\CoreBundle\Entity\Teams;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonFacultyRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Service\Utility\EntityValidationService;
use Synapse\CoreBundle\Util\Constants\TeamsConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\TeamIdsDto;
use Synapse\RestBundle\Entity\TeamMembersDto;
use Synapse\RestBundle\Entity\TeamsDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("teams_service")
 */
class TeamsService extends AbstractService
{

    const SERVICE_KEY = 'teams_service';

    //Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var EntityValidationService
     */
    private $entityValidationService;

    //Services
    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * @var PersonService
     */
    private $personService;

    //Repositories

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgPersonFacultyRepository
     */
    private $orgPersonFacultyRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var TeamMembersRepository
     */
    private $teamMembersRepository;

    /**
     * @var TeamsRepository
     */
    private $teamsRepository;

    /**
     *  @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        //Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Services
        $this->entityValidationService = $this->container->get(EntityValidationService::SERVICE_KEY);
        $this->organizationService = $this->container->get(OrganizationService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);


        //Repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(OrgPersonFacultyRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->teamsRepository = $this->repositoryResolver->getRepository(TeamsRepository::REPOSITORY_KEY);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamMembersRepository::REPOSITORY_KEY);
    }


    /**
     * create a new team
     *
     * @param TeamsDto $teamsDto
     * @return mixed
     * @throws SynapseValidationException | DataProcessingExceptionHandler
     */
    public function createNewTeam(TeamsDto $teamsDto)
    {
        $organization = $this->organizationRepository->find($teamsDto->getOrganization(), new SynapseValidationException('Organization not found'));
        $team = new Teams();
        $team->setTeamName($teamsDto->getTeamName());
        $team->setTeamDescription($teamsDto->getTeamDescription());
        $team->setOrganization($organization);

        $dpeh = new DataProcessingExceptionHandler('Team was invalid or a duplicate');
        $this->entityValidationService->validateDoctrineEntity($team, $dpeh);
        $teamInstance = $this->teamsRepository->createNewTeam($team);

        $response['team'] = $team;
        $staff = $teamsDto->getStaff();
        $staffCount = count($staff);
        for ($indexCount = 0; $indexCount < $staffCount; $indexCount ++) {
            $newTeamMember = new TeamMembers();
            $newTeamMember->setTeamId($teamInstance);
            $newTeamMember->setOrganization($organization);
            $person = $this->personRepository->find($staff[$indexCount]['id'], new SynapseValidationException('Faculty Not Found'));
            $newTeamMember->setPerson($person);
            $newTeamMember->setIsTeamLeader($staff[$indexCount]['is_leader']);
            $this->teamMembersRepository->createTeamMembers($newTeamMember);
            $response['staff'][$indexCount] = $newTeamMember->getId();
        }
        $this->teamsRepository->flush();
        return $response;
    }

    public function getTeams($orgId)
    {
        $this->logger->debug("Get Teams by Organization Id" . $orgId);
        $teams = $this->teamsRepository->getTeams($orgId);
        
        if (! $teams) {
            $this->logger->error("Teams Service - getTeams - " . TeamsConstant::ERROR_TEAM_NOT_FOUND);
            throw new ValidationException([
                TeamsConstant::ERROR_TEAM_NOT_FOUND
            ], TeamsConstant::ERROR_TEAM_NOT_FOUND, TeamsConstant::ERROR_TEAM_NOT_FOUND_KEY);
        }
        $this->logger->info("Get Teams by Organization Id");
        return $teams;
    }
    
    public function getTeamLeadersTeams($orgId,$facultyId){

        $teams = $this->teamsRepository->getfacultyTeams($orgId, $facultyId);
        
        
        if (! $teams) {
            $this->logger->error("Teams Service - getTeams - " . TeamsConstant::ERROR_TEAM_NOT_FOUND);
            throw new ValidationException([
                TeamsConstant::ERROR_TEAM_NOT_FOUND
                ], TeamsConstant::ERROR_TEAM_NOT_FOUND, TeamsConstant::ERROR_TEAM_NOT_FOUND_KEY);
        }
        $this->logger->info("Get Teams by Organization Id");
        return $teams;
    }

    public function deleteTeam($id)
    {
        $this->logger->debug("Delete Team by Id" . $id);
        $teamInstance = $this->findTeam($id);
        $teamMembersInstance = $this->teamMembersRepository->findBy(array(
            'teamId' => $teamInstance
        ));
        if (! is_null($teamMembersInstance) && count($teamMembersInstance) > 0) {
            foreach ($teamMembersInstance as $teamMembers) {
                $this->teamMembersRepository->deleteMember($teamMembers);
            }
        }
        $this->teamsRepository->deleteTeam($teamInstance);
        $this->teamsRepository->flush();
        $this->logger->info("Delete Team by Id");
        return $teamInstance;
    }

    public function updateTeams(TeamsDto $teamsDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($teamsDto);
        $this->logger->debug(" Updating Team -  " . $logContent);

        $response = array();
        $organization = $this->organizationService->find($teamsDto->getOrganization());
        $teamId = $teamsDto->getTeamId();
        $teamInstance = $this->findTeam($teamId);
        $staff = $teamsDto->getStaff();
        $staffCount = count($staff);
        $modified = false;
        for ($indexCount = 0; $indexCount < $staffCount; $indexCount ++) {
            $resultSet = $this->teamMembersRepository->getTeamById($staff[$indexCount][TeamsConstant::FIELD_PERSONID], $teamId);
            $newTeamMember = new TeamMembers();
            if ($staff[$indexCount]['action'] == 'update') {
                $person = $this->personService->findPerson($staff[$indexCount][TeamsConstant::FIELD_PERSONID]);
                if (! empty($resultSet)) {
                    $updateInstance = $this->teamMembersRepository->find($resultSet[0]['id']);
                    $updateInstance->setPerson($person);
                    $updateInstance->setIsTeamLeader($staff[$indexCount][TeamsConstant::FIELD_ISLEADER]);
                    $response[][TeamsConstant::FIELD_STAFF] = $updateInstance->getId();
                    $this->teamMembersRepository->updateTeamMembers($updateInstance);
                    $modified = true;
                } else {
                    $newTeamMember->setTeamId($teamInstance);
                    $newTeamMember->setOrganization($organization);
                    $newTeamMember = $this->setPersonAsLeader($newTeamMember, $staff, $indexCount);
                    
                    $teamMember = $this->teamMembersRepository->createTeamMembers($newTeamMember);
                    $response[TeamsConstant::FIELD_STAFF][] = $teamMember->getId();
                    $modified = true;
                }
            } else 
                if ($staff[$indexCount]['action'] == 'delete' && ! empty($resultSet)) {
                    $teamMembersObj = $this->teamMembersRepository->find($resultSet[0]['id']);
                    $this->teamMembersRepository->deleteMember($teamMembersObj);
                    $response[TeamsConstant::FIELD_STAFF][] = $resultSet[0]['id'];
                }
        }
        
        $teamInstance->setTeamName($teamsDto->getTeamName());
        $teamInstance->setTeamDescription($teamsDto->getTeamDescription());
        if ($modified) {
            $teamInstance->setModifiedAt(Helper::getUtcDate());
        }
        $validator = $this->container->get('validator');
        $errors = $validator->validate($teamInstance);
        $this->catchError($errors);
        $this->teamsRepository->updateTeams($teamInstance);
        $this->teamsRepository->flush();
        $response[]['team'] = $teamInstance;
        $this->logger->info("Team Updated");
        return $response;
    }

    private function setPersonAsLeader($newTeamMember, $staff, $indexCount)
    {
        $person = $this->personService->findPerson($staff[$indexCount][TeamsConstant::FIELD_PERSONID]);
        if (isset($person)) {
            $newTeamMember->setPerson($person);
            $newTeamMember->setIsTeamLeader($staff[$indexCount][TeamsConstant::FIELD_ISLEADER]);
        } else {
            return $newTeamMember;
        }
        return $newTeamMember;
    }

    /**
     * @param int $id
     * @param int $organizationId
     * @return array
     * @throws SynapseValidationException
     */
    public function getTeamMembers($id, $organizationId)
    {
        $response = [];
        $teamInstance = $this->teamsRepository->findOneBy([
            'id' => $id,
            'organization' => $organizationId
        ]);
        if ($teamInstance) {
            $teamId = $teamInstance->getId();
            $teamMemberList = $this->teamsRepository->getTeamMembers($teamId);
            $teamDetails = $this->teamsRepository->getTeamDetails($teamId);
            $response['team_id'] = $teamDetails['team_id'];
            $response['team_name'] = $teamDetails['team_name'];
            $response['modified_at'] = $teamDetails['modified_at'];
            $response['staff'] = $teamMemberList;
        } else {
            throw new SynapseValidationException("Team Not Found.");
        }
        return $response;
    }

    public function findTeam($id)
    {
        $this->logger->debug("find Team by Id " . $id);
        $team = $this->teamsRepository->find($id);
        
        if (! $team) {
            $this->logger->error("Teams Service - findTeam - " . "Find Team by Id" . $id);
            throw new ValidationException([
                TeamsConstant::ERROR_TEAM_NOT_FOUND
            ], TeamsConstant::ERROR_TEAM_NOT_FOUND, TeamsConstant::ERROR_TEAM_NOT_FOUND_KEY);
        }
        return $team;
    }

    /**
     * Get team details by faculty id
     *
     * @param int $organizationId
     * @param int $personId
     * @return array
     * @throws SynapseValidationException
     */
    public function getOrganizationTeamByUserId($organizationId, $personId)
    {
        $person = $this->personRepository->find($personId);
        if (!$person) {
            throw new SynapseValidationException('The person does not exist.');
        }

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        // Adding this condition to check whether personId is exist in that organization or not.
        $orgPersonFaculty = $this->orgPersonFacultyRepository->findBy(array(
            'person' => $personId,
            'organization' => $organizationId
        ));
        if (!$orgPersonFaculty) {
            throw new SynapseValidationException('Person Not Found in this Organization.');
        }

        $organizationTeams = $this->teamMembersRepository->getOrganizationTeamByUserId($organizationId, $personId);
        if (!$organizationTeams) {
            $organizationTeams = [];
        }
        array_walk($organizationTeams, [$this, 'nullToZero']);
        return $organizationTeams;
    }


    private function nullToZero(&$arr,&$key){
       if(is_null($arr['role']))
       {
           $arr['role'] = "0";
       }
        
    }
    public function getMyTeams($loggedUserId)
    {
        $this->logger->info("Get My Teams ");
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamsConstant::TEAM_MEMBER_REPO);
        $loggedPerson = $this->personService->findPerson($loggedUserId);
        $personOrganization = $loggedPerson->getOrganization();
        $organizationId = $personOrganization->getId();
        $teamsDto = new TeamsDto();
        $personTeams = $this->teamMembersRepository->getMyTeams($loggedUserId, $organizationId);
        if (isset($personTeams) && count($personTeams) > 0) {
            $teamsDto->setPersonId($loggedUserId);
            $teamsId = [];
            foreach ($personTeams as $pTeams) {
                $teamIdsDto = new TeamIdsDto();
                $teamIdsDto->setId($pTeams["team_id"]);
                $teamIdsDto->setTeamName($pTeams["team_name"]);
                $teamsId[] = $teamIdsDto;
            }
            $teamsDto->setTeamIds($teamsId);
        }
        $this->logger->info("Get My Teams ");
        return $teamsDto;
    }

    public function getTeamMembersByPerson($loggedUserId, $teamId)
    {
        $this->logger->debug("Get Teams Members by Team Id" . $teamId);
        $this->teamMembersRepository = $this->repositoryResolver->getRepository(TeamsConstant::TEAM_MEMBER_REPO);
        $loggedPerson = $this->personService->findPerson($loggedUserId);
        $personOrganization = $loggedPerson->getOrganization();
        $organizationId = $personOrganization->getId();
        $teamsDto = new TeamsDto();
        $teamMembers = $this->teamMembersRepository->getTeamMembersByPerson($teamId, $organizationId);
        
        if (! $teamMembers) {
            $this->logger->error( "Teams Service - getTeamMembersByPerson - " . TeamsConstant::ERROR_TEAM_NOT_FOUND );
            throw new ValidationException([
                TeamsConstant::ERROR_TEAM_NOT_FOUND
            ], TeamsConstant::ERROR_TEAM_NOT_FOUND, TeamsConstant::ERROR_TEAM_NOT_FOUND_KEY);
        }
        $teamsDto->setPersonId($loggedUserId);
        $teamsDto->setTeamId($teamId);
        $teamMembersArray = [];
        foreach ($teamMembers as $tMember) {
            $teamMembersDto = new TeamMembersDto();
            $teamMembersDto->setId($tMember['person']);
            $teamMembersDto->setFirstName($tMember['first_name']);
            $teamMembersDto->setLastName($tMember['last_name']);
            $teamMembersDto->setTeamMemberEmailId($tMember['primaryEmail']);
            $teamMembersArray[] = $teamMembersDto;
        }
        $teamsDto->setTeamMembers($teamMembersArray);
        $this->logger->info("Team Not found");
        return $teamsDto;
    }

    private function catchError($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'team_duplicate_Error');
        }
    }

}