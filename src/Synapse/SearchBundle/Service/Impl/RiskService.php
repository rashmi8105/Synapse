<?php
namespace Synapse\SearchBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\SearchBundle\Service\RiskServiceInterface;
use Synapse\SearchBundle\EntityDto\RiskLevelsDto;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\SearchBundle\EntityDto\RiskLevelArrayDto;
use Synapse\SearchBundle\EntityDto\IntentToLeaveArrayDto;
use Synapse\SearchBundle\EntityDto\IntentToLeaveDto;
use Synapse\SearchBundle\Entity\IntentToLeave;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("risk_service")
 */
class RiskService extends AbstractService implements RiskServiceInterface
{

    const SERVICE_KEY = 'risk_service';

    /**
     *
     * @var personRepository
     */
    private $personRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger")
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
    }

    public function getRiskIndicatorsOrIntentToLeave($type)
    {
        $this->logger->debug(">>>> Get Risk IndicatorsOrIntentToLeave" . $type);
        if ($type == 'indicators') {
            $this->logger->info("Risk Service - Get RiskIndicators by Type");
            $this->riskLevelsRepository = $this->repositoryResolver->getRepository("SynapseRiskBundle:RiskLevels");
            $risks = $this->riskLevelsRepository->findAll();
            
            $riskArray = [];
            $riskLevelArrayDto = new RiskLevelArrayDto();
            
            if (! empty($risks)) {
                foreach ($risks as $risk) {
                    $riskLevelDto = new RiskLevelsDto();
                    $riskLevelDto->setRiskLevel($risk->getId());
                    $riskLevelDto->setRiskText($risk->getRiskText());
                    $riskLevelDto->setImageName($risk->getImageName());
                    $riskLevelDto->setColorHex($risk->getColorHex());
                    $riskArray[] = $riskLevelDto;
                }
                $riskLevelArrayDto->setRiskLevels($riskArray);
            }
            $risk = $riskLevelArrayDto;
        } 

        elseif ($type == 'intent_to_leave') {
            $this->logger->info("Risk Service - Get Intent to Leave by Type");
            $this->intentToLeaveRepository = $this->repositoryResolver->getRepository("SynapseSearchBundle:IntentToLeave");
            $risks = $this->intentToLeaveRepository->findAll();
            $intentToLeaveArray = [];
            $intentToLeaveArrayDto = new IntentToLeaveArrayDto();
            
            if (! empty($risks)) {
                foreach ($risks as $risk) {
                    $intentToLeaveDto = new IntentToLeaveDto();
                    $intentToLeaveDto->setId($risk->getId());
                    $intentToLeaveDto->setText($risk->getText());
                    $intentToLeaveDto->setImageName($risk->getImageName());
                    $intentToLeaveDto->setColorHex($risk->getColorHex());
                    $intentToLeaveArray[] = $intentToLeaveDto;
                }
                $intentToLeaveArrayDto->setIntentToLeaveTypes($intentToLeaveArray);
            }
            $risk = $intentToLeaveArrayDto;
        } 

        else {
            $this->logger->error("Risk Service - get Risk Indicators Or IntentToLeave -  Incorrect Filter Type");
            throw new ValidationException([
                'Incorrect Filter Type.'
            ], 'Incorrect Filter Type.', 'incorrect_filter_type');
        }
        return $risk;
    }
}