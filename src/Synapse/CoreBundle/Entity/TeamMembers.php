<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * TeamMembers
 *
 * @ORM\Table(name="team_members", indexes={@ORM\Index(name="fk_team_members_organization1", columns={"organization_id"}), @ORM\Index(name="fk_team_members_person1", columns={"person_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\TeamMembersRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class TeamMembers extends BaseEntity
{

    /**
     *
     * @var string @ORM\Column(name="is_team_leader", type="string", length=1, nullable=true)
     */
    private $isTeamLeader;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

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
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Teams")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="teams_id", referencedColumnName="id")
     *      })
     */
    private $teamId;

    /**
     * Set isTeamLeader
     *
     * @param string $isTeamLeader            
     * @return TeamMembers
     */
    public function setIsTeamLeader($isTeamLeader)
    {
        $this->isTeamLeader = $isTeamLeader;
        
        return $this;
    }

    /**
     * Get isTeamLeader
     *
     * @return string
     */
    public function getIsTeamLeader()
    {
        return $this->isTeamLeader;
    }

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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return TeamMembers
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return TeamMembers
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
     * Set Team
     *
     * @param \Synapse\CoreBundle\Entity\Teams $teamId            
     * @return Teams
     */
    public function setTeamId(\Synapse\CoreBundle\Entity\Teams $teamId = null)
    {
        $this->teamId = $teamId;
        
        return $this;
    }

    /**
     * Get Team
     *
     * @return \Synapse\CoreBundle\Entity\Teams
     */
    public function getTeamId()
    {
        return $this->teamId;
    }
}
