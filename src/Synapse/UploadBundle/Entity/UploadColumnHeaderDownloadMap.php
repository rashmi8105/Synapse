<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * UploadColumnHeaderDownloadMap
 *
 * @ORM\Table(name="upload_column_header_download_map")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\UploadColumnHeaderDownloadMapRepository")
 */
class UploadColumnHeaderDownloadMap extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="Synapse\UploadBundle\Entity\UploadColumnHeader")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="upload_column_header_id", referencedColumnName="id")
     * })
     */
    private $uploadColumnHeader;

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
     * @return UploadColumnHeader
     */
    public function getUploadColumnHeader()
    {
        return $this->uploadColumnHeader;
    }

    /**
     * @param UploadColumnHeader $uploadColumnHeader
     */
    public function setUploadColumnHeader($uploadColumnHeader)
    {
        $this->uploadColumnHeader = $uploadColumnHeader;
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
     * @return EbiDownloadType
     */
    public function getEbiDownloadType(){
        return $this->ebiDownloadType;
    }

    /**
     * @param EbiDownloadType $ebiDownloadType
     */
    public function setEbiDownloadType($ebiDownloadType)
    {
        $this->ebiDownloadType = $ebiDownloadType;
    }



}
