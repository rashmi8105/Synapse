<?php
namespace Synapse\RiskBundle\Service;

interface RiskModelCreateServiceInterface
{

    public function createModel($riskModelDto);
    
    public function updateModel($riskModelDto);
}