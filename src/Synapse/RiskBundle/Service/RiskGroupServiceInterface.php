<?php
namespace Synapse\RiskBundle\Service;

interface RiskGroupServiceInterface
{

    public function createGroup($riskGroupDto);

    public function editGroup($riskGroupDto);

    public function getRiskGroups();

    public function getRiskGroupById($id);

    public function getRiskModelAssingment($id);
}