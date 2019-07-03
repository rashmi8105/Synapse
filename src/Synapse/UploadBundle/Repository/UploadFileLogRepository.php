<?php
namespace Synapse\UploadBundle\Repository;

use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\UploadBundle\Entity\UploadFileLog;
use Synapse\RestBundle\Entity\Error;
use Synapse\CoreBundle\Util\Constants\UploadLogConstant;
use Synapse\UploadBundle\Util\Constants\UploadConstant;
use Synapse\RestBundle\Exception\ValidationException;

class UploadFileLogRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseUploadBundle:UploadFileLog';

    public function findPendingView($organization, $type)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('p.id');
        $qb->from(UploadLogConstant::SYNAPSE_UPLOAD_LOG_TABLE, 'p');
        $qb->where('p.organizationId = :organization', 'p.uploadType = :type', 'p.viewed IS NULL');
        $qb->orWhere('p.organizationId = :organization AND p.status = :status AND p.uploadType = :type');
        $qb->orderBy('p.id', 'DESC');
        $qb->setMaxResults(1);
        
        $qb->setParameters(array(
            'organization' => $organization,
            'type' => $type,
            UploadConstant::STATUS => 'Q'
        ));
        $query = $qb->getQuery();
        
        $result = $query->getOneOrNullResult();
        return $result;
    }

    public function findGroupPendingView($group, $organization)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('p.id', 'p.groupId');
        $qb->from(UploadLogConstant::SYNAPSE_UPLOAD_LOG_TABLE, 'p');
        $qb->where('p.organizationId = :organization AND p.groupId = :group AND p.uploadType = :type AND p.viewed IS NULL');
        $qb->orWhere('p.organizationId = :organization AND p.groupId = :group AND p.status = :status AND p.uploadType = :type');
        $qb->orderBy('p.id', 'DESC');
        $qb->setMaxResults(1);
        
        $qb->setParameters(array(
            'organization' => $organization,
            'group' => $group,
            'type' => 'S2G',
            UploadConstant::STATUS => 'Q'
        ));
        $query = $qb->getQuery();
        
        $result = $query->getOneOrNullResult();
        return $result;
    }

    public function getLastRowByType($type)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id');
        $qb->from(UploadLogConstant::SYNAPSE_UPLOAD_LOG_TABLE, 'u');
        $qb->where('u.uploadType = :type AND u.validRowCount > 0');
        $qb->orderBy('u.id', 'DESC');
        $qb->setMaxResults(1);
        $qb->setParameters(array(
            'type' => $type
        ));
        
        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        return $result;
    }

    public function findPendingViewEbi($type)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder();
        $qb->select('p.id');
        $qb->from(UploadLogConstant::SYNAPSE_UPLOAD_LOG_TABLE, 'p');
        $qb->where('p.uploadType = :type', 'p.viewed IS NULL');
        $qb->orWhere('p.status = :status AND p.uploadType = :type');
        $qb->orderBy('p.id', 'DESC');
        $qb->setMaxResults(1);
        
        $qb->setParameters(array(
            
            'type' => $type,
            UploadConstant::STATUS => 'Q'
        ));
        $query = $qb->getQuery();
        
        $result = $query->getOneOrNullResult();
        return $result;
    }

    /**
     * Listing of upload history
     *
     * @param int $organizationId
     * @param int $startPoint
     * @param int $recordsPerPage
     * @param string $sortBy
     * @param array $uploadType
     * @param bool $isJob
     * @return array
     * @throws SynapseDatabaseException
     */
    public function listHistory($organizationId, $startPoint, $recordsPerPage, $sortBy, $uploadType, $isJob = false)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'uploadType' => $uploadType
        ];
        $parameterTypes['uploadType'] = Connection::PARAM_STR_ARRAY;
        if (!$isJob) {
            $parameters['recordsPerPage'] = $recordsPerPage;
            $parameters['offset'] = $startPoint;

            $parameterTypes['recordsPerPage'] = \PDO::PARAM_INT;
            $parameterTypes['offset'] = \PDO::PARAM_INT;

            $limitCondition = ' LIMIT :recordsPerPage OFFSET :offset';
        } else {
            $limitCondition = '';
        }

        $deterministicSort = 'uploaded_date DESC, upload_file_log_id DESC ';

        switch ($sortBy) {
            case 'uploaded_by':
            case '+uploaded_by':
                $orderByClause = ' ORDER BY p.lastname, p.firstname, '.$deterministicSort;
                break;
            case '-uploaded_by':
                $orderByClause = ' ORDER BY p.lastname DESC, p.firstname, '.$deterministicSort;
                break;
            case 'file_name':
            case '+file_name':
                $orderByClause = ' ORDER BY upload_file_name, '.$deterministicSort;
                break;
            case '-file_name':
                $orderByClause = ' ORDER BY upload_file_name DESC, '.$deterministicSort;
                break;
            case 'type':
            case '+type':
                $orderByClause = ' ORDER BY upload_type, '.$deterministicSort;
                break;
            case '-type':
                $orderByClause = ' ORDER BY upload_type DESC, '.$deterministicSort;
                break;
            case 'uploaded_date':
            case '+uploaded_date':
                $orderByClause = ' ORDER BY uploaded_date, upload_file_log_id DESC ';
                break;
            case '-uploaded_date':
                $orderByClause = ' ORDER BY '.$deterministicSort;
                break;
            default:
                $orderByClause = ' ORDER BY uploaded_date DESC, upload_file_log_id DESC';
        }

        $sql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS ufl.id AS upload_file_log_id,
                    ufl.uploaded_file_path AS file_name,
                    ufl.upload_type AS type,
                    ufl.upload_date AS uploaded_date,
                    p.firstname AS firstname,
                    p.lastname AS lastname,
                    p.external_id AS UploadedByExternalId,
                    p.username AS UploadedByEmail,
                    ufl.error_file_path AS error,
                    (CASE 
						WHEN ufl.error_count > 0 THEN ufl.error_file_path
					    ELSE ''
					END) as error_file_name,
                    ufl.error_count AS error_count,
                    (CASE
                        WHEN ufl.upload_type = 'F' THEN 'Faculty Upload'
                        WHEN ufl.upload_type = 'F' THEN 'Faculty Upload'
                        WHEN ufl.upload_type = 'S' THEN 'Student Upload'
                        WHEN ufl.upload_type = 'C' THEN 'Courses'
                        WHEN ufl.upload_type = 'T' THEN 'Student Course'
                        WHEN ufl.upload_type = 'P' THEN 'Faculty Course'
                        WHEN ufl.upload_type = 'G' THEN 'Subgroups'
                        WHEN ufl.upload_type = 'GF' THEN 'Group Faculty'
                        WHEN ufl.upload_type = 'GS' THEN 'Group Student'
                        WHEN ufl.upload_type = 'A' THEN 'Academic Updates'
                        WHEN ufl.upload_type = 'S2G' THEN 'Group Student'
                        WHEN ufl.upload_type = 'GS' THEN 'Group Student'
                        ELSE ' '
                    END) AS upload_type,
                    TRIM(CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(ufl.uploaded_file_path,CONCAT(ufl.organization_id, '-'),'-1'),'-UID',1),'.csv')) AS upload_file_name
                FROM
                    upload_file_log ufl
                        LEFT JOIN
                    person p ON (ufl.person_id = p.id)
                        AND (p.deleted_at IS NULL)
                WHERE
                    ufl.organization_id = :organizationId
                        AND ufl.upload_type IN (:uploadType)
                $orderByClause 
                $limitCondition";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            return $results;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }
    
    public function getUniqueOrganization(){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('DISTINCT ufl.organizationId');
        $qb->from(UploadLogConstant::SYNAPSE_UPLOAD_LOG_TABLE, 'ufl');
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }
    
    public function getQueryResultSet($sql){
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
                ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }
}