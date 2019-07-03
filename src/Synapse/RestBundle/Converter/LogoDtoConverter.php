<?php
namespace Synapse\RestBundle\Converter;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Entity\OrganizationProfileDTO;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\RestBundle\Entity\LogoDto;

/**
 * Class LogoDtoConverter
 * @DI\Service("logodto_converter")
 */
class LogoDtoConverter
{

    const SERVICE_KEY = 'logodto_converter';

    /**
     * Generate the Organization Details response
     *
     * @param Organization $logoInfo
     * @param int $numberOfUsersEnabledCalendar
     * @return LogoDto
     */
    public function createLogoResponse($logoInfo, $numberOfUsersEnabledCalendar = 0)
    {
        $logoDto = new LogoDto();

        $logoDto->setLogoFileName($logoInfo->getLogoFileName());
        $logoDto->setOrganizationId($logoInfo->getId());
        $logoDto->setPrimaryColor($logoInfo->getPrimaryColor());
        $logoDto->setSecondaryColor($logoInfo->getSecondaryColor());
        $logoDto->setEbiConfidentialityStatement($logoInfo->getEbiConfidentialityStatement());
        $logoDto->setInactivityTimeout($logoInfo->getInactivityTimeout());
        $logoDto->setAcademicUpdateNotification($logoInfo->getAcademicUpdateNotification());
        $logoDto->setReferForAcademicAssistance($logoInfo->getReferForAcademicAssistance());
        $logoDto->setSendToStudent($logoInfo->getSendToStudent());
        $logoDto->setCanViewAbsences(($logoInfo->getCanViewAbsences()) ? $logoInfo->getCanViewAbsences() : false);
        $logoDto->setCanViewInProgressGrade(($logoInfo->getCanViewInProgressGrade()) ? $logoInfo->getCanViewInProgressGrade() : false);
        $logoDto->setCanViewComments(($logoInfo->getCanViewComments()) ? $logoInfo->getCanViewComments() : false);
        $calendarType = NULL;
        if ($logoInfo->getPcs() == 'G') {
            $calendarType = 'google';
        }

        $logoDto->setCalendarType($calendarType);
        $logoDto->setCalendarSyncUsers($numberOfUsersEnabledCalendar);
        $logoDto->setCalendarSync(($logoInfo->getCalendarSync()) ? true : false);
        return $logoDto;
    }
}