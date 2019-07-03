<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * DatablockUi
 *
 * @ORM\Table(name="datablock_ui")
 * @ORM\Entity
 */
class DatablockUi extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="ui_feature_name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $uiFeatureName;



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
     * Set key
     *
     * @param string $key
     * @return DatablockUi
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set uiFeatureName
     *
     * @param string $uiFeatureName
     * @return DatablockUi
     */
    public function setUiFeatureName($uiFeatureName)
    {
        $this->uiFeatureName = $uiFeatureName;

        return $this;
    }

    /**
     * Get uiFeatureName
     *
     * @return string 
     */
    public function getUiFeatureName()
    {
        return $this->uiFeatureName;
    }
}
