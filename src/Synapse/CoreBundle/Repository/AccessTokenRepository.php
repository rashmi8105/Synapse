<?php
namespace Synapse\CoreBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Entity\AccessToken;

class AccessTokenRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:AccessToken';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return AccessToken|null
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception $exception
     * @return AccessToken[]|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $object = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($object, $exception);
    }

    /**
     *  Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return AccessToken|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    public function createAccessToken($userId, $token)
    {
        try {
            $em = $this->getEntityManager();
            $sql = "INSERT INTO `AccessToken` (`client_id`, `user_id`, `token`, `expires_at`, `scope`) VALUES ('1', '" . $userId . "', '" . $token . "', '0', 'user');";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {          
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
    }

    public function getAccessToken($userId)
    {
        try {
            $em = $this->getEntityManager();
            $sql = "SELECT token from AccessToken WHERE user_id = $userId AND expires_at = 0";
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {          
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }

    /**
     * Get expire time of the token
     * @param int $userId
     * @param string $accessToken
     * @throws SynapseDatabaseException
     */
    public function getAccessTokenExpireTime($userId, $accessToken)
    {
        $sql = "SELECT expires_at FROM AccessToken WHERE user_id = :userId AND token = :accessToken";
        $parameters = ['userId' => $userId,
            'accessToken' => $accessToken
        ];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results[0];
    }

    /**
     * Invalidates all access tokens for a service account.
     *
     * @param integer $userId
     * @return void
     * @throws SynapseDatabaseException
     */
    public function invalidateAccessTokensForUser($userId)
    {
        $sql = "UPDATE 
                    AccessToken 
                SET 
                    expires_at = -1
                WHERE
                    user_id = :userId";

        $parameters = [
            'userId' => $userId
        ];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}