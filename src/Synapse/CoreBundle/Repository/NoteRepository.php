<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\Note;


class NoteRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:Note';

    public function createNote($notes)
    {

        $em = $this->getEntityManager();
        $em->persist($notes);
        return $notes;

    }

    public function getNotes($noteId)
    {
        $em = $this->getEntityManager();
        $notes = $em->getRepository('SynapseCoreBundle:Note')->findOneBy(array(
                'id' => $noteId
        ));
        return $notes;
    }

    public function remove(Note $note)
    {
        $em = $this->getEntityManager();
        $em->remove($note);
    }

    public function getNoteTeamIds($noteId)
    {
        $em = $this->getEntityManager();
        $records = $em->createQueryBuilder()
        ->select('IDENTITY(noteteam.teamsId) as teams_id')
        ->from('SynapseCoreBundle:NoteTeams', 'noteteam')
        ->where('noteteam.noteId = :id')
        ->setParameter('id', $noteId)
        ->getQuery()->getResult();
        return $records;
    }

    public function getStudentNotes($studentId,$orgId)
    {

         
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select(' N.id as activity_id,
                N.createdAt as  activity_date,
                IDENTITY(N.personIdFaculty) as activity_created_by_id ,
                P.firstname as activity_created_by_first_name,
                P.lastname as activity_created_by_last_name,
                AC.id as activity_reason_id,
                AC.shortName as activity_reason_text,
                N.note as activity_description')
                ->from('SynapseCoreBundle:Note', 'N')
                ->LEFTJoin('SynapseCoreBundle:Person','P',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'N.personIdFaculty = P.id')
                        ->LEFTJoin('SynapseCoreBundle:ActivityCategory','AC',
                                \Doctrine\ORM\Query\Expr\Join::WITH,
                                'N.activityCategory = AC.id')
                                ->where ( 'N.personIdStudent = :studentId' )
                                ->andWhere('N.organization = :orgId')
                                ->orderBy('N.createdAt','desc')
                                ->setParameters ( array ('studentId' => $studentId , 'orgId' => $orgId ))
                                ->getQuery ();
        $resultSet = $qb->getResult();
        return $resultSet;
    }
}