<?php
namespace Synapse\RiskBundle\Service;

interface RiskCalculationServiceInterface
{

    public function createRiskCalculationInput($riskCalculationInputDto);

    public function getCalculatedRiskVariables($personId, $start, $end, $riskmodel, $org_id);

    public function getRiskScores($personId, $start, $end, $riskmodel);

    public function scheduleRiskJob($riskScheduleDto);

    public function invokeRiskCalculation();
}