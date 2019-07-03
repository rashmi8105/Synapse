<?php
namespace Synapse\MultiCampusBundle\Service;

use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\CampusChangeRequestDto;
use Synapse\MultiCampusBundle\EntityDto\ConflictDto;

interface CampusServiceInterface
{

    public function createHierarchyCampus($tierId, CampusDto $campusDto);

    public function updateMoveHierarchyCampus($tierId, CampusDto $campusDto);

    public function viewCampuses($tierId, $campusId);

    public function listHierarchyCampus($tierId, $paramFetcher);

    public function deleteHierarchyCampus($tierId, $campusId);

    public function createChangeRequest(ChangeRequestDto $changeRequestDto);

    public function deleteChangeRequest($requestId);

    public function listChangeRequest($type, $loggedUserId, $campusId);

    public function updateChangeRequest(CampusChangeRequestDto $campusChangeRequestDto, $campusId);

    public function listTierUsersCampus($id);
}