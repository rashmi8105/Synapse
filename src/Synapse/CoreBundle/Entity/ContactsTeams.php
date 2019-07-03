<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ContactsTeams
 * @ORM\Table(name="contacts_teams", indexes={@ORM\Index(name="fk_contacts_teams_contacts1_idx",columns={"contacts_id"}),@ORM\Index(name="fk_contacts_teams_Teams1_idx",columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ContactsTeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ContactsTeams extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Contacts @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Contacts")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="contacts_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $contactsId;

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
     * @param \Synapse\CoreBundle\Entity\Contacts $contactsId            
     */
    public function setContactsId($contactsId)
    {
        $this->contactsId = $contactsId;
    }

    /**
     *
     * @return \Synapse\CoreBundle\Entity\Contacts
     */
    public function getContactsId()
    {
        return $this->contactsId;
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