<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;

interface ProfileServiceInterface
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
    public function updateProfile(ProfileDto $profileDto);

    /**
     *
     * @param unknown $ebiMetadataId            
     * @param unknown $sequence            
     */
    public function reorderProfile(ReOrderProfileDto $reOrderProfileDto);

    /**
     *
     * @param unknown $ebiMetadataId            
     */
    public function deleteProfile($ebiMetadataId);

    public function getProfiles($status);

    /**
     *
     * @param unknown $ebiMetadataId
     */
    public function getProfile($ebiMetadataId);
    
    public function updateProfileStatus(ProfileDto $profileDto);
}