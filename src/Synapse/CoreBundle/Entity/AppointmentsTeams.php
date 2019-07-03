<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * AppointmentsTeams
 * @ORM\Table(name="appointments_teams", indexes={@ORM\Index(name="fk_appointments_teams_appointments1_idx",columns={"appointments_id"}),@ORM\Index(name="fk_appointments_teams_teams1_idx",columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AppointmentsTeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class AppointmentsTeams extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Contacts @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Appointments")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="appointments_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $appointmentsId;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Teams @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Teams")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="teams_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $teamsId;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Appointments $appointmentsId            
     */
    public function setAppointmentsId($appointmentsId)
    {
        $this->appointmentsId = $appointmentsId;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Appointments
     */
    public function getAppointmentsId()
    {
        return $this->appointmentsId;
    }

    /**
     *
     * @param \Synapse\CoreBundle\Entity\Teams $teamsId            
     */
    public function setTeamsId($teamsId)
    {
        $this->teamsId = $teamsId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Teams
     */
    public function getTeamsId()
    {
        return $this->teamsId;
    }

   
    
    
}