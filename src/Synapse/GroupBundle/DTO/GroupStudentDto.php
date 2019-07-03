<?php

namespace Synapse\GroupBundle\DTO;

use JMS\Serializer\Annotation as JMS;

class GroupStudentDto {


    /**
     * ExternalId of the Group
     *
     * @var string
     */
    private $groupExternalId;


    /**
     * Name of the Group
     *
     * @var string
     */
    private $groupName;


    /**
     * List of student  in the Group
     *
     * @var array
     */
    private $studentList;

    /**
     * @return string
     */
    public function getGroupExternalId()
    {
        return $this->groupExternalId;
    }

    /**
     * @param string $groupExternalId
     */
    public function setGroupExternalId($groupExternalId)
    {
        $this->groupExternalId = $groupExternalId;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }


    /**
     * @return array
     */
    public function getStudentList()
    {
        return $this->studentList;
    }

    /**
     * @param array $studentList
     */
    public function setStudentList($studentList)
    {
        $this->studentList = $studentList;
    }


}