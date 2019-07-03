<?php

namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\CoreBundleConstant;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("date_utility_service")
 */
class DateUtilityService extends AbstractService
{

    const SERVICE_KEY = 'date_utility_service';

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;


    /**
     * @param $repositoryResolver
     * @param $logger
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger")
     *            })
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
    }


    /**
     * Given an organization ID, adjusts a DateTime object to the organization's time zone.
     * It should be noted that the parameter $dateTimeToAdjust does not remain constant and
     * its value may change as a result. For example, $a = adjustDateTimeToOrganizationTimezone(2, $b)
     * will most likely change the value of $b.
     *
     * @param int $organizationId - The organization ID to use for adjusting the DateTime
     * @param \DateTime $dateTimeToAdjust - The DateTime to adjust to an organization's timezone
     * @return \DateTime
     */
    public function adjustDateTimeToOrganizationTimezone($organizationId, $dateTimeToAdjust)
    {
        $timeZoneName = $this->organizationRepository->find($organizationId)->getTimeZone();

        $metadataListValuesInstance = $this->metadataListValuesRepository->findOneBy(['listName' => $timeZoneName], new SynapseValidationException('Timezone does not exist'));

        if (isset($metadataListValuesInstance)) {
            $timeZoneKey = $metadataListValuesInstance->getListValue();
        } else {
            $timeZoneKey = SynapseConstant::SKYFACTOR_DEFAULT_TIMEZONE;
        }

        $dateTimeZone = new \DateTimeZone($timeZoneKey);
        $dateTimeToAdjust->setTimezone($dateTimeZone);

        return $dateTimeToAdjust;
    }


    /**
     * Uses the timezone for the organization to adjust the given DateTime,
     * and then formats it as requested.  The default format is a date yyyy-mm-dd.
     *
     * @param int $organizationId
     * @param \DateTime $dateTimeToFormat
     * @param string $format
     * @return string
     */
    public function getFormattedDateTimeForOrganization($organizationId, $dateTimeToFormat, $format = SynapseConstant::DATE_YMD_FORMAT)
    {
        $adjustedDateTime = $this->adjustDateTimeToOrganizationTimezone($organizationId, $dateTimeToFormat);
        $formattedDate = date_format($adjustedDateTime, $format);
        return $formattedDate;
    }


    /**
     * @param int $orgId - Using the given organization ID, gets the current DateTime object adjusted for the organization's time zone.
     * @return \DateTime - Current date time adjusted for the given organization
     */
    public function getTimezoneAdjustedCurrentDateTimeForOrganization($orgId)
    {
        return $this->adjustDateTimeToOrganizationTimezone($orgId, new \DateTime());
    }


    /**
     * Uses the timezone for the organization to determine the current DateTime for that organization,
     * and then formats it as requested.  The default format is a date yyyy-mm-dd.
     *
     * @param int $orgId
     * @param string $format
     * @return string
     */
    public function getCurrentFormattedDateTimeForOrganization($orgId, $format = SynapseConstant::DATE_YMD_FORMAT)
    {
        $orgDateTime = $this->getTimezoneAdjustedCurrentDateTimeForOrganization($orgId);
        $orgDate = date_format($orgDateTime, $format);
        return $orgDate;
    }


    /**
     * sets incoming date to datetime in UTC time using the org/university timezone
     *
     * @param INT $orgId
     * @param string $dateFromUserAsString
     * @param bool $isEndDate
     * @return string $utcDateTimeAsString
     */
    public function convertToUtcDatetime($orgId, $dateFromUserAsString, $isEndDate = false)
    {

        //get Organization TimeZone, if no timezone, concatenating string
        $reportOrganization = $this->organizationRepository->find($orgId);
        $timeZoneName = $reportOrganization->getTimeZone();
        $timeZone = $this->metadataListValuesRepository->findOneBy(['listName' => $timeZoneName], new SynapseValidationException('Timezone does not exist'));

        if ($isEndDate) {
            $dateFromUserAsString = $dateFromUserAsString . " 23:59:59";
        } else {
            $dateFromUserAsString = $dateFromUserAsString . " 00:00:00";
        }

        if ($timeZone) {
            $timeZoneKey = $timeZone->getListValue();
        } else {
            return $dateFromUserAsString; // if the org doesn't have a timezone
        }

        //Convert time to UTC datetime
        $orgTimezone = new \DateTimeZone($timeZoneKey);
        $convertedToDateTime = new \DateTime($dateFromUserAsString, $orgTimezone);
        $convertedToDateTime->setTimezone(new \DateTimeZone('UTC'));

        //Convert back to String
        $utcDateTimeAsString = $convertedToDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);


