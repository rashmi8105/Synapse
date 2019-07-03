<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\AcademicUpdateBundle\EntityDto\ProfileDto;

class AcademicUpdateCreateDto
{
    /**
     * The ID of the organization that is using mapworks. This ID must be populated.
     *
     * @var int @JMS\Type("integer")
     *  @Assert\NotBlank(message = "Organization should not be blank")
     */
    private $organizationId;

    /**
     * The ID of an Academic Update Request.
     *
     * @var int @JMS\Type("integer")
     */
    private $id;

    /**
     * The name of a specific Academic Update Request.
     *
     * @var string @JMS\Type("string")
     */
    private $requestName;

    /**
     * The description of an Academic Update Request.
     *
     * @var string @JMS\Type("string")
     *      @Assert\Length(
     *      max = 140,
     *     
     *      maxMessage = "profileBlockName cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $requestDescription;

    /**
     * The date that an Academic Update is due.
     *
     * @var \DateTime @JMS\Type("DateTime<'m/d/Y'>")
     */
    private $requestDueDate;

    /**
     * The subject line of the email that is sent to all faculty that apply to an Academic Update Request.
     *
     * @var string @JMS\Type("string")
     */
    private $requestEmailSubject;

    /**
     * The optional message that can be included with the email that is sent to faculty applying to an Academic Update Request.
     *
     * @var string @JMS\Type("string")
     * @Assert\Length(
     *      max = 65536,
     *     
     *      maxMessage = "Email Optional Message cannot be longer than {{ limit }} characters."
     *      )
     */
    private $requestEmailOptionalMessage;

    /**
     * Students JSON object. See attributes below for details.
     *
     * @var StudentsDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\StudentsDto")
     */
    private $students;

    /**
     * Staff JSON object. See attributes below for details.
     *
     * @var StaffDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\StaffDto")
     */
    private $staff;

    /**
     * Groups JSON object. See attributes below for details.
     *
     * @var GroupsDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\GroupsDto")
     */
    private $groups;

    /**
     * Courses JSON object. See attributes below for details.
     *
     * @var CoursesDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\CoursesDto")
     */
    private $courses;

    /**
     * Profile JSON object. See attributes below for details.
     *
     * @var ProfileDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\ProfileDto")
     */
    private $profile_items;

    /**
     * StaticList JSON object. See attributes below for details.
     *
     * @var StaticListDto @JMS\Type("Synapse\AcademicUpdateBundle\EntityDto\StaticListDto")
     */
    private $static_list;
    
    /**
     * Number of Academic Update Requests made in a single session.
     *
     * @var integer @JMS\Type("integer")
     */
    private $updateCount;


    /**
     * Returns the courses that are included in an Academic Update Request.
     *
     * @return CoursesDto
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Set what courses an Academic Update Request applies to.
     *
     * @param CoursesDto $courses
     */
    public function setCourses($courses)
    {
        $this->courses = $courses;
    }

    /**
     * Returns Groups included in an Academic Update Request.
     *
     * @return GroupsDto
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set Groups to an Academic Update Request.
     *
     * @param GroupsDto $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * Returns the description of an Academic Update Request.
     *
     * @return string
     */
    public function getRequestDescription()
    {
        return $this->requestDescription;
    }

    /**
     * Set the description of an Academic Update Request.
     *
     * @param string $requestDescription
     */
    public function setRequestDescription($requestDescription)
    {
        $this->requestDescription = $requestDescription;
    }

    /**
     * Returns the date that an Academic Update Request is due.
     *
     * @return \DateTime
     */
    public function getRequestDueDate()
    {
        return $this->requestDueDate;
    }

    /**
     * Set the date that an Academic Update Request is due.
     *
     * @param \DateTime $requestDueDate
     */
    public function setRequestDueDate($requestDueDate)
    {
        $this->requestDueDate = $requestDueDate;
    }

    /**
     * Returns the optional message in the email for an Academic Update Request.
     *
     * @return string
     */
    public function getRequestEmailOptionalMessage()
    {
        return $this->requestEmailOptionalMessage;
    }

    /**
     * Set the optional message in the email for an Academic Update Request.
     *
     * @param string $requestEmailOptionalMessage
     */
    public function setRequestEmailOptionalMessage($requestEmailOptionalMessage)
    {
        $this->requestEmailOptionalMessage = $requestEmailOptionalMessage;
    }

    /**
     * Returns the subject of an Academic Update Request email.
     *
     * @return string
     */
    public function getRequestEmailSubject()
    {
        return $this->requestEmailSubject;
    }

    /**
     * Set the subject of an Academic Update Request email.
     *
     * @param string $requestEmailSubject
     */
    public function setRequestEmailSubject($requestEmailSubject)
    {
        $this->requestEmailSubject = $requestEmailSubject;
    }

    /**
     * Returns the name of an Academic Update Request.
     *
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Set the name of an Academic Update Request.
     *
     * @param string $requestName
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
    }

    /**
     * Returns the array of staff that are included in an Academic Update Request.
     *
     * @return StaffDto
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set the array of staff that are included in an Academic Update Request.
     *
     * @param StaffDto $staff
     */
    public function setStaff($staff)
    {
        $this->staff = $staff;
    }

    /**
     * Returns the students that are included in an Academic Update Request.
     *
     * @return StudentsDto
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Set the students that will be included in an Academic Update Request.
     *
     * @param StudentsDto $students
     */
    public function setStudents($students)
    {
        $this->students = $students;
    }

    /**
     * Returns the ID of an Academic Update Request.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the ID of an Academic Update Request.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns Profile Items.
     *
     * @return ProfileDto
     */
    public function getProfileItems()
    {
        return $this->profile_items;
    }

    /**
     * Set Profile Items.
     *
     * @param array $profile_items
     */
    public function setProfileItems($profile_items)
    {
        $this->profile_items = $profile_items;
    }

    /**
     * Returns the ID of the organization that is using mapworks.
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set the ID of the Organization that is using mapworks.
     *
     * @param int $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;

    }

    /**
     * Returns the number of Academic Update Requests made in a single session.
     *
     * @return int
     */
    public function getUpdateCount()
    {
        return $this->updateCount;
    }

    /**
     * Set the number of Academic Updates requested in a single session.
     *
     * @param int $updateCount
     */
    public function setUpdateCount($updateCount)
    {
        $this->updateCount = $updateCount;
    }

    /**
     * Returns a specified list of students.
     *
     * @return StaticListDto
     */
    public function getStaticList()
    {
        return $this->static_list;
    }

    /**
     * Set a specified list of students.
     *
     * @param array $static_list
     */
    public function setStaticList($static_list)
    {
        $this->static_list = $static_list;
    }


    
}