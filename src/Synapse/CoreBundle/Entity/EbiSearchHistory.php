<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * EbiSearchHistory
 *
 * @ORM\Table(name="ebi_search_history")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiSearchHistoryRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiSearchHistory extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $person;

    /**
     * @var EbiSearch
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiSearch")
     * @ORM\JoinColumn(name="ebi_search_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $ebiSearch;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_run", type="datetime", nullable=true)
     */
    private $lastRun;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return EbiSearch
     */
    public function getEbiSearch()
    {
        return $this->ebiSearch;
    }

    /**
     * @param EbiSearch $ebiSearch
     */
    public function setEbiSearch($ebiSearch)
    {
        $this->ebiSearch = $ebiSearch;
    }

    /**
     * @return \DateTime
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }

    /**
     * @param \DateTime $lastRun
     */
    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;
    }

}