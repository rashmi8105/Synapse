<?php
namespace Synapse\RiskBundle\Service;

use Synapse\RiskBundle\EntityDto\RiskVariableDto;

interface RiskVariableServiceInterface
{
    public function getResourceTypes();
    public function getRiskVariables($status);    
    public function getRiskSourceIds($type);
    public function create(RiskVariableDto $riskVariableDto, $type);
    public function getRiskVariable($id);
    public function changeStatus($id);

}