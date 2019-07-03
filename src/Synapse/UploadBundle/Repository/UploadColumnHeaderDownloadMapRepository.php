<?php

namespace Synapse\UploadBundle\Repository;

use Flow\Exception;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\UploadBundle\Entity\UploadColumnHeaderDownloadMap;

class UploadColumnHeaderDownloadMapRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseUploadBundle:UploadColumnHeaderDownloadMap';


    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return null | UploadColumnHeaderDownloadMap
     * @throws  \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return null | UploadColumnHeaderDownloadMap
     * @throws \Exception
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception|null $exception
     *
     * @return UploadColumnHeaderDownloadMap[]
     * @throws \Exception
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }

    /**
     * gets the column headers for an upload template
     *
     * @param string $uploadType
     * @param string $downloadType
     * @param string $uploadColumnName
     * @return array|null
     */
    public function getUploadHeaders($uploadType, $downloadType, $uploadColumnName = 'upload_column_name')
    {

        $parameters = [
            'uploadType' => $uploadType,
            'downloadType' => $downloadType

        ];

        $sql = "
    SELECT
        upload_column_name,
        upload_column_display_name
    FROM
        (SELECT
            uch.upload_column_name,
            uch.upload_column_display_name,
            sort_order,
            0 AS tie_breaker
        FROM
            upload u
        INNER JOIN
            upload_column_header_download_map uchdm ON u.id = uchdm.upload_id
        INNER JOIN
            upload_column_header uch ON uch.id = uchdm.upload_column_header_id
        INNER JOIN
            ebi_download_type edt ON edt.id = uchdm.ebi_download_type_id
        WHERE
            u.upload_name = :uploadType
                AND edt.download_type = :downloadType
                AND u.deleted_at IS NULL
                AND uch.deleted_at IS NULL
                AND edt.deleted_at IS NULL
                AND uchdm.deleted_at IS NULL
            UNION
        SELECT
            em.meta_key,
            em.meta_key,
            sort_order,
            1 AS tie_breaker
        FROM
            upload u
        INNER JOIN
            upload_ebi_metadata_column_header_download_map uemchd ON uemchd.upload_id = u.id
        INNER JOIN
            ebi_metadata em ON em.id = uemchd.ebi_metadata_id
        INNER JOIN
            ebi_download_type edt ON edt.id = uemchd.ebi_download_type_id
        WHERE
            u.upload_name = :uploadType
                AND edt.download_type = :downloadType
                AND u.deleted_at IS NULL
                AND em.deleted_at IS NULL
                AND edt.deleted_at IS NULL
                AND uemchd.deleted_at IS NULL) AS upload_headers
    ORDER BY sort_order ASC, tie_breaker ASC;";


        $result = $this->executeQueryFetchAll($sql, $parameters);
        $result = array_column($result, $uploadColumnName);
        if (empty($result)) {
            throw new SynapseValidationException("No headers found for the $downloadType");
        }
        return $result;


    }

}