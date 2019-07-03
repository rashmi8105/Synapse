<?php
namespace Synapse\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * EbiSearch
 *
 * @ORM\Table(name="ebi_search")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\SearchRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EbiSearch extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="category", type="string", nullable=true)
     * @JMS\Expose
     */
    private $category;

    /**
     * @var string
     * @ORM\Column(name="query_key", type="string", nullable=true)
     * @JMS\Expose
     */
    private $queryKey;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=true)
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $sequence;

    /**
     * @var string
     * @ORM\Column(name="search_type", type="string", length=1, nullable=true)
     */
    private $searchType;

    /**
     * @var boolean
     * @ORM\Column(name="is_enabled", type="boolean", length=1, nullable=true)
     * @JMS\Expose
     */
    private $isEnabled;

    /**
     * @var string
     * @ORM\Column(name="query", type="text", nullable=true)
     * @JMS\Expose
     */
    private $query;


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
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $queryKey
     */
    public function setQueryKey($queryKey)
    {
        $this->queryKey = $queryKey;
    }

    /**
     * @return string
     */
    public function getQueryKey()
    {
        return $this->queryKey;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param string $searchType
     */
    public function setSearchType($searchType)
    {
        $this->searchType = $searchType;
    }

    /**
     * @return string
     */
    public function getSearchType()
    {
        return $this->searchType;
    }

    /**
     * @param boolean $isEnabled
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }


}