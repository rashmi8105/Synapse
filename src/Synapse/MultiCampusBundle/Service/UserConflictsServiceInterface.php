<?php
namespace Synapse\MultiCampusBundle\Service;

use Synapse\MultiCampusBundle\EntityDto\ConflictDto;

interface UserConflictsServiceInterface
{

    public function listConflicts($sourceId, $destinationId, $viewmode);

    public function viewConflictUserDetails($conflictId);

    public function updateResolveSingleConflict(ConflictDto $conflictDto);
}
