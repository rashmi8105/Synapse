<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\EbiMetadata;

/**
 * UploadEbiMetadataColumnHeaderDownloadMap
 *
 * @ORM\Table(name="upload_ebi_metadata_column_header_download_map")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\UploadEbiMetadataColumnHeaderDownloadMapRepository")
 */
class UploadEbiMetadataColumnHeaderDownloadMap extends BaseEntity
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
     * @var Upload
     * @ORM\ManyToOne(targetEntity="Synapse\UploadBundle\Entity\Upload")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="upload_id", referencedColumnName="id")
     * })
     *
     */
    private $upload;

    /**
     *
     * @var UploadColumnHeader
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id")
     * })
     */
    private $ebiMetadata;

    /**
     *
     * @var EbiDownloadType
     * @ORM\ManyToOne(targetEntity="Synapse\UploadBundle\Entity\EbiDownloadType")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_download_type_id", referencedColumnName="id")
     * })
     */
    private $ebiDownloadType;

    /**
     * @var integer
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder;

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
     * @return Upload
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @param Upload $upload
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;
    }

    /**
     * @return ebiDownloadType
     */
    public function getEbiDownloadType()
    {
        return $this->ebiDownloadType;
    }

    /**
     * @param ebiDownloadType $ebiDownloadType
     */
    public function setUploadColumnHeader($ebiDownloadType)
    {
        $this->ebiDownloadType = $ebiDownloadType;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return ebiMetadata
     */
    public function getEbiMetadata(){
        return $this->ebiMetadata;
    }

    /**
     * @param EbiDownloadType $ebiDownloadType
     */
    public function setEbiMetadata($ebiMetadata)
    {
        $this->ebiMetadata = $ebiMetadata;
    }



}
