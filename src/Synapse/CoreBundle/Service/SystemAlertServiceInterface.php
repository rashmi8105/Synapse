<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\SystemAlertDto;


interface SystemAlertServiceInterface {

    /**
     *
     * @param SystemAlerts $systemAlert

     */
    public function createSystemAlert(SystemAlertDto $systemAlertDto);
    
   
} 