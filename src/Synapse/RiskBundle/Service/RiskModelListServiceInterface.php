<?php
namespace Synapse\RiskBundle\Service;

interface RiskModelListServiceInterface
{

    public function getModelList($status);
    
    public function getModelAssignments($filter);
}