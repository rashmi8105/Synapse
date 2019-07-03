<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OfficeHoursSeriesDto
{

    /**
     * Indicator for the repetition pattern of the office hours series.
     * 'MWF', 'TT', 'W' for weekly repetition, 'D' for daily repetition, 'M' for monthly repetition.
     *
     * @JMS\Type("string")
     */
    private $repeatPattern;

    /**
     * Used in conjunction with repeat pattern. Frequency of pattern repetition within specified time frame.
     *
     * @JMS\Type("integer")
     */
    private $repeatEvery;

    /**
     * String of 1's and 0's indicating on which days of the week the series should be created.
     * Only used with weekly and monthly repeating series. Eg. 1010101 would be office hours on Sunday, Tuesday, Thursday, and Saturday.
     *
     * @JMS\Type("string")
     */
    private $repeatDays;

    /**
     * Used in conjunction with repeat range. This will be the number of times the office hour will occur in the series
     * as opposed to setting an end date for the series.
     *
     * @JMS\Type("integer")
     */
    private $repeatOccurence;

    /**
     * Indicator for the end date of the series. 'N' for no end date, 'D' for a specified end date, or 'E' for ending
     * after X occurrences.
     *
     * @JMS\Type("string")
     */
    private $repeatRange;

    /**
     * The length of the individual office hour units.
     *
     * @JMS\Type("integer")
     */
    private $meetingLength;

    /**
     * The week of the month on which to repeat the series.
     * Only used in monthly repeating series.
     *
     * @JMS\Type("integer")
     */
    private $repeatMonthlyOn;
    
    /**
     * Include weekends in the office hours series.
     * Only used with daily repeating series.
     *
     * @JMS\Type("integer")
     */
    private $includeSatSun;

    /**
     *
     * @param mixed $repeatRange            
     */
    public function setRepeatRange($repeatRange)
    {
        $this->repeatRange = $repeatRange;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatRange()
    {
        return $this->repeatRange;
    }

    /**
     *
     * @param mixed $repeatPattern            
     */
    public function setRepeatPattern($repeatPattern)
    {
        $this->repeatPattern = $repeatPattern;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatPattern()
    {
        return $this->repeatPattern;
    }

    /**
     *
     * @param mixed $repeatOccurence            
     */
    public function setRepeatOccurence($repeatOccurence)
    {
        $this->repeatOccurence = $repeatOccurence;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatOccurence()
    {
        return $this->repeatOccurence;
    }

    /**
     *
     * @param mixed $repeatEvery            
     */
    public function setRepeatEvery($repeatEvery)
    {
        $this->repeatEvery = $repeatEvery;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatEvery()
    {
        return $this->repeatEvery;
    }

    /**
     *
     * @param mixed $repeatDays            
     */
    public function setRepeatDays($repeatDays)
    {
        $this->repeatDays = $repeatDays;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatDays()
    {
        return $this->repeatDays;
    }

    /**
     *
     * @param mixed $meetingLength            
     */
    public function setMeetingLength($meetingLength)
    {
        $this->meetingLength = $meetingLength;
    }

    /**
     *
     * @return mixed
     */
    public function getMeetingLength()
    {
        return $this->meetingLength;
    }

    /**
     *
     * @param mixed $repeatMonthlyOn            
     */
    public function setRepeatMonthlyOn($repeatMonthlyOn)
    {
        $this->repeatMonthlyOn = $repeatMonthlyOn;
    }

    /**
     *
     * @return mixed
     */
    public function getRepeatMonthlyOn()
    {
        return $this->repeatMonthlyOn;
    }

    /**
     * @param mixed $includeSatSun
     */
    public function setIncludeSatSun($includeSatSun)
    {
        $this->includeSatSun = $includeSatSun;
    }

    /**
     * @return mixed
     */
    public function getIncludeSatSun()
    {
        return $this->includeSatSun;
    }


}