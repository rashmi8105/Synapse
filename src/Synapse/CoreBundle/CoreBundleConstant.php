<?php

namespace Synapse\CoreBundle;

class CoreBundleConstant
{
    //Date Interval constants

    const DATE_INTERVAL_ONE_DAY = 'P1DT0H0M0S';

    const DATE_INTERVAL_ONE_WEEK = 'P7DT0H0M0S';

    const DATE_INTERVAL_SET_HOURS = 'H0M0S';

    const DATE_INTERVAL_SET_HOURS_HEADER = 'PT';

    const DATE_INTERVAL_SET_DAYS = "DT0H0M0S";

    const DATE_INTERVAL_SET_DAYS_HEADER = 'P';


    // Date format constants

    const DATE_FORMAT_HOURS = 'H';

    const DATE_FORMAT_MINUTES = 'i';

    const DATE_FORMAT_DAY_OF_WEEK = 'N';

    const DATE_FORMAT_MONTH = 'm';


    // Days of the week constants

    const MONDAY = 1;

    const TUESDAY = 2;

    const WEDNESDAY = 3;

    const THURSDAY = 4;

    const FRIDAY = 5;

    const SATURDAY = 6;

    const SUNDAY = 7;


    // Time constants

    const HOUR_AS_SECONDS = 3600;
}