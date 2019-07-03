<?php
namespace Synapse\SearchBundle\Service;

use Synapse\SearchBundle\EntityDto\SharedSearchDto;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;

interface SharedSearchServiceInterface
{

    public function create(SharedSearchDto $sharedSearchDto);

    public function getSharedSearches($loggedInUser, $timezone, $orgId);

    public function delete($searchId, $shared_search_id, $shared_by_user_id);

    public function edit(SaveSearchDto $saveSearchDto, $loggedInUser);
}