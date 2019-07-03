<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * UploadColumnHeader
 *
 * @ORM\Table(name="upload_column_header")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\UploadColumnHeaderRepository")
 */
class UploadColumnHeader extends BaseEntity
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
     * @ORM\Column(name="upload_column_name", type="string", nullable=false)
     *
     */
    private $uploadColumnName;

    /**
     *
     * @var string
     * @ORM\Column(name="upload_column_display_name", type="string", nullable=false)
     *
     */
    private $uploadColumnDisplayName;

    /**
     * @return integer
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
    public function getUploadColumnName()
    {
        return $this->uploadColumnName;
    }

    /**
     * @param string $uploadColumnName
     */
    public function setUploadColumnName($uploadColumnName)
    {
        $this->uploadColumnName = $uploadColumnName;
    }


    /**
     * @return string
     */
    public function getUploadColumnDisplayName()
    {
        return $this->uploadColumnDisplayName;
    }

    /**
     * @param string $uploadColumnDisplayName
     */
    public function setUploadColumnDisplayName($uploadColumnDisplayName)
    {
        $this->uploadColumnName = $uploadColumnDisplayName;
    }



}
