<?php
namespace Synapse\CampusConnectionBundle\Service\Impl;

use Monolog\Logger;
use Synapse\CampusConnectionBundle\EntityDto\StudentListDto;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RestBundle\Exception\ValidationException;


class CampusConnectionServiceHelper extends AbstractService
{

    /**
     * Gets the current dateTime adjusted by timezone
     *
     * @param \DateTime $timeZone
     * @param string $dateFormat
     *
     * @return string
     */
    protected function getDateByTimezone($timeZone, $dateFormat)
    {
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timeZone));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format($dateFormat);
        return $currentDate;
    }

    /**
     * Maps students to their faculty campus connections
     *
     * @param array $campusConnections
     *
     * @return array
     */
    protected function mapStudentFacultyArray($campusConnections)
    {
        $studentFaculty = array();
        if (isset($campusConnections) && count($campusConnections) > 0) {
            foreach ($campusConnections as $campusConnection) {
                $studentFaculty[$campusConnection['student_id']][] = $campusConnection['faculty_id'];
            }
        }
        return $studentFaculty;
    }

    /**
     * Returns a student array for assigning primary connections
     *
     * @param StudentListDto[] $staffList
     *
     * @return array
     */
    protected function prepareStudentArray($staffList)
    {
        $studentArray = array();
        foreach ($staffList as $staff) {
            if (! in_array($staff->getStudentId(), $studentArray)) {
                $studentArray[] = $staff->getStudentId();
            }
        }
        return $studentArray;
    }

    /**
     * Gets an array of faculty with campus connection details
     *
     * @param array $campusConnections
     *
     * @return array
     */
    protected function getFacultyDetailsArray($campusConnections)
    {
        $facultyDetails = [];
        foreach ($campusConnections as $campusConnection) {
            $facultyDetails[$campusConnection['person_id']]['id'] = $campusConnection['person_id'];
            $facultyDetails[$campusConnection['person_id']]['fname'] = $campusConnection['fname'];
            $facultyDetails[$campusConnection['person_id']]['lname'] = $campusConnection['lname'];
            $facultyDetails[$campusConnection['person_id']]['title'] = $campusConnection['title'];
            $facultyDetails[$campusConnection['person_id']]['email'] = $campusConnection['email'];
            $facultyDetails[$campusConnection['person_id']]['externalId'] = $campusConnection['external_id'];
            if($campusConnection['is_invisible']){
                $facultyDetails[$campusConnection['person_id']]['is_invisible'] = $campusConnection['is_invisible'];
            }else{
                $facultyDetails[$campusConnection['person_id']]['is_invisible'] = false;
            }
            $associatedWith = array();
            $associatedWith['flag'] = $campusConnection['flag'];
            $associatedWith['course_or_group_id'] = $campusConnection['course_or_group_id'];
            $associatedWith['course_or_group_name'] = $campusConnection['course_or_group_name'];
            $facultyDetails[$campusConnection['person_id']]['details'][] = $associatedWith;
        }
        return $facultyDetails;
    }

    /**
     * Gets an array of campus connections with the primary campus connection listed first
     *
     * @param array $facultyCampusConnection
     * @param integer $personPrimary
     *
     * @return array
     */
    protected function getPrimaryConnectionFirstArray($facultyCampusConnection, $personPrimary)
    {
        $primaryConnection = array();
        $otherConnection = array();
        foreach ($facultyCampusConnection as $key => $faculty) {
            if ($key == $personPrimary) {
                $primaryConnection[$key] = $faculty;
            } else {
                $otherConnection[$key] = $faculty;
            }
        }
        $campusConnection = array_merge($primaryConnection, $otherConnection);
        return $campusConnection;
    }

    /**
     * Gets students and their faculty campus connections
     *
     * @param array $facultyStudents
     * @param integer $facultyId
     *
     * @return array
     */
    protected function getFacultyStudentsArray($facultyStudents, $facultyId)
    {
        $studentArray = array();
        foreach ($facultyStudents as $key => $students) {
            $uniqueFaculty = array_unique($students);
            if (in_array($facultyId, $uniqueFaculty)) {
                $student = array();
                $student['student_id'] = $key;
                $studentArray[] = $student;
            }
        }
        return $studentArray;
    }
}