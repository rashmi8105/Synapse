<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 *
 * @package Synapse\RestBundle\Entity
 */
class PersonDTO
{

    /**
     * issueId
     *
     * @var integer @JMS\Type("integer")
     */
    private $issueId;
    
    /**
     * First name of a person.
     *
     * @var string @JMS\Type("string")
     */
    private $firstName;

    /**
     * Last name of a person.
     *
     * @var string @JMS\Type("string")
     */
    private $lastName;

    /**
     * Title of the person, i.e. Dr., Mr., Sir, Count, etc.
     *
     * @var string @JMS\Type("string")
     */
    private $title;

    /**
     * Date that the person was born.
     *
     * @var datetime @JMS\Type("DateTime")
     */
    private $dateOfBirth;

    /**
     * Id for a person determined by the organization.
     *
     * @var string @JMS\Type("string")
     */
    private $externalId;

    /**
     * Username of a person. Usually is the person's email address.
     *
     * @var string @JMS\Type("string")
     */
    private $username;

    /**
     * A person's login password.
     *
     * @var string @JMS\Type("string")
     */
    private $password;

    /**
     * activationToken
     *
     * @var string @JMS\Type("string")
     */
    private $activationToken;

    /**
     * The date that a person accept their organization's confidentiality statement.
     *
     * @var datetime @JMS\Type("datetime")
     */
    private $confidentialityStmtAcceptDate;

    /**
     * The unique id that relates to a person.
     *
     * @var integer @JMS\Type("integer")
     */
    private $personid;

    /**
     * The organization that a person belongs to.
     *
     * @var integer @JMS\Type("integer")
     */
    private $organization;

    /**
     * Primary address of a person.
     *
     * @var string @JMS\Type("string")
     */
    private $address1;

    /**
     * Secondary address of a person.
     *
     * @var string @JMS\Type("string")
     */
    private $address2;

    /**
     * City that a person lives in.
     *
     * @var string @JMS\Type("string")
     */
    private $city;

    /**
     * Zip code that a person lives in.
     *
     * @var string @JMS\Type("string")
     */
    private $zip;

    /**
     * State that a person lives in.
     *
     * @var string @JMS\Type("string")
     */
    private $state;

    /**
     * Country that a person lives in.
     *
     * @var string @JMS\Type("string")
     */
    private $country;

    /**
     * Primary mobile phone number of a person.
     *
     * @var string @JMS\Type("string")
     */
    private $primaryMobile;

    /**
     * Alternate mobile number of a person. Not required.
     *
     * @var string @JMS\Type("string")
     */
    private $alternateMobile;

    /**
     * Home phone number of a person. Not required.
     *
     * @var string @JMS\Type("string")
     */
    private $homePhone;

    /**
     * Office phone number of a person. Not Required.
     *
     * @var string @JMS\Type("string")
     */
    private $officePhone;

    /**
     * Primary email address of a person. Required.
     *
     * @var string @JMS\Type("string")
     */
    private $primaryEmail;

    /**
     * Alternate email address of a person. Not required.
     *
     * @var string @JMS\Type("string")
     */
    private $alternateEmail;

    /**
     * Primary mobile phone service provider for a person.
     *
     * @var string @JMS\Type("string")
     */
    private $primaryMobileProvider;

    /**
     * Alternate mobile phone service provider for a person.
     *
     * @var string @JMS\Type("string")
     */
    private $alternateMobileProvider;

    /**
     * Id used to identify a person's contact information.
     *
     * @var string @JMS\Type("string")
     */
    private $contactinfoid;

    /**
     * Total number of students that are assigned to a person.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalStudents;

    /**
     * Total number of high priority students assigned to a person.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalHighPriorityStudents;

    /**
     * Object storing the risk levels of the students assigned to a person.
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\RiskLevelsDto>")
     */
    private $riskLevels;

    /**
     * red2
     *
     * @var string @JMS\Type("string")
     */
    private $red2;

    /**
     * red1
     *
     * @var string @JMS\Type("string")
     */
    private $red1;

    /**
     * yellow
     *
     * @var string @JMS\Type("string")
     */
    private $yellow;

    /**
     * green
     *
     * @var string @JMS\Type("string")
     */
    private $green;

    /**
     * gray
     *
     * @var string @JMS\Type("string")
     */
    private $gray;
    
    /**
     * Static list that a person belongs to.
     *
     * @var string @JMS\Type("string")
     */
    private $staticlistName;

    /**
     * Description of a person's static list.
     *
     * @var string @JMS\Type("string")
     */
    private $staticlistDescription;

    /**
     * Object storing all of the students assigned to a person.
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\TotalStudentsListDTO>")
     */
    private $totalStudentsList;
    
    /**
     * Number of pages that result from the pagination of the total list of students.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalPages;
    
    
    /**
     * Number of student results per page.
     *
     * @var integer @JMS\Type("integer")
     */
    private $recordsPerPage;
    
    
    /**
     * Number equivalent of the page that a person is on, i.e. page 3 of 15.
     *
     * @var integer @JMS\Type("integer")
     */
    private $currentPage;
    
