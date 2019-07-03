<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * EmailTeams
 * @ORM\Table(name="email_teams", indexes={@ORM\Index(name="fk_email_teams_email1",columns={"email_id"}),@ORM\Index(name="fk_email_teams_teams1",columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EmailTeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EmailTeams extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Email @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Email")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="email_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $emailId;

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

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Email $emailId
     */
    public function setEmailId($emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Email
     */
    public function getEmailId()
    {
        return $this->emailId;
    }
    
}