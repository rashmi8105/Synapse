<?php

namespace Synapse\CoreBundle\Repository;


use Synapse\CoreBundle\Entity\OrgTalkingPoints;
use Doctrine\ORM\Query\ResultSetMapping;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;


class OrgTalkingPointsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgTalkingPoints';

    public function getTalkingPoints($studentId,$orgLangId)
    {
        $em = $this->getEntityManager();
        $qb1 = $em->createQueryBuilder ()
        ->select ('IDENTITY(otp.talkingPoints) as talkingPointsId, otp.createdAt,tpl.title, tp.talkingPointsType,tpl.description')
        ->from ( 'SynapseCoreBundle:OrgTalkingPoints', 'otp' )
        ->LEFTJoin('SynapseCoreBundle:TalkingPoints','tp',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'tp.id = otp.talkingPoints')
                ->LEFTJoin('SynapseCoreBundle:TalkingPointsLang','tpl',
                        \Doctrine\ORM\Query\Expr\Join::WITH,
                        'tpl.talkingPoints = tp.id')
                        ->where ( 'otp.person = :person' )
                        ->andWhere('tpl.languageMaster = :lang')
                        ->setParameters ( array (
                                'person' => $studentId,
                                'lang' => $orgLangId) )
                                ->addOrderBy('tp.talkingPointsType','ASC')
                                ->addOrderBy('otp.createdAt','ASC')
                                ->getQuery ();
                        $resultSet = $qb1->getResult();

                        return $resultSet;
    }


    /**
     * Returns all data needed for talking points based on survey questions to be displayed on the student profile page,
     * including survey names, restricted by the user's permissions.
     *
     * @param int $studentId
     * @param int $orgLangId - 1 for English
     * @param array $surveyBlocks - survey datablocks the logged-in user is allowed to see
     * @return array
     */
    public function getOrgTalkingPointsBasedOnSurveyQuestions($studentId, $orgLangId, $surveyBlocks)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder ()
            ->select ('IDENTITY(otp.talkingPoints) as talkingPointsId,
                otp.sourceModifiedAt,
                tp.type as source,
                tp.talkingPointsType,
                tpl.title,
                tpl.description,
                sl.name as surveyName')

            ->from ('SynapseCoreBundle:OrgTalkingPoints', 'otp')
            ->join('SynapseCoreBundle:TalkingPoints','tp', \Doctrine\ORM\Query\Expr\Join::WITH, 'tp.id = otp.talkingPoints')
            ->join('SynapseCoreBundle:TalkingPointsLang','tpl', \Doctrine\ORM\Query\Expr\Join::WITH, 'tpl.talkingPoints = tp.id')
            ->join('SynapseCoreBundle:DatablockQuestions','dq', \Doctrine\ORM\Query\Expr\Join::WITH, 'dq.ebiQuestion = tp.ebiQuestion')
            ->join('SynapseCoreBundle:SurveyLang','sl', \Doctrine\ORM\Query\Expr\Join::WITH, 'sl.survey = otp.survey')

            ->where ('otp.person = :person')
            ->andWhere('tp.type = :S')
            ->andWhere('tpl.languageMaster = :lang')
            ->andWhere('dq.datablock IN (:surveyBlocks)')

            ->setParameters ( array (
                'person' => $studentId,
                'lang' => $orgLangId,
                'surveyBlocks' => $surveyBlocks,
                'S' => 'S'
            ) )
            ->addOrderBy('otp.sourceModifiedAt','DESC')
            ->getQuery();

        $resultSet = $qb->getResult();
        return $resultSet;

    }


    /**
     * Returns all data needed for talking points based on profile items to be displayed on the student profile page,
     * including names of terms and years for term/year-dependent items, restricted by the user's permissions.
     *
     * @param int $studentId
     * @param int $orgLangId - 1 for English
     * @param array $profileBlocks - profile datablocks the logged-in user is allowed to see
     * @return array
     */
    public function getOrgTalkingPointsBasedOnProfileItems($studentId, $orgLangId, $profileBlocks)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder ()
            ->select ('IDENTITY(otp.talkingPoints) as talkingPointsId,
                otp.sourceModifiedAt,
                tp.type as source,
                tp.talkingPointsType,
                tpl.title,
                tpl.description,
                em.scope,
                oay.name as orgAcademicYear,
                oat.name as orgAcademicTerm')

            ->from ('SynapseCoreBundle:OrgTalkingPoints', 'otp')
            ->join('SynapseCoreBundle:TalkingPoints','tp', \Doctrine\ORM\Query\Expr\Join::WITH, 'tp.id = otp.talkingPoints')
            ->join('SynapseCoreBundle:TalkingPointsLang','tpl', \Doctrine\ORM\Query\Expr\Join::WITH, 'tpl.talkingPoints = tp.id')
            ->join('SynapseCoreBundle:DatablockMetadata','dm', \Doctrine\ORM\Query\Expr\Join::WITH, 'dm.ebiMetadata = tp.ebiMetadata')
            ->join('SynapseCoreBundle:EbiMetadata','em', \Doctrine\ORM\Query\Expr\Join::WITH, 'em.id = tp.ebiMetadata')
            ->leftJoin('SynapseAcademicBundle:OrgAcademicYear','oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.id = otp.orgAcademicYear')
            ->leftJoin('SynapseAcademicBundle:OrgAcademicTerms','oat', \Doctrine\ORM\Query\Expr\Join::WITH, 'oat.id = otp.orgAcademicTerms')

            ->where ('otp.person = :person')
            ->andWhere('tp.type = :P')
            ->andWhere('tpl.languageMaster = :lang')
            ->andWhere('dm.datablock IN (:profileBlocks)')

            ->setParameters ( array (
                'person' => $studentId,
                'lang' => $orgLangId,
                'profileBlocks' => $profileBlocks,
                'P' => 'P'
            ) )
            ->addOrderBy('otp.sourceModifiedAt','DESC')
            ->getQuery();

        $resultSet = $qb->getResult();
        return $resultSet;
    }


    /**
     * Returns last entry for a specific person and metadata combination (if available)
     *
     * @param int $organizationId
     * @param int $personId
     * @param int $metadataId
     * @param int $orgAcademicYearId
     * @param int $orgAcademicTermId
     * @return array
     */
    public function getLastOrgTalkingPointIdBasedOnStudentAndProfileItem($organizationId, $personId, $metadataId, $orgAcademicYearId, $orgAcademicTermId)
    {
        $sql = "SELECT
                    otp.id
                FROM org_talking_points otp
                    INNER JOIN talking_points tp ON tp.id = otp.talking_points_id
                    INNER JOIN person_ebi_metadata pem ON pem.ebi_metadata_id = tp.ebi_metadata_id
                        AND pem.person_id = otp.person_id
                        AND (pem.org_academic_terms_id = otp.org_academic_terms_id
                            OR pem.org_academic_terms_id IS NULL)
                        AND (pem.org_academic_year_id = otp.org_academic_year_id
                            OR pem.org_academic_year_id IS NULL)
                WHERE
                    otp.organization_id = :organizationId
                    AND otp.person_id = :personId
                    AND pem.ebi_metadata_id = :metadataId
                    AND pem.org_academic_year_id <=> :orgAcademicYearId
                    AND pem.org_academic_terms_id <=> :orgAcademicTermsId
                    AND otp.modified_at >= pem.modified_at
                    AND otp.deleted_at IS NULL
                    AND pem.deleted_at IS NULL
                    AND tp.deleted_at IS NULL
                ORDER BY otp.id DESC
                LIMIT 1;
        ";


        try {
            $parameters = ['organizationId' => $organizationId, 'personId' => $personId, 'metadataId' => $metadataId, 'orgAcademicYearId' => $orgAcademicYearId, 'orgAcademicTermsId' => $orgAcademicTermId];
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        //Since we know only one result will come back, putting it in integer form
        if (isset($results[0]['id'])) {
            $orgTalkingPointId = $results[0]['id'];
        } else {
            $orgTalkingPointId = null;
        }

        return $orgTalkingPointId;

    }

}