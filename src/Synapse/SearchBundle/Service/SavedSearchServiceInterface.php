<?php
namespace Synapse\SearchBundle\Service;

use Synapse\SearchBundle\EntityDto\SaveSearchDto;

interface SavedSearchServiceInterface
{

    public function createSavedSearches(SaveSearchDto $saveSearchDto, $loggedUserId);

    public function editSavedSearches(SaveSearchDto $saveSearchDto);

    public function cancelSavedsearch($searchId, $loggedUserId);

    public function getSavedSearch($searchId, $orgId, $userId);

    public function listSavedSearch($loggedUser, $orgId, $myTimezone);
}