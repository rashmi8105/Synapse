<?php
namespace Synapse\DataBundle\Service;

use Synapse\DataBundle\EntityDto\ProfileBlocksDto;

interface ProfileBlocksServiceInterface
{

    /**
     *
     * @param ProfileBlocksDto $profileBlocksDto            
     */
    public function createProfileBlocks(ProfileBlocksDto $profileBlocksDto);

    /**
     *
     * @param ProfileBlocksDto $profileBlocksDto            
     */
    public function updateProfileBlocks(ProfileBlocksDto $profileBlocksDto);

    /**
     *
     * @param unknown $profileBlockID            
     */
    public function deleteProfileBlocks($profileBlockID);

    public function getBlockById($blockId, $exclude);

    public function getDatablocks($user, $type);
}