<?php
namespace Synapse\MultiCampusBundle\Service;

use Synapse\MultiCampusBundle\EntityDto\TierDto;

interface TierServiceInterface
{

    public function createTier(TierDto $tierDto);

    public function updateTier(TierDto $tierDto);

    public function viewTier($tierId, $tierlevel);

    public function listTier($tierId, $tierlevel);

    public function deleteSecondaryTier($id, $tierlevel);    
  
}