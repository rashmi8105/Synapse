<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Exception\ValidationException;


class AppointmentHelperService extends AbstractService
{

    protected function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    protected function dateValidation($date1, $date2, $vaildationType = null)
    {
        if ($vaildationType == "pastDate") {
            
            if ($date1 > $date2) {
                throw new ValidationException([
                    'Past appointments cannot be edited'
                ], 'Past appointments cannot be edited', 'Invalid_Date');
            }
        } else {
            
            if ($date2 < $date1) {
                throw new ValidationException([
                    'End date/time should be greater then start date/time'
                ], 'End date/time should be greater then start date/time', 'Invalid_Date');
            }
        }
    }
}