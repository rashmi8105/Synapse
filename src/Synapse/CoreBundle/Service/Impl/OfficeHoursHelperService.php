<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\OfficeHoursServiceInterface;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Entity\Person;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use \DateTime;
use Synapse\CoreBundle\Entity\OfficeHoursSeries;
use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;

class OfficeHoursHelperService extends AbstractService
{

    protected function isValidEndDate($officeHoursDto)
    {
        if ($officeHoursDto->getSlotEnd() < $officeHoursDto->getSlotStart()) {
            throw new ValidationException([
                'Invalid End Slot Date.'
            ], 'Invalid End Slot Date', 'invalid_end_date');
        }
    }

    protected function isValidStartDate($currentDateTime, $start)
    {
        if ($currentDateTime > $start) {
            throw new ValidationException([
                'Invalid Start Slot Date.'
            ], 'Invalid Start Slot Date', 'invalid_start_date');
        } 
    }

    protected function isOfficeHourExists($officeHourSeries)
    {
        if (! $officeHourSeries) {
            throw new ValidationException([
                'Office Hour Series Not Found.'
            ], 'Office Hour Series Not Found.', 'invalid_office_hour_series');
        }
    }

    protected function getDaysSelected($officeHourSeries)
    {
        $daysSelected = $officeHourSeries->getDays();
        // It splited to days to values
        $daysSelected = str_split($daysSelected);
        $remove = [
            0
        ];
        // This will give you the keys which are 1
        $daysSelected = array_keys(array_diff($daysSelected, $remove));
        return $daysSelected;
    }

    /**
     * Get the office hours series end date
     *
     * @param $startSlot
     * @param $officeHourSeries
     * @param $noOfTimesRepeat
     * @return DateTime
     */
    protected function getOfficeHoursEndDate($startSlot, $officeHourSeries, $noOfTimesRepeat)
    {
        if ($officeHourSeries->getRepetitionRange() == 'E') {
            $officeHourSeriesEndDate = clone $startSlot;
            $officeHourSeriesEndDate->add(new \DateInterval('P' . $officeHourSeries->getRepeatEvery() * $noOfTimesRepeat . 'M'));
            $officeHourSeriesEndDate->setTime($officeHourSeries->getSlotEnd()
                ->format('H'), $officeHourSeries->getSlotEnd()
                ->format('i'));
        } else {
            $officeHourSeriesEndDate = clone $officeHourSeries->getSlotEnd();
        }
        return $officeHourSeriesEndDate;
    }

    protected function getStartSlot($startSlot, $currentDate)
    {
        for ($i = 1; $i < 7; $i ++) {
            if ($startSlot < $currentDate) {
                $startSlot->add(new \DateInterval('P1D'));
            } else {
                break;
            }
        }
    }

    protected function isOfficeHourFound($officehours)
    {
        if (! $officehours) {
            throw new ValidationException([
                'Office Hour  Not Found.'
            ], 'Office Hour  Not Found.', 'invalid_office_hour');
        }
    }

    protected function isDateGreater($officeHoursDto)
    {
        if ($officeHoursDto->getSlotEnd() < $officeHoursDto->getSlotStart()) {
            throw new ValidationException([
                self::ERROR_DATE_GREATER
            ], self::ERROR_DATE_GREATER, 'Invalid_Date');
        }
    }

    /**
     * This function will set all common values for Office Hours
     */
    protected function getOfficeHoursWithDefault(OfficeHoursSeries $officeHourSeries)
    {
        $officehours = new OfficeHours();
        $officehours->setPerson($officeHourSeries->getPerson());
        $officehours->setPersonProxyCreated($officeHourSeries->getPersonProxy());
        $officehours->setOrganization($officeHourSeries->getPerson()
            ->getOrganization());
        $officehours->setSlotType('S');
        $officehours->setOfficeHoursSeries($officeHourSeries);
        $officehours->setLocation($officeHourSeries->getLocation());
        $officehours->setMeetingLength($officeHourSeries->getMeetingLength());
        return $officehours;
    }
}
