<?php
namespace Synapse\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Teams
 *
 * @ORM\Table(name="Teams", indexes={@ORM\Index(name="fk_Teams_organization1", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\TeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"teamName", "organization"},message="Team Name already exists.")
 */
class Teams extends BaseEntity
{

    /**
     *
     * @var string @ORM\Column(name="team_name", type="string", length=100, nullable=true)
     */
    private $teamName;

    /**
     *
     * @var string @ORM\Column(name="team_description", type="string", length=500, nullable=true)
     */
    private $teamDescription;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
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
     * Set teamDescription
     *
     * @param string $teamDescription            
     * @return Teams
     */
    public function setTeamDescription($teamDescription)
    {
        $this->teamDescription = $teamDescription;
        
        return $this;
    }

    /**
     * Get teamDescription
     *
     * @return string
     */
    public function getTeamDescription()
    {
        return $this->teamDescription;
    }

    /**
     * Set Id
     *
     * @param string $id            
     * @return Teams
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
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
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return Teams
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
     * Set teamName
     *
     * @param string $teamName            
     * @return Teams
     */
    public function setTeamName($teamName)
    {
        $this->teamName = $teamName;
        
        return $this;
    }

    /**
     * Get teamName
     *
     * @return string 
     */
    public function getTeamName()
    {
        return $this->teamName;
    }
}
