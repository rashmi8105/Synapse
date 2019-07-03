<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * AppointmentRecepientAndStatus
 *
 * @ORM\Table(name="appointment_recepient_and_status", indexes={@ORM\Index(name="fk_appointment_recepient_organization1_idx", columns={"organization_id"}), @ORM\Index(name="fk_appointment_recepient_appointments1_idx", columns={"appointments_id"}), @ORM\Index(name="fk_appointment_recepient_person1_idx", columns={"person_id_faculty"}), @ORM\Index(name="fk_appointment_recepient_and_status_person1_idx", columns={"person_id_student"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AppointmentRecepientAndStatusRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AppointmentRecepientAndStatus extends BaseEntity implements OwnableAssetEntityInterface
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Appointments @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Appointments")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="appointments_id", referencedColumnName="id")
     *      })
     */
    private $appointments;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     *      })
     */
    private $personIdFaculty;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     *      })
     */
    private $personIdStudent;

    /**
     *
     * @var boolean @ORM\Column(name="has_attended", type="boolean", length=1, nullable=true)
     */
    private $hasAttended;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return AppointmentRecepientAndStatus
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;
        
        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set appointments
     *
     * @param \Synapse\CoreBundle\Entity\Appointments $appointments            
     * @return AppointmentRecepientAndStatus
     */
    public function setAppointments(\Synapse\CoreBundle\Entity\Appointments $appointments = null)
    {
        $this->appointments = $appointments;
        
        return $this;
    }

    /**
     * Get appointments
     *
     * @return \Synapse\CoreBundle\Entity\Appointments
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * Set personIdFaculty
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty            
     * @return AppointmentRecepientAndStatus
     */
    public function setPersonIdFaculty(\Synapse\CoreBundle\Entity\Person $personIdFaculty = null)
    {
        $this->personIdFaculty = $personIdFaculty;
        
        return $this;
    }

    /**
     * Get personIdFaculty
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdFaculty()
    {
        return $this->personIdFaculty;
    }

    /**
     * Set personIdStudent
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdStudent            
     * @return AppointmentRecepientAndStatus
     */
    public function setPersonIdStudent(\Synapse\CoreBundle\Entity\Person $personIdStudent = null)
    {
        $this->personIdStudent = $personIdStudent;
        
        return $this;
    }

    /**
     * Get personIdStudent
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonIdStudent()
    {
        return $this->personIdStudent;
    }

    /**
     * Set hasAttended
     *
     * @param boolean $hasAttended            
     * @return AppointmentRecepientAndStatus
     */
    public function setHasAttended($hasAttended)
    {
        $this->hasAttended = $hasAttended;
        
        return $this;
    }

    /**
     * Get hasAttended
     *
     * @return boolean 
     */
    public function getHasAttended()
    {
        return $this->hasAttended;
    }
 }
