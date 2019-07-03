<?php
namespace Synapse\CoreBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class OrgPersonStudentListener
{
    /**
     * Soft-deletes a student from the org_person_student_year table before soft-deleting from org_person_student
     *
     * @param OrgPersonStudent $orgPersonStudent
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function preRemove(OrgPersonStudent $orgPersonStudent, LifecycleEventArgs $lifecycleEventArgs)
    {
        $studentId = $orgPersonStudent->getPerson()->getId();

        $parameters = ['personId' => $studentId];

        $sql = "UPDATE org_person_student_year SET deleted_at = NOW() WHERE person_id = :personId";

        try {
            $entityManager = $lifecycleEventArgs->getEntityManager();
            $connection = $entityManager->getConnection();
            $connection->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }
}