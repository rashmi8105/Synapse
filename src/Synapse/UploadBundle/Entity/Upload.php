<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Upload
 *
 * @ORM\Table(name="upload")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\UploadRepository")
 */
class Upload extends BaseEntity
{

    /**
     *
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="upload_name", type="string", nullable=false)
     *     
     */
    private $uploadName;

    /**
     * @var string
     * @ORM\Column(name="upload_display_name", type="string", nullable=false)
     */
    private $uploadDisplayName;

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
     * @return string
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * @param string $uploadName
     */
    public function setUploadName($uploadName)
    {
        $this->uploadName = $uploadName;
    }

    /**
     * @return string
     */
    public function getUploadDisplayName(){
        return $this->uploadDisplayName;
    }

    /**
     * @param $uploadDisplayName
     */
    public function setUploadDisplayName($uploadDisplayName){
        $this->uploadDisplayName = $uploadDisplayName;
    }
}
