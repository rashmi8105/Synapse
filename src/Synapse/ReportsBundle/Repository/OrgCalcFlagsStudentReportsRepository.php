<?php
namespace Synapse\ReportsBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Entity\OrgCalcFlagsStudentReports;

class OrgCalcFlagsStudentReportsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:OrgCalcFlagsStudentReports';

    /**
     * Gets a list of all students who have undergone Report Calculation, but have no pdf filename
     * Essentially, getting a list of students who need pdf generation
     *
     * @return array
     */
    public function getStudentsWithCalculatedReportAndNoPDF()
    {
        $sql = "SELECT
                    person_id as student_id,
                    survey_id
                FROM
                    org_calc_flags_student_reports
                WHERE
                    calculated_at IS NOT NULL
                    AND calculated_at <> '1900-01-01 00:00:00'
                    AND file_name IS NULL
                    AND survey_id IS NOT NULL
                    AND deleted_at IS NULL;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $studentList = $stmt->fetchAll();

        return $studentList;
    }

    /**
     * Retrieve existing student report id
     *
     * @param int $studentId
     * @param int $surveyId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentReportId($studentId, $surveyId)
    {
        $parameters = [
            'studentId' => $studentId,
            'surveyId' => $surveyId
        ];
        $sql = "SELECT
                    id AS calculated_student_report_id
                FROM
                    org_calc_flags_student_reports
                WHERE
                    calculated_at IS NOT NULL
                        AND file_name IS NULL
                        AND person_id = :studentId
                        AND survey_id = :surveyId
                        AND deleted_at IS NULL
                ORDER BY id ASC;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();
        return $resultSet;

    }


    /**
     * Retrieve list of students who need a partially complete email sent
     *
     * @return array
     */
    public function getStudentsNeedingPartiallyCompleteStudentReportEmail()
    {
        $sql = "SELECT
                    ocfsr.id AS ocfsr_id,
                    ocfsr.person_id AS student_id
                FROM
                    org_calc_flags_student_reports ocfsr
                      INNER JOIN org_person_student_survey_link opssl ON ocfsr.org_id = opssl.org_id
                          AND ocfsr.person_id = opssl.person_Id
                          AND ocfsr.survey_id = opssl.survey_id
                      INNER JOIN org_person_student_year opsy ON opsy.person_id =  opssl.person_id
                          AND opsy.organization_id = opssl.org_id
                          AND opsy.org_academic_year_id = opssl.org_academic_year_id
                WHERE
                    ocfsr.calculated_at IS NOT NULL
                    AND calculated_at <> '1900-01-01 00:00:00'
                    AND ocfsr.file_name IS NOT NULL
                    AND ocfsr.survey_id IS NOT NULL
                    AND ocfsr.completion_email_sent = 0
                    AND ocfsr.in_progress_email_sent = 0
                    AND opssl.survey_completion_status <> 'CompletedAll'
                    AND ocfsr.created_at + INTERVAL 30 MINUTE < NOW()
                    AND ocfsr.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();


            $stmt = $em->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $studentList = $stmt->fetchAll();

        return $studentList;

    }


    /**
     * Retrieve list of students who need a report completion email sent
     *
     * @return array
     */
    public function getStudentsNeedingCompletedStudentReportEmail()
    {
        $sql = "SELECT
                    ocfsr.id AS ocfsr_id,
                    ocfsr.person_id AS student_id
                FROM
                    org_calc_flags_student_reports ocfsr
                    INNER JOIN org_person_student_survey_link opssl ON ocfsr.org_id = opssl.org_id
                        AND ocfsr.person_id = opssl.person_Id
                        AND ocfsr.survey_id = opssl.survey_id
                    INNER JOIN org_person_student_year opsy ON opsy.person_id =  opssl.person_id 
                        AND opsy.organization_id = opssl.org_id
                        AND opsy.org_academic_year_id = opssl.org_academic_year_id
                WHERE
                    ocfsr.calculated_at IS NOT NULL
                    AND calculated_at <> '1900-01-01 00:00:00'
                    AND ocfsr.file_name IS NOT NULL
                    AND ocfsr.survey_id IS NOT NULL
                    AND ocfsr.completion_email_sent = 0
                    AND opssl.survey_completion_status = 'CompletedAll'
                    AND ocfsr.deleted_at IS NULL
                    AND opssl.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $studentList = $stmt->fetchAll();

        return $studentList;

    }


    /**
     * Returns the name of the last generated PDF (based off of highest record id).  If there is none, return the string
     * "NoReportFound.pdf".
     *
     * @param $survey_id
     * @param $person_id
     * @return array|string
     */
    public function getLastStudentReportGeneratedPdfName($survey_id, $person_id)
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('ocr.fileName')
            ->from('SynapseReportsBundle:OrgCalcFlagsStudentReports', 'ocr')
            ->where('ocr.survey = :survey_id')
            ->andwhere('ocr.person = :person_id')
            ->andwhere('ocr.fileName is not null')
            ->orderBy('ocr.id', 'DESC')
            ->setMaxResults(1)
            ->setParameters(array(
                'survey_id' => $survey_id,
                'person_id' => $person_id
            ))
            ->getQuery();
        $pdfName = $qb->getResult();
        $pdfName = empty($pdfName[0]['fileName']) ? 'NoReportFound.pdf' : $pdfName[0]['fileName'];

        return $pdfName;
    }


    /**
     * Returns the calculated at date of the last calculation for the passed in student_id (based off of highest record id).
     * If there is none, returns an empty string.
     *
     * @param $person_id
     * @return array|string
     */
    public function getLastCalculatedAtDateForStudent($person_id)
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('ocr.calculatedAt')
            ->from('SynapseReportsBundle:OrgCalcFlagsStudentReports', 'ocr')
            ->where('ocr.person = :person_id')
            ->andwhere('ocr.calculatedAt is not null')
            ->orderBy('ocr.id', 'DESC')
            ->setMaxResults(1)
            ->setParameters(array(
                'person_id' => $person_id
            ))
            ->getQuery();
        $calculatedAt = $qb->getResult();
        $calculatedAt = empty($calculatedAt[0]['calculatedAt']) ? '' : $calculatedAt[0]['calculatedAt'];

        return $calculatedAt;
    }


    /**
     * Based on a student survey report ID, get the student's cohort, survey_id, organization_id, and person_id.
     *
     * @param int $studentSurveyReportId
     * @return array
     */
    public function getStudentSurveyDetailsUsingStudentReportID($studentSurveyReportId)
    {
        $parameters = ['studentSurveyReportId' => $studentSurveyReportId];

        $sql = "
            SELECT
                opsc.cohort,
                wl.survey_id,
                wl.year_id,
                opsc.person_id
            FROM
                org_calc_flags_student_reports ocfsr
                    JOIN
                wess_link wl ON wl.org_id = ocfsr.org_id
                    AND ocfsr.survey_id = wl.survey_id
                    JOIN
                org_academic_year oay ON oay.year_id = wl.year_id
                    AND oay.organization_id = wl.org_id
                    JOIN
                org_person_student_cohort opsc ON opsc.organization_id = wl.org_id
                    AND ocfsr.person_id = opsc.person_id
                    AND oay.id = opsc.org_academic_year_id
                    AND wl.cohort_code = opsc.cohort
            WHERE
                ocfsr.id = :studentSurveyReportId
                AND ocfsr.deleted_at IS NULL
                AND wl.deleted_at IS NULL
                AND oay.deleted_at IS NULL
                AND opsc.deleted_at IS NULL
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $result = $stmt->fetchAll();

        return $result[0];
    }

    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param int $id The identifier.
     * @param SynapseException | null $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return null|OrgCalcFlagsStudentReports
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }
}