        return $utcDateTimeAsString;
    }


    /**
     * This function will return the ISO standard
     * Time Zone for an organization.
     *
     * @param $orgId => the numeric id of the organization
     */
    public function getOrganizationISOTimeZone($orgId)
    {
        $timeZoneName = $this->organizationRepository->find($orgId)->getTimeZone();
        $metadatatListValueTimeZone = $this->metadataListValuesRepository->findOneBy(['listName' => $timeZoneName], new SynapseValidationException('Timezone does not exist'));
        $timeZoneKey = $metadatatListValueTimeZone->getListValue();
        return $timeZoneKey;

    }


    /**
     * Takes a string formatted as a datetime in the database (e.g., "2016-05-01 05:00:00")
     * and converts it to the format that Javascript can understand when it is used in a JSON response (e.g., "2016-05-01T05:00:00+0000").
     * It does not change the time zone.  Both strings will usually be in UTC.
     *
     * @param string $databaseString
     * @return string
     */
    public function convertDatabaseStringToISOString($databaseString)
    {
        if (empty($databaseString)) {
            return null;
        } else {
            $dateTime = new \DateTime($databaseString);
            $timestamp = $dateTime->getTimestamp();
            $isoString = date(SynapseConstant::DATE_FORMAT_WITH_TIMEZONE, $timestamp);
            return $isoString;
        }
    }
    
    /**
     * Return date in UTC formate
     *
     * @param string $date
     * @param string $orgTimezone
     * @return date
     */
    public static function getUtcDate($date = null, $orgTimezone = null)
    {
        if ($date) {
            $dateTime = clone $date;
        } else {
            $dateTime = new \DateTime('now');
            $date = $dateTime;
        }
        if (! is_null($orgTimezone)) {
            try {
                $orgTimezone = new \DateTimeZone($orgTimezone);
                $dateTime = new \DateTime($date->format(SynapseConstant::DEFAULT_DATETIME_FORMAT), $orgTimezone);
            } catch (\Exception $e) {}
        }
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        return $dateTime;
    }


    /**
     * Method returns date range to on the basis of time period to fetch appointment data
     *
     * @param string $timePeriod
     * @param string $currentDate
     * @return array
     */
    public function getDateRange($timePeriod, $currentDate)
    {
        switch ($timePeriod) {
            case "past":
                $dateRange['from_date'] = $currentDate;
                $dateRange['to_date'] = $currentDate;
                break;
            case "current":
                $dateRange['from_date'] = date(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $dateRange['to_date'] = $this->moveDateStringInTime($currentDate, $key = 'sunday');
                break;
            case "next":
                $dateRange['from_date'] = $this->moveDateStringInTime($currentDate, $key = 'next monday');
                $dateRange['to_date'] = $this->moveDateStringInTime($dateRange['from_date'], $key = 'next sunday');
                break;
            case "next_two":
                $dateRange['from_date'] = $this->moveDateStringInTime($currentDate, $key = 'next monday');
                $dateRange['to_date'] = $this->moveDateStringInTime($dateRange['from_date'], $key = 'second sunday');
                break;
            case "current_month":
                $dateRange['from_date'] = date(SynapseConstant::DEFAULT_DATETIME_FORMAT);
                $dateRange['to_date'] = $this->moveDateStringInTime($currentDate, $key = 'last day of this month');
                break;
            case "next_month":
                $dateRange['from_date'] = $this->moveDateStringInTime($currentDate, $key = 'first day of next month');
                $dateRange['to_date'] = $this->moveDateStringInTime($currentDate, $key = 'last day of next month');
                break;
            default:
                $dateRange['from_date'] = $currentDate;
                $dateRange['to_date'] = $this->moveDateStringInTime($currentDate, $key = 'sunday');
                break;
        }

        return $dateRange;
    }

    /**
     * Creates DateString based on a time period relative to datetime passed into the function
     * currently uses Week definition of Monday through Sunday
     *
     * @param $timePeriod 'today'|'week'|'month'|'custom' (if none supplied, week is default)
     * @param \DateTime $dateTimeObject
     * @return array
     */
    public function buildDateTimeRangeByTimePeriodAndDateTimeObject($timePeriod, $dateTimeObject)
    {
        $dateTimeRange = [];

        $formattedDateTimeString = $dateTimeObject->format(SynapseConstant::DATE_YMD_FORMAT);

        switch ($timePeriod) {
            case "today":
                $dateTimeRange['from_date'] = $formattedDateTimeString;
                $dateTimeRange['to_date'] = $formattedDateTimeString;
                break;
            case "week":
                //If the current date is Monday(1) or Sunday(7), do not look forward or back
                //format('N') returns the day of the week as an integer
                if ($dateTimeObject->format('N') == 1) {
                    $dateTimeRange['from_date'] = $formattedDateTimeString;
                    $dateTimeRange['to_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = 'next sunday');
                } elseif ($dateTimeObject->format('N') == 7) {
                    $dateTimeRange['from_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = "previous monday");
                    $dateTimeRange['to_date'] = $formattedDateTimeString;
                } else {
                    $dateTimeRange['from_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = "previous monday");
                    $dateTimeRange['to_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = 'next sunday');
                }
                break;
            case "month":
                //format('F Y') returns the full string representation of month and year
                $month = $dateTimeObject->format('F Y');
                $dateTimeRange['from_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = 'first day of ' . $month);
                $dateTimeRange['to_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = 'last day of ' . $month);
                break;
            default:
                $dateTimeRange['from_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = "previous monday");
                $dateTimeRange['to_date'] = $this->moveDateStringInTime($formattedDateTimeString, $key = 'next sunday');
                break;
        }

        $dateTimeRange['from_date'] = $dateTimeRange['from_date'] . " 00:00:00";
        $dateTimeRange['to_date'] = $dateTimeRange['to_date'] . " 23:59:59";
        return $dateTimeRange;
    }


    /**
     * Moves a Date Forward or Backward in time based on a key
     * Key is the semantic explanation of how to move the datestring passed
     *
     * @param string $dateString - 'Y-m-d'
     * @param string $key 'previous $dayOfWeek'|'next $dayOfWeek'|'first day of $month'|'last day of $month'
     * @return bool|string
     */
    public function moveDateStringInTime($dateString, $key)
    {
        $dateParts = explode("-", $dateString);
        $newDateString = date(SynapseConstant::DATE_YMD_FORMAT , strtotime($key, mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0])));
        return $newDateString;
    }


    /**
     * Uses a DateTimeString and Organization to create a DateTime Object in UTC time
     *
     * @param string $dateTimeString - 'yyyy-mm-dd hh:mm:ss'
     * @param int $organizationId
     * @return \DateTime
     */
    public function adjustOrganizationDateTimeStringToUtcDateTimeObject($dateTimeString, $organizationId)
    {
        $timeZoneName = $this->organizationRepository->find($organizationId)->getTimeZone();
        $metadataListValueTimeZone = $this->metadataListValuesRepository->findOneBy(['listName' => $timeZoneName], new SynapseValidationException('Timezone does not exist'));
        $timeZoneKey = $metadataListValueTimeZone->getListValue();
        $dateTimeZone = new \DateTimeZone($timeZoneKey);
        $dateTimeObject = new \DateTime($dateTimeString, $dateTimeZone);
        $dateTimeObject->setTimezone(new \DateTimeZone('UTC'));

        return $dateTimeObject;
    }


    /**
     * Uses a DateTimeString and Organization to create a Formatted DateTime String in UTC time
     *
     * @param string $dateTimeString - 'yyyy-mm-dd hh:mm:ss'
     * @param int $organizationId
     * @param string $format - 'Y-m-d' or any Date/DateTime format
     * @return string
     */
    public function getFormattedCurrentUtcDateTimeStringFromOrganizationDateTimeString($dateTimeString, $organizationId, $format = SynapseConstant::DATE_YMD_FORMAT)
    {
        $utcDateTimeObject = $this->adjustOrganizationDateTimeStringToUtcDateTimeObject($dateTimeString, $organizationId);
        $utcDateString = $utcDateTimeObject->format($format);
        return $utcDateString;
    }

    /**
     * Gets the organization's Daylight Savings Time offset from UTC, as an integer value representing hours.
     *
     * @param \DateTimeZone $organizationDateTimeZoneObject
     * @param \DateTime $startDateTime
     * @param \DateTime $endDateTime
     * @return int
     */
    public function getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $startDateTime, $endDateTime)
    {

        //Format the datetimes as a string, then convert them to integer time values.
        $startDateString = $startDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $endDateString = $endDateTime->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $startTime = strtotime($startDateString);
        $endTime = strtotime($endDateString);

        //Get the transitions that occur in between the integer start and end datetimes.
        // The start date time's value will always be the first element in the array.
        $timezoneTransitions = $organizationDateTimeZoneObject->getTransitions($startTime, $endTime);

        //If there is a daylight savings time crossover in the gap between start and end date,
        // getTransitions() will return the start date transition and the daylight savings time transition.
        // The start date transition will always be the first element in the returned array. The daylight
        // savings time transition will always be the second element in the returned array.
        if (count($timezoneTransitions) > 1) {
            $offset = $timezoneTransitions[0]['offset'] - $timezoneTransitions[1]['offset'];
        } else {
            $offset = 0;
        }

        //Return the offset as an integer value for hour.
        return $offset / CoreBundleConstant::HOUR_AS_SECONDS ;
    }

    /**
     * Adjusts the passed in datetime to the first day (Sunday) of its week.
     *
     * @param \DateTime $datetime
     * @return mixed
     */
    public function getFirstDayOfWeekForDatetime($datetime)
    {
        return clone $datetime->sub(new \DateInterval('P' . $datetime->format('w') . 'D'));
    }

}