<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\RelatedActivitiesDto;

interface RelatedActivitiesServiceInterface
{
    public function createRelatedActivities(RelatedActivitiesDto $relatedActivitiesDto);
}