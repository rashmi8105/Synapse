<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * EbiDownloadType
 *
 * @ORM\Table(name="ebi_download_type")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\EbiDownloadTypeRepository")
 */
class EbiDownloadType extends BaseEntity
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
     * @ORM\Column(name="download_type", type="integer", nullable=true)
     */
    private $downloadType;

    /**
     *
     * @var string
     * @ORM\Column(name="download_display_name", type="integer", nullable=true)
     */
    private $downloadDisplayName;


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
    public function getDownloadType()
    {
        return $this->downloadType;
    }

    /**
     * @param string $downloadType
     */
    public function setDownloadType($downloadType)
    {
        $this->downloadType = $downloadType;
    }

    /**
     * @return UploadColumnHeader
     */
    public function getUploadColumnHeader()
    {
        return $this->downloadDisplayName;
    }

    /**
     * @param string $downloadDisplayName
     */
    public function setDownloadDisplayName($downloadDisplayName)
    {
        $this->downloadDisplayName = $downloadDisplayName;
    }

}
