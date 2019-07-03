<?php
namespace Synapse\SearchBundle\Service;

interface RiskServiceInterface
{

    public function getRiskIndicatorsOrIntentToLeave($type);
}