    /**
     * Total number of student results.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalRecords;
    
    /**
     * Sets the id of an issue.
     *
     * @param int $issueId
     */
    public function setIssueId($issueId)
    {
    	$this->issueId = $issueId;
    }

    /**
     * Sets the activation token.
     *
     * @param string $activationToken            
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;
    }
    
    /**
     * Returns the activation token.
     *
     * @return string
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * Sets the date that a person accepts an organization's confidentiality statement.
     *
     * @param datetime $confidentialityStmtAcceptDate
     */
    public function setConfidentialityStmtAcceptDate($confidentialityStmtAcceptDate)
    {
        $this->confidentialityStmtAcceptDate = $confidentialityStmtAcceptDate;
    }

    /**
     * Returns the date that a person accepts an organization's confidentiality statement.
     *
     * @return datetime
     */
    public function getConfidentialityStmtAcceptDate()
    {
        return $this->confidentialityStmtAcceptDate;
    }

    /**
     * Sets the date of birth for a person.
     *
     * @param datetime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * Returns the date of birth for a person.
     *
     * @return datetime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Sets the external Id of a person.
     *
     * @param string $externalId            
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * Returns the external Id of a person.
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Sets the first name of a person.
     *
     * @param string $firstName            
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the first name of a person.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the id of a person.
     *
     * @param integer $personid
     */
    public function setPersonId($personid)
    {
        $this->personid = $personid;
    }

    /**
     * Returns the id of a person.
     *
     * @return int
     */
    public function getPersonId()
    {
        return $this->personid;
    }

    /**
     * Sets the password of a person.
     *
     * @param string $password            
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the password of a person.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the title of a person.
     *
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the title of a person.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the username of a person.
     *
     * @param string $username            
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns the username of a person.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the primary address of a person.
     *
     * @param string $address1            
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * Returns the primary address of a person.
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Sets the secondary address of a person.
     *
     * @param string $address2            
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Returns the secondary address of a person.
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Sets the alternate email of a person.
     *
     * @param string $alternateEmail            
     */
    public function setAlternateEmail($alternateEmail)
    {
        $this->alternateEmail = $alternateEmail;
    }

    /**
     * Returns the alternate email of a person.
     *
     * @return string
     */
    public function getAlternateEmail()
    {
        return $this->alternateEmail;
    }

    /**
     * Sets the alternate mobile number of a person.
     *
     * @param string $alternateMobile            
     */
    public function setAlternateMobile($alternateMobile)
    {
        $this->alternateMobile = $alternateMobile;
    }

    /**
     * Returns the alternate mobile number of a person.
     *
     * @return string
     */
    public function getAlternateMobile()
    {
        return $this->alternateMobile;
    }

    /**
     * Sets the alternate mobile provider for a person.
     *
     * @param string $alternateMobileProvider            
     */
    public function setAlternateMobileProvider($alternateMobileProvider)
    {
        $this->alternateMobileProvider = $alternateMobileProvider;
    }

    /**
     * Returns the alternate mobile provider for a person.
     *
     * @return string
     */
    public function getAlternateMobileProvider()
    {
        return $this->alternateMobileProvider;
    }

    /**
     * Sets the contact information id for a person.
     *
     * @param string $contactinfoid            
     */
    public function setContactinfoid($contactinfoid)
    {
        $this->contactinfoid = $contactinfoid;
    }

    /**
     * Returns the contact information id for a person.
     *
     * @return string
     */
    public function getContactinfoid()
    {
        return $this->contactinfoid;
    }

    /**
     * Sets the city for a person.
     *
     * @param string $city            
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the city for a person.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the country for a person.
     *
     * @param string $country            
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Returns the country for a person.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the home phone of a person.
     *
     * @param string $homePhone            
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;
    }

    /**
     * Returns the home phone of a person.
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Sets the office phone number of a person.
     *
     * @param string $officePhone            
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;
    }

    /**
     * Returns the office phone number of a person.
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Sets the primary email for a person.
     *
     * @param string $primaryEmail            
     */
    public function setPrimaryEmail($primaryEmail)
    {
        $this->primaryEmail = $primaryEmail;
    }

    /**
     * Returns the primary email for a person.
     *
     * @return string
     */
    public function getPrimaryEmail()
    {
        return $this->primaryEmail;
    }

    /**
     * Sets the primary mobile phone number for a person.
     *
     * @param string $primaryMobile            
     */
    public function setPrimaryMobile($primaryMobile)
    {
        $this->primaryMobile = $primaryMobile;
    }

    /**
     * Returns the primary mobile phone number for a person.
     *
     * @return string
     */
    public function getPrimaryMobile()
    {
        return $this->primaryMobile;
    }

