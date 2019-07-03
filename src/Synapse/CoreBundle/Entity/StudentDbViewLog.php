<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;

/**
 * StudentDbViewLog
 *
 * @ORM\Table(name="student_db_view_log", indexes={@ORM\Index(name="fk_db_view_log_person1",columns={"person_id_faculty"}),@ORM\Index(name="fk_db_view_log_person2",columns={"person_id_student"}),@ORM\Index(name="fk_db_view_log_organization1",columns={"organization_id"}) })
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\StudentDbViewLogRepository")
 */
class StudentDbViewLog extends BaseEntity implements OwnableAssetEntityInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

     /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="person_id_faculty", referencedColumnName="id")
     * })
     */
    private $personIdFaculty;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="person_id_student", referencedColumnName="id")
     * })
     */
    private $personIdStudent;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="last_viewed_on", type="datetime", nullable=true)
     *
     * @JMS\Expose
     */
    private $lastViewedOn;

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
     * Set id
     *
     * @return RelatedActivities
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return RelatedActivities
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
     * Set personIdFaculty
     *
     * @param \Synapse\CoreBundle\Entity\Person $personIdFaculty
     * @return StudentDbViewLog
     */
    public function setPersonIdFaculty(\Synapse\CoreBundle\Entity\Person $personIdFaculty = null)
    {
        return $this->personIdFaculty = $personIdFaculty;

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
     * @return StudentDbViewLog
     */
    public function setPersonIdStudent(\Synapse\CoreBundle\Entity\Person $personIdStudent = null)
    {
        return $this->personIdStudent = $personIdStudent;

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
     * @param \Datetime $lastViewedOn
     */
    public function setLastViewedOn($lastViewedOn)
    {
        $this->lastViewedOn = $lastViewedOn;
    }

    /**
     * @return \Datetime
     */
    public function getLastViewedOn()
    {
        return $this->lastViewedOn;
    }
}
