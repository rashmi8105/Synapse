<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\ActivityLogDto;

interface ActivityLogServiceInterface
{
    public function createActivityLog(ActivityLogDto $activityLogDto);
    
    public function deleteActivityLogByType($actvityId, $acticityType);
}