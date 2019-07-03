<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\UserDTO;

interface UsersServiceInterface
{

    /**
     *
     * @param UserDTO $userDTO            
     */
    public function createUser(UserDTO $userDTO);

    /**
     *
     * @param UserDTO $userDTO            
     * @param int $userId         
	 * @param int $loggedInOrgId         	 
     */
    public function updateUser(UserDTO $userDTO, $userId, $loggedInOrgId);

    /**
     *
     * @param UserDTO $userDTO            
     */
    public function promoteFacultyToCoordinator(UserDTO $userDTO);

    /**
     *
     * @param int $orgId            
     * @param string $filter            
     */
    public function getUsers($orgId, $filter);

    /**
     *
     * @param int $tierCampusId            
     * @param int $campusId            
     * @param string $type            
     * @param string $tierlevel            
     * @param int $tierId            
     * @param string $list            
     */
    public function getUsersList($tierCampusId, $campusId, $type, $tierlevel, $tierId, $list, $searchText, $page, $offset, $exclude,$checkAccessToOrg);

    /**
     *
     * @param int $campusId            
     * @param int $userId            
     * @param string $type            
     */
    public function sendInvitation($campusId, $userId, $type);

    /**
     *
     * @param int $userId            
     */
    public function getUser($userId, $campusId);

    /**
     *
     * @param int $userId            
     * @param int $campusId            
     * @param string $type            
     */
    public function deleteUser($userId, $campusId, $type);

    public function listTierUserDashboard($loggedUser);

    /**
     *
     * @param int $campusId            
     * @param string $type            
     */
    public function bulkUserInvite($campusId, $type);

    /**
     *
     * @param int $primaryTierId            
     *
     */
    public function listPrimaryTierDetails($primaryTierId);

    /**
     *
     * @param int $primaryTierId            
     * @param int $secondaryTierId            
     * @param int $campusId            
     *
     */
    public function listUserDetails($primaryTierId, $secondaryTierId, $campusId);
}