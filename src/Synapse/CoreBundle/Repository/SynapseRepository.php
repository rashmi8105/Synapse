<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityRepository;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class SynapseRepository extends EntityRepository
{

    /**
     * Persist a new entity
     *
     * @param
     *            $entity
     * @param bool $flush
     * @return mixed $entity
     */
    public function persist($entity, $flush = true)
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($entity);
            if ($flush) {
                $em->flush();
            }
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $entity;
    }

    /**
     * Updates an existing entity
     *
     * @param
     *            $entity
     * @param bool $flush
     * @return mixed $entity
     */
    public function update($entity, $flush = true)
    {
        try {
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $entity;
    }

    /**
     * Deletes an entity
     *
     * @param
     *            $entity
     * @return bool true if entity was deleted correctly
     */
    public function delete($entity, $flush = true)
    {
        try {
            $em = $this->getEntityManager();
            $em->remove($entity);
            if ($flush) {
                $em->flush();
            }
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return true;
    }

    /**
     * Flushes the entity manager
     */
    public function flush()
    {
        try {
            $em = $this->getEntityManager();
            $em->flush();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException();
        }
    }

    /**
     * Flushes the entity manager for a single entity
     *
     * @param $entity
     */
    public function flushEntity($entity){
        try {
            $em = $this->getEntityManager();
            $em->flush($entity);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage());
        }
    }
    /**
     * Clear the entity manager
     */
    public function clear()
    {
        try {
            $em = $this->getEntityManager();
            $em->clear();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    public function startTransaction()
    {
        try {
            $em = $this->getEntityManager();
            $em->getConnection()->beginTransaction();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    public function completeTransaction()
    {
        try {
            $em = $this->getEntityManager();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    public function rollbackTransaction()
    {
        try {
            $em = $this->getEntityManager();
            $em->getConnection()->rollback();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    /**
     * Works the same as findOneBy  but includes the soft deleted record
     * @param $criteria
     * @return null|object
     */
    public function findOneByIncludingDeletedRecords($criteria){
        $this->getEntityManager()->getFilters()->disable('softdeleteable');
        $result = $this->findOneBy($criteria);
        $this->getEntityManager()->getFilters()->enable('softdeleteable');
        return $result;
    }

    /**
     * Generic method for executing raw parameterized SQL queries and use fetchall for performance.
     * Usage: In the Inherited class
     *      $this->executeQueryFetchAll($sql);
     *      $this->executeQueryFetchAll($sql, $parameters);
     *      $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
     *      $this->executeQueryFetchAll($sql, $parameters, $parameterTypes, true);
     *
     * @param string $sql
     * @param array $parameters
     * @param array $parameterTypes
     * @param bool $isExceptionSuppressed //This flag denotes if there is any error then that error
     *          needs to displayed or not
     * @return null|array
     */
    public function executeQueryFetchAll($sql, $parameters = [], $parameterTypes = [], $isExceptionSuppressed = false)
    {
        $results = null;

        $statement = $this->executeQuery($sql, $parameters, $parameterTypes, $isExceptionSuppressed);
        $results = $statement->fetchAll();
        return $results;
    }

    /**
     * Generic method for executing raw parameterized SQL queries and use fetch for performance.
     * Usage: In the inherited class
     *      $this->executeQueryFetch($sql);
     *      $this->executeQueryFetch($sql, $parameters);
     *      $this->executeQueryFetch($sql, $parameters, $parameterTypes);
     *      $this->executeQueryFetch($sql, $parameters, $parameterTypes, true);
     *
     * @param string $sql
     * @param array $parameters
     * @param array $parameterTypes
     * @param bool $isExceptionSuppressed //This flag denotes if there is any error then that error
     *          needs to displayed or not
     * @return null|array
     */
    public function executeQueryFetch($sql, $parameters = [], $parameterTypes = [], $isExceptionSuppressed = false)
    {
        $results = null;
        $statement = $this->executeQuery($sql, $parameters, $parameterTypes, $isExceptionSuppressed);
        $results = $statement->fetch();
        return $results;
    }

    /**
     * Generic method for executing raw parameterized SQL queries.
     * Usage: In the inherited class
     *      $this->executeQueryStatement($sql);
     *      $this->executeQueryStatement($sql, $parameters);
     *      $this->executeQueryStatement($sql, $parameters, $parameterTypes);
     *      $this->executeQueryStatement($sql, $parameters, $parameterTypes, true);
     *
     * @param string $sql
     * @param array $parameters
     * @param array $parameterTypes
     * @param bool $isExceptionSuppressed //This flag denotes if there is any error then that error
     *          needs to displayed or not
     * @return null|array
     */
    public function executeQueryStatement($sql, $parameters = [], $parameterTypes = [], $isExceptionSuppressed = false)
    {
        $this->executeQuery($sql, $parameters, $parameterTypes, $isExceptionSuppressed);
    }

    /**
     * Generic method for executing raw parameterized SQL queries..
     * Usage: In the inhertied class
     *      $this->executeQuery($sql);
     *      $this->executeQuery($sql, $parameters);
     *      $this->executeQuery($sql, $parameters, $parameterTypes);
     *      $this->executeQuery($sql, $parameters, $parameterTypes, true);
     *
     * @param string $sql
     * @param array $parameters
     * @param array $parameterTypes
     * @param bool $isExceptionSuppressed //This flag denotes if there is any error then that error
     *          needs to displayed or not
     * @return null|Statement
     */
    private function executeQuery($sql, $parameters = [], $parameterTypes = [], $isExceptionSuppressed = false)
    {
        $statement = null;
        try {
            if (trim($sql) == '') {
                throw new SynapseDatabaseException("SQL string not passed, it cannot be empty.\n"
                    . $this->getEntityName() . " . " . $this->getClassName());
            }
            if ($parameterTypes && !$parameters) {
                throw new SynapseDatabaseException("Parameter types passed, but parameters not passed.\n"
                    . $this->getClassName() . " . " . $this->getEntityName() . ".\n"
                    . $parameterTypes);
            }
            $entityManager = $this->getEntityManager();

            $statement = $entityManager->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {

            print_r($parameters);exit;
            if (!$isExceptionSuppressed) {
                throw new SynapseDatabaseException($this->getClassName() . " . " . $this->getEntityName() . ".\n"
                    . $parameterTypes . "\n"
                    . $e->getMessage() . ": " . $e->getTraceAsString());
            }
        }
        return $statement;
    }

    /**
     * Throws the specified exception if the object does not exist.
     *
     * @param BaseEntity $object
     * @param \Exception $exception
     * @return BaseEntity | null
     * @throws \Exception
     */
    public function doesObjectExist($object, $exception)
    {
        if (!$object && ($exception instanceof \Exception)) {
            throw $exception;
        }

        return $object;
    }
    /**
     * Throws the specified exception if the array is empty.
     *
     * @param array $arrayOfObjects
     * @param \Exception $exception
     * @return array | null
     * @throws \Exception
     */
    public function doObjectsExist($arrayOfObjects, $exception)
    {
        if ((!isset($arrayOfObjects) || empty($arrayOfObjects)) && ($exception instanceof \Exception)) {
            throw $exception;
        }

        return $arrayOfObjects;
    }

    /**
     * "_" and "%"  are wild cards for Mysql LIKE ,  It needs to be escaped when we use  LIKE in mysql
     *
     * @param string $string
     * @return string
     */
    public function escapeMysqlWildCards($string){

        $string =  str_replace("_","\_",$string);
        $string =  str_replace("%","\%",$string);
        return $string;
    }
}
