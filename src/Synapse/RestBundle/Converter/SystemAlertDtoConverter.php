<?php
namespace Synapse\RestBundle\Converter;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Entity\SystemAlertDto;
use Synapse\CoreBundle\Entity\SystemAlerts;

/**
 * DTO converter
 *
 * Helper class to convert entities to data transfer objects
 * This class only contain converter that could be shared across different REST end points
 * Some end points could have different implementation accordingly with its needs
 *
 * @DI\Service("alertdto_converter")
 */
class SystemAlertDtoConverter
{
    const SERVICE_KEY = 'alertdto_converter';

    public function createAlertResponse($alert)
    {
       
        $alertDto = new SystemAlertDto();
        $alertDto->setId($alert->getId());
        $alertDto->setMessage($alert->getDescription());
        $alertDto->setStartDateTime($alert->getStartDate());
        $alertDto->setEndDateTime($alert->getEndDate());
        
        return $alertDto;
    }
    
}