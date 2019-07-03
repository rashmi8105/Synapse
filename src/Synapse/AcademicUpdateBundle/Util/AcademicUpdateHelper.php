<?php
namespace Synapse\AcademicUpdateBundle\Util;

use Synapse\RestBundle\Exception\ValidationException;

class AcademicUpdateHelper
{

    public static function checkEmpty($students)
    {
        $returnStr = "";
        if (count($students) > 0) {
            $students = array_column($students, 'person_id');
            $returnStr = implode(",", $students);
        } else {
            $returnStr = "";
        }
        return $returnStr;
    }

    public static function getMetadataValues($profileIsp)
    {
        $values = '';

        $itemDataType = $profileIsp['item_data_type'];
        if ($itemDataType == 'S') {
            $ispCatArray = array_column($profileIsp['category_type'], 'value');
            $values .= implode(",", $ispCatArray);
        } elseif ($itemDataType == 'D') {

            $values .= $profileIsp['start_date'] . "," . $profileIsp['end_date'];
        } elseif ($itemDataType == 'N') {
            if ($profileIsp['is_single']) {

                $values .= $profileIsp['single_value'];
            } else {

                $values .= $profileIsp['min_digits'] . "," . $profileIsp['max_digits'];
            }
        } else {
            $values = '';
        }

        return $values;
    }

    public static function validateOrganizationOnRequest($orgId, $academicUpdateCreateDto)
    {
        if ($orgId != $academicUpdateCreateDto->getOrganizationId()) {
            throw new ValidationException([

                'Invalid Organization'
            ], 'Invalid Organization', 'au_organization_error');
        } else {
            return true;
        }
    }

    public static function checkAcademicUpdateCreated($createFlag)
    {
        if (!$createFlag) {
            throw new ValidationException([
                'No Related Data found to create Academic Update'
            ], 'No Related Data found to create Academic Update', 'academic_update_create_exception');
        } else {
            true;
        }
    }

    /**
     * @deprecated
     */
    public static function getNullIfEmpty($val)
    {
        return empty($val) ? null : $val;
    }

    public static function dueDateAcademicYearCheck($getAcademicDates)
    {
        if ($getAcademicDates['oayCount'] <= 0) {
            throw new ValidationException([
                'Invalid due date. Please enter another.'
            ], 'Invalid due date. Please enter another.', 'academic_update_due_date_error');
        }
    }
}