    /**
     * Sets the primary mobile provider for a person.
     *
     * @param string $primaryMobileProvider            
     */
    public function setPrimaryMobileProvider($primaryMobileProvider)
    {
        $this->primaryMobileProvider = $primaryMobileProvider;
    }

    /**
     * Returns the primary mobile provider for a person.
     *
     * @return string
     */
    public function getPrimaryMobileProvider()
    {
        return $this->primaryMobileProvider;
    }

    /**
     * Sets the state(location) for a person.
     *
     * @param string $state            
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Returns the state(location) for a person.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the zip code for a person.
     *
     * @param string $zip            
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the zip code for a person.
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the organization(id) for a person.
     *
     * @param int $organization            
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * Returns the organization(id) for a person.
     *
     * @return int
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Returns the last name of a person.
     *
     * @return int
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Returns the last name of a person.
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Sets the risk levels for an organization.
     *
     * @param Object $riskLevels            
     */
    public function setRiskLevels($riskLevels)
    {
        $this->riskLevels = $riskLevels;
    }

    /**
     * Returns the risk levels for an organization.
     *
     * @return Object
     */
    public function getRiskLevels()
    {
        return $this->riskLevels;
    }

    /**
     * Sets the total number of students within an organization.
     *
     * @param integer $totalStudents            
     */
    public function setTotalStudents($totalStudents)
    {
        $this->totalStudents = $totalStudents;
    }

    /**
     * Returns the total number of students within an organization.
     *
     * @return integer
     */
    public function getTotalStudents()
    {
        return $this->totalStudents;
    }

    /**
     * Sets the total number of students within an organization.
     *
     * @param integer $totalHighPriorityStudents            
     */
    public function setTotalHighPriorityStudents($totalHighPriorityStudents)
    {
        $this->totalHighPriorityStudents = $totalHighPriorityStudents;
    }

    /**
     * Returns the total number of high priority students within an organization.
     *
     * @return integer
     */
    public function getTotalHighPriorityStudents()
    {
        return $this->totalHighPriorityStudents;
    }

    /**
     *
     * @param string $red2
     */
    public function setRed2($red2)
    {
        $this->red2 = $red2;
    }

    /**
     *
     * @return string
     */
    public function getRed2()
    {
        $this->red2;
    }

    /**
     *
     * @param string $red1
     */
    public function setRed1($red1)
    {
        $this->red1 = $red1;
    }

    /**
     *
     * @return string
     */
    public function getRed1()
    {
        $this->red1;
    }

    /**
     *
     * @param string $yellow            
     *
     */
    public function setYellow($yellow)
    {
        $this->yellow = $yellow;
    }

    /**
     *
     * @return string
     */
    public function getYellow()
    {
        $this->yellow;
    }

    /**
     *
     * @param string $green            
     *
     */
    public function setGreen($green)
    {
        $this->green = $green;
    }

    /**
     *
     * @return string
     */
    public function getGreen()
    {
        return $this->green;
    }

    /**
     * Sets the name of a static list.
     *
     * @param string $staticlistName            
     */
    public function setStaticlistName($staticlistName)
    {
        $this->staticlistName = $staticlistName;
    }

    /**
     * Returns the name of a static list.
     *
     * @return string
     */
    public function getStaticlistName()
    {
        $this->staticlistName;
    }

    /**
     * Sets the description of a static list.
     *
     * @param string $staticlistDescription
     */
    public function setStaticlistDescription($staticlistDescription)
    {
        $this->staticlistDescription = $staticlistDescription;
    }

    /**
     * Returns the description of a static list.
     *
     * @return string
     */
    public function getStaticlistDescription()
    {
        $this->staticlistDescription;
    }


    /**
     * Sets the list of every student object within an organization.
     *
     * @param array $totalStudentsList
     */
    public function setTotalStudentsList($totalStudentsList)
    {
        $this->totalStudentsList = $totalStudentsList;
    }

    /**
     * Returns the list of every student object within an organization.
     *
     * @return Object
     */
    public function getTotalStudentsList()
    {
        return $this->totalStudentsList;
    }

    /**
     * Sets the current page of student records.
     *
     * @return string
     */
    public function setCurrentPage($currentPage){
    
        $this->currentPage = $currentPage;
    }

    /**
     * Returns the current page of student records.
     *
     * @return string
     */
    public function setTotalPages($totalPages){
    
        $this->totalPages = $totalPages;
    }

    /**
     * Sets the number of student records(results) per page.
     *
     * @return string
     */
    public function setRecordsPerPage($recordsPerPage){
    
        $this->recordsPerPage = $recordsPerPage;
    }
    
    /**
     * Returns the number of student records(results) per page.
     *
     * @param integer $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }
    
    /**
     * Returns the total number of student records.
     *
     * @return integer
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }
    
    /**
     *
     * @param string $gray
     *
     */
    public function setGray($gray)
    {
        $this->gray = $gray;
    }
    
    /**
     *
     * @return string
     */
    public function getGray()
    {
        return $this->gray;
    }
}