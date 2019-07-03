<?php

namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;

interface OrgProfileServiceInterface
{
    /**
     *
     * @param ProfileDto $profileDto
     */
    public function createProfile(ProfileDto $profileDto);
    
    /**
     * 
     * @param ProfileDto $profileDto
     */
    public function editProfile(ProfileDto $profileDto);
    
    /**
     * @param unknown $organizationid
     */
    public function getProfiles($organizationid,$exclude, $status);
    
    public function getProfile($orgId, $metadataId);
    
    public function deleteProfile($metadataId);
    
    public function reorderProfile(ReOrderProfileDto $reOrderProfileDto);
    
    public function updateProfileStatus(ProfileDto $profileDto);
}