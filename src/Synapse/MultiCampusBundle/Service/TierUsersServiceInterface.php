<?php
namespace Synapse\MultiCampusBundle\Service;

use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\RoleDto;
use Synapse\MultiCampusBundle\EntityDto\PromoteUserDto;

interface TierUsersServiceInterface
{

    public function deleteTierUser($personid, $tierId, $tierlevel);

    public function listTierUsers($tierlevel, $tierId);

    public function listExistingUsers($tierId, $tierlevel);

    public function updateCoordinatorRole($userId, RoleDto $roleDto);

    public function promoteUserToTierUser(PromoteUserDto $promoteDto);

    public function listPrimaryTierCoordinators($campusId, $filter = '');

    public function listActiveCampusTiersforUser($loggedUser);
}