<?php
namespace Synapse\CoreBundle\Repository;
use Synapse\CoreBundle\Entity\Contacts;




class ContactsRepository extends SynapseRepository {

    const REPOSITORY_KEY = 'SynapseCoreBundle:Contacts';

    public function createContact($contacts){

        $em = $this->getEntityManager();
        $em->persist($contacts);
        return $contacts;
    }

    public function deleteContact(Contacts $contacts)
    {
        $em = $this->getEntityManager();
        $em->remove($contacts);
        return $contacts;
    }


    public function getStudentContacts($studentId,$orgId,$isInteraction){
         
        $em = $this->getEntityManager();
         
        if(!$isInteraction){
            $qb = $em->createQueryBuilder()
            ->select(' C.id as activity_id,
                    C.createdAt as  activity_date,
                    IDENTITY(C.personIdFaculty) as activity_created_by_id ,
                    P.firstname as activity_created_by_first_name,
                    P.lastname as activity_created_by_last_name,
                    AC.id as activity_reason_id,
                    AC.shortName as activity_reason_text,
                    IDENTITY(C.contactTypesId)  as activity_contact_type_id,
                    CTL.description  as activity_contact_type_text,
                    C.note as activity_description')
                    ->from('SynapseCoreBundle:Contacts', 'C')
                    ->LEFTJoin('SynapseCoreBundle:Person','P',
                            \Doctrine\ORM\Query\Expr\Join::WITH,
                            'C.personIdFaculty = P.id')
                            ->LEFTJoin('SynapseCoreBundle:ContactTypesLang','CTL',
                                    \Doctrine\ORM\Query\Expr\Join::WITH,
                                    'C.contactTypesId = CTL.contactTypesId')
                                    ->LEFTJoin('SynapseCoreBundle:ActivityCategory','AC',
                                            \Doctrine\ORM\Query\Expr\Join::WITH,
                                            'C.activityCategory = AC.id')
                                            ->where ( 'C.personIdStudent = :studentId' )
                                            ->andWhere('C.organization = :orgId')
                                            ->orderBy('C.createdAt','desc')
                                            ->setParameters ( array (
                                                    'studentId' => $studentId , 'orgId' => $orgId ))
                                                    ->getQuery ();
                                            $resultSet = $qb->getResult();

        }else{
            $qb = $em->createQueryBuilder()
            ->select(' C.id as activity_id,
                    C.createdAt as  activity_date,
                    IDENTITY(C.personIdFaculty) as activity_created_by_id ,
                    P.firstname as activity_created_by_first_name,
                    P.lastname as activity_created_by_last_name,
                    AC.id as activity_reason_id,
                    AC.shortName as activity_reason_text,
                    IDENTITY(C.contactTypesId)  as activity_contact_type_id,
                    CTL.description  as activity_contact_type_text,
                    C.note as activity_description')
                    ->from('SynapseCoreBundle:Contacts', 'C')
                    ->LEFTJoin('SynapseCoreBundle:Person','P',
                            \Doctrine\ORM\Query\Expr\Join::WITH,
                            'C.personIdFaculty = P.id')
                            ->LEFTJoin('SynapseCoreBundle:ContactTypes','CT',
                                    \Doctrine\ORM\Query\Expr\Join::WITH,
                                    'C.contactTypesId = CT.id')
                                    ->LEFTJoin('SynapseCoreBundle:ContactTypesLang','CTL',
                                            \Doctrine\ORM\Query\Expr\Join::WITH,
                                            'C.contactTypesId = CTL.contactTypesId')
                                            ->LEFTJoin('SynapseCoreBundle:ActivityCategory','AC',
                                                    \Doctrine\ORM\Query\Expr\Join::WITH,
                                                    'C.activityCategory = AC.id')
                                                    ->where ( 'C.personIdStudent = :studentId' )
                                                    ->andWhere('C.organization = :orgId')
                                                    ->andWhere('CT.parentContactTypesId = :parentId')
                                                    ->orderBy('C.createdAt','desc')
                                                    ->setParameters ( array (
                                                            'studentId' => $studentId , 'orgId' => $orgId,'parentId' => 1 ))
                                                            ->getQuery ();
                                                    $resultSet = $qb->getResult();
        }
        return $resultSet;
    }

}