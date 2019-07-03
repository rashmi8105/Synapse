<?php

namespace Synapse\PersonBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ContactInfoRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:ContactInfo';

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param \Exception $exception
     * @return ContactInfo|null
     */
    public function findOneBy(array $criteria, array $orderBy = null , $exception =  null)
    {
        $contactInfoEntity =  parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($contactInfoEntity, $exception);
    }


    /**
     * @param mixed $id
     * @param \Exception $exception
     * @param null $lockMode
     * @param null $lockVersion
     * @return ContactInfo|object
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $contactInfoEntity = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($contactInfoEntity, $exception);
    }

    public function createContact(ContactInfo $contactInfo)
    {
        $em = $this->getEntityManager();
        $em->persist($contactInfo);
        return $contactInfo;
    }
    public function remove(ContactInfo $contact)
    {
        $em = $this->getEntityManager();
        $em->remove($contact);
    }

    /**
     * Returns the first valid contact field from every ContractInfo record associated with a user.
     *
     * @param ContactInfo[] $contacts
     * @return ContactInfo
     */
    public function getCoalescedContactInfo($contacts)
    {
        static $fieldsToCheck = null;

        // This list technically only has to be updated when the class ContactInfo has a database field
        // modification. I struggled with whether to do the Reflection inspection on every class load or
        // to "just" tightly couple this class with ContactInfo in a brittle fashion. The reflection adds
        // less than 2 ms, so I decided to leave it.
        if (!$fieldsToCheck) {
            $mirror = new \ReflectionClass(ContactInfo::class);
            $properties = $mirror->getProperties(\ReflectionProperty::IS_PRIVATE);
            foreach ($properties as $prop) {
                $fieldsToCheck[] = $prop->getName();
            }
        }

        $contactRecord = new ContactInfo();
        foreach ($contacts as $contact) {
            foreach ($fieldsToCheck as &$field) {
                $getMethod = 'get' . ucfirst($field);
                $setMethod = 'set' . ucfirst($field);

                if (!method_exists($contact, $getMethod) || !method_exists($contact, $setMethod)) {
                    continue;
                }

                $value = $contact->$getMethod();

                if ($value) {
                    $contactRecord->$setMethod($value);
                    // Unset the field because we will no longer need to search for it.
                    unset($field);
                }
            }
        }

        return $contactRecord;
    }

    /**
     * Get person contact phone and mobile based on person Ids
     *
     * @param array $personIds
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPersonMobileAndHomePhoneNumbers($personIds)
    {
        $parameters = ['personIds' => $personIds];
        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    p.id,
                    ci.home_phone,
                    ci.primary_mobile
                FROM
                    person p
                        LEFT JOIN
                    person_contact_info pci ON pci.person_id = p.id AND pci.deleted_at IS NULL
                        LEFT JOIN
                    contact_info ci ON ci.id = pci.contact_id AND ci.deleted_at IS NULL
                WHERE
                    p.id IN (:personIds)
                        AND p.deleted_at IS NULL 
                ORDER BY p.lastname, p.firstname ASC";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Gets the list of person id's based on the contact filters
     *
     * @param integer $organizationId
     * @param string $personFilter
     * @param string $contactFilter
     * @param string $contactFilterType
     * @return array
     */
    public function getPersonIdsBasedOnContactInfoFilters($organizationId, $personFilter, $contactFilter, $contactFilterType)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $parameterTypes = [];

        $personFilterCondition = "";
        $contactFilterCondition = "";

        if (!is_null($contactFilter)) {
            switch ($contactFilterType) {
                case 'address':
                    $contactFilterCondition = "AND (
                                                    cis.address_1 LIKE :contactFilter
                                                    OR cis.address_2 LIKE :contactFilter
                                                    OR cis.city LIKE :contactFilter
                                                    OR cis.state LIKE :contactFilter
                                                    OR cis.zip LIKE :contactFilter
                                                    OR cis.country LIKE :contactFilter
                                                    OR cis.full_address_1 LIKE :contactFilter
                                                    OR cis.full_address_2 LIKE :contactFilter
                                                )";
                    break;
                case 'phone' :
                    $contactFilterCondition = "AND (
                                                    cis.primary_mobile LIKE :contactFilter
                                                    OR cis.alternate_mobile LIKE :contactFilter
                                                    OR cis.home_phone LIKE :contactFilter
                                                    OR cis.office_phone LIKE :contactFilter
                                                    OR cis.alternate_mobile_provider LIKE :contactFilter
                                                )";
                    break;
                default:
                    $contactFilterCondition = "AND (
                                                    cis.address_1 LIKE :contactFilter
                                                    OR cis.address_2 LIKE :contactFilter
                                                    OR cis.city LIKE :contactFilter
                                                    OR cis.state LIKE :contactFilter
                                                    OR cis.zip LIKE :contactFilter
                                                    OR cis.country LIKE :contactFilter
                                                    OR cis.primary_mobile LIKE :contactFilter
                                                    OR cis.alternate_mobile LIKE :contactFilter
                                                    OR cis.home_phone LIKE :contactFilter
                                                    OR cis.office_phone LIKE :contactFilter
                                                    OR cis.alternate_email LIKE :contactFilter
                                                    OR cis.primary_mobile_provider LIKE :contactFilter
                                                    OR cis.alternate_mobile_provider LIKE :contactFilter
                                                    OR cis.full_address_1 LIKE :contactFilter
                                                    OR cis.full_address_2 LIKE :contactFilter
                                                )";
                    break;

            }

            $parameters['contactFilter'] = "%$contactFilter%";
            $parameterTypes['contactFilter'] = 'string';
        }

        if (!is_null($personFilter)) {

            $personFilterCondition = "AND (
                                            ps.external_id LIKE :personFilter
                                            OR ps.firstname LIKE :personFilter
                                            OR ps.lastname LIKE :personFilter
                                            OR ps.username LIKE :personFilter
                                            OR ps.first_and_last_name LIKE :personFilter
                                            OR ps.last_and_first_name LIKE :personFilter
                                        )";

            $parameters['personFilter'] = "%$personFilter%";
            $parameterTypes['personFilter'] = 'string';
        }


        $sql = "SELECT
                    ps.person_id
                FROM
                    synapse.person_search ps
                        JOIN
                    synapse.contact_info_search cis
                            ON cis.person_id = ps.person_id
                WHERE
                    ps.organization_id = :organizationId
                    $personFilterCondition
                    $contactFilterCondition
                ORDER BY ps.lastname ASC, ps.firstname ASC, ps.username ASC, ps.external_id ASC, ps.person_id DESC";

        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        if (count($result) > 0) {
            return array_column($result, 'person_id');
        } else {
            return [];
        }
    }

    /**
     * Gets contact details for the list of personIds
     *
     * @param integer $organizationId
     * @param array $personIds
     * @param integer $offset
     * @param integer $recordsPerPage
     * @return array
     */
    public function getUsersContactInfo($organizationId, $personIds, $offset, $recordsPerPage)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'personIds' => $personIds
        ];

        $parameterTypes = [
            'personIds' => Connection::PARAM_INT_ARRAY
        ];

        if (!is_null($offset) && !is_null($recordsPerPage)) {

            $paginationText = "LIMIT :limit OFFSET :offset ";
            $parameters['offset'] = $offset;
            $parameters['limit'] = $recordsPerPage;

            $parameterTypes ['offset'] = \PDO::PARAM_INT;
            $parameterTypes ['limit'] = \PDO::PARAM_INT;
        } else {
            $paginationText = "";
        }


        $sql = "SELECT
                    ps.external_id,
                    ps.firstname,
                    ps.lastname,
                    ps.username AS primary_email,
                    cis.address_1 AS address_one,
                    cis.address_2 AS address_two,
                    cis.city,
                    cis.state,
                    cis.zip,
                    cis.country,
                    cis.primary_mobile,
                    cis.alternate_mobile,
                    cis.home_phone,
                    cis.office_phone,
                    cis.alternate_email,
                    cis.primary_mobile_provider,
                    cis.alternate_mobile_provider
                FROM
                    synapse.person_search ps
                        JOIN
                    synapse.contact_info_search cis
                            ON cis.person_id = ps.person_id
                WHERE
                    ps.organization_id = :organizationId
                    AND ps.person_id IN (:personIds)
                ORDER BY ps.lastname ASC, ps.firstname ASC, ps.username ASC, ps.external_id ASC, ps.person_id DESC
                $paginationText ";

        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;
    }
}
