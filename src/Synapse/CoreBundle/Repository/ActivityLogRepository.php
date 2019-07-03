<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\ActivityLog;
use Synapse\CoreBundle\Entity\AppointmentRecepientAndStatus;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Entity\Contacts;
use Synapse\CoreBundle\Entity\Note;
use Synapse\CoreBundle\Entity\ActivityCategory;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactTypesLang;
use Synapse\CoreBundle\Entity\RelatedActivities;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\ActivityLogConstant;

class ActivityLogRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:ActivityLog';

    public function createActivityLog($activitylog)
    {
        $em = $this->getEntityManager();
        $em->persist($activitylog);
        return $activitylog;
    }

    /**
     * Get appointment for student by faculty permission
     * TODO: Figure out a way to change the $sharingViewAccess to a flag that determines a column name instead of passing in a column name.
     *
     * @param int $studentId
     * @param int $organizationId
     * @param int $staffId
     * @param array $sharingViewAccess
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStudentAppointments($studentId, $organizationId, $staffId, $sharingViewAccess)
    {
        $parameters = [
            'studentId' => $studentId,
            'staffId' => $staffId,
            'organizationId' => $organizationId,
            'publicViewAccess' => $sharingViewAccess['public_view'],
            'teamViewAccess' => $sharingViewAccess['team_view']
        ];

        $sql = 'SELECT 
            a.id AS activity_id,
            al.id AS activity_log_id,
            a.start_date_time AS activity_date,
            a.person_id AS activity_created_by_id,
            p.firstname AS activity_created_by_first_name,
            p.lastname AS activity_created_by_last_name,
            ac.id AS activity_reason_id,
            ac.short_name AS activity_reason_text,
            a.description AS activity_description
        FROM
            activity_log al
                LEFT JOIN
            appointment_recepient_and_status aras ON al.appointments_id = aras.appointments_id
                AND aras.deleted_at IS NULL
                LEFT JOIN
            Appointments a ON aras.appointments_id = a.id
                AND a.deleted_at IS NULL
                LEFT JOIN
            appointments_teams as at ON a.id = at.appointments_id
                AND at.deleted_at IS NULL
                LEFT JOIN
            person p ON a.person_id = p.id
                AND p.deleted_at IS NULL
                LEFT JOIN
            activity_category ac ON a.activity_category_id = ac.id
                AND ac.deleted_at IS NULL
        WHERE
            al.person_id_student = :studentId
                AND al.organization_id = :organizationId
                AND al.activity_type = "A"
                AND al.deleted_at IS NULL
                AND (CASE
                WHEN
                    a.access_team = 1 AND :teamViewAccess = 1
                THEN
                    at.teams_id IN (SELECT
                            teams_id
                        FROM
                            team_members
                        WHERE
                            person_id = :staffId
                               AND teams_id IN (SELECT
                                    teams_id
                                FROM
                                    appointments_teams
                                WHERE
                                    appointments_id = a.id
                                        AND deleted_at IS NULL)
                                AND deleted_at IS NULL)
                ELSE CASE
                    WHEN a.access_private = 1 THEN aras.person_id_faculty = :staffId
                    ELSE a.access_public = 1 AND :publicViewAccess = 1
                    OR aras.person_id_faculty = :staffId
                END
            END
                )
        GROUP BY al.appointments_id
        ORDER BY a.start_date_time DESC, al.appointments_id DESC';

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    public function remove($activitylog)
    {
        $em = $this->getEntityManager();
        $em->remove($activitylog);
    }

    private function getContactRealtedActivity()
    {
        $em = $this->getEntityManager();
        $qbRelated = $em->createQueryBuilder();
        $relatedActivities = $qbRelated->select(ActivityLogConstant::AL_ID)
            ->from('SynapseCoreBundle:RelatedActivities', 'R')
            ->LEFTJoin(ActivityLogConstant::ACTIVITYLOG_ENTITY, 'AL', \Doctrine\ORM\Query\Expr\Join::WITH, 'AL.contacts = R.contacts')
            ->where('R.contacts IS NOT NULL')
            ->andwhere('R.deletedAt IS NULL')
            ->andwhere('AL.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
        
        if ($relatedActivities) {
            $finalArr = array();
            foreach ($relatedActivities as $related) {
                if ($related['id']) {
                    $finalArr[] = $related['id'];
                }
            }
            $finalArr = implode(',', $finalArr);
            return $finalArr;
        } else {
            return false;
        }
    }

    private function getNotesRealtedActivity()
    {
        $em = $this->getEntityManager();
        $qbRelated = $em->createQueryBuilder();
        $relatedActivities = $qbRelated->select(ActivityLogConstant::AL_ID)
            ->from('SynapseCoreBundle:RelatedActivities', 'R')
            ->LEFTJoin(ActivityLogConstant::ACTIVITYLOG_ENTITY, 'AL', \Doctrine\ORM\Query\Expr\Join::WITH, 'AL.note = R.note')
            ->where('R.note IS NOT NULL')
            ->andwhere('R.deletedAt IS NULL')
            ->andwhere('AL.deletedAt IS NULL')
        ->getQuery()
        ->getResult();
        if($relatedActivities){
            $finalArr =  array();
            foreach($relatedActivities as $related)
            {
                if($related['id']){
                    $finalArr[] = $related['id'];
                }
            }
            $finalArr= implode(',',$finalArr);
            return $finalArr;
        }else{
            return false;
        }
        
    }

    /**
     * Gets all activities count for the student with respect to faculty and permission set
     *
     * @param array $allVariablesInArray
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getActivityCount($allVariablesInArray)
    {
        $sql = "SELECT activity_type
        FROM
            (SELECT 
                AL.activity_type AS activity_type, AL.id
            FROM
                activity_log AS AL
            LEFT JOIN appointment_recepient_and_status AS ARS ON (AL.appointments_id = ARS.appointments_id)
                AND (ARS.deleted_at IS NULL)
            LEFT JOIN Appointments AS A ON (ARS.appointments_id = A.id)
            LEFT JOIN appointments_teams AS APT ON A.id = APT.appointments_id
            LEFT JOIN note AS N ON AL.note_id = N.id
            LEFT JOIN note_teams AS NT ON N.id = NT.note_id
            LEFT JOIN contacts AS C ON AL.contacts_id = C.id
            LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id
            LEFT JOIN email AS E ON AL.email_id = E.id
            LEFT JOIN email_teams AS ET ON E.id = ET.email_id
            LEFT JOIN referrals AS R ON AL.referrals_id = R.id
            LEFT JOIN referrals_teams AS RT ON (R.id = RT.referrals_id AND RT.deleted_at IS NULL)
            LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id
                OR N.activity_category_id = AC.id
                OR R.activity_category_id = AC.id
                OR C.activity_category_id = AC.id
            LEFT JOIN related_activities as RA ON (N.id = RA.note_id
                OR C.id = RA.contacts_id)
            LEFT JOIN activity_log AL1 ON RA.activity_log_id = AL1.id
            LEFT JOIN referrals AS R1 ON AL1.referrals_id = R1.id
            LEFT JOIN note AS N1 ON AL1.note_id = N1.id
            LEFT JOIN contacts AS C1 ON AL1.contacts_id = C1.id
            LEFT JOIN Appointments AS A1 ON AL1.appointments_id = A1.id
            LEFT JOIN person AS P ON AL.person_id_faculty = P.id
            LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id
            LEFT JOIN organization_role as orgr ON orgr.organization_id = AL.organization_id
            LEFT JOIN referral_routing_rules as rr ON rr.activity_category_id = R.activity_category_id
            WHERE
                AL.person_id_student = :studentId
                    AND AL.organization_id = :organizationId
                    AND AL.activity_type IN (:activityArray)
                    AND AL.deleted_at IS NULL
                    AND A.deleted_at IS NULL
                    AND APT.deleted_at IS NULL
                    AND N.deleted_at IS NULL
                    AND NT.deleted_at IS NULL
                    AND C.deleted_at IS NULL
                    AND CT.deleted_at IS NULL
                    AND E.deleted_at IS NULL
                    AND ET.deleted_at IS NULL
                    AND R.deleted_at IS NULL
                    AND AC.deleted_at IS NULL
                    AND P.deleted_at IS NULL
                    AND CTL.deleted_at IS NULL
                    AND rr.deleted_at IS NULL
                    AND (CASE
                    WHEN
                        AL.activity_type = 'N'
                    THEN
                        CASE
                            WHEN
                                AL1.activity_type IS NOT NULL
                                    AND ((AL1.activity_type = 'R'
                                    AND R1.access_private = 1)
                                    OR (AL1.activity_type = 'C'
                                    AND C1.access_private = 1)
                                    OR (AL1.activity_type = 'N'
                                    AND N1.access_private = 1))
                            THEN
                                N.person_id_faculty = :facultyId
                            ELSE CASE
                                WHEN
                                    N.access_team = 1
                                THEN
                                    NT.teams_id IN (SELECT 
                                            teams_id
                                        FROM
                                            team_members
                                        WHERE
                                            person_id = :facultyId
                                                AND teams_id IN (SELECT 
                                                    teams_id
                                                FROM
                                                    note_teams
                                                WHERE
                                                    note_id = N.id AND deleted_at IS NULL)
                                                AND deleted_at IS NULL)
                                        AND :noteTeamAccess = 1
                                ELSE CASE
                                    WHEN N.access_private = 1 THEN N.person_id_faculty = :facultyId
                                    ELSE N.access_public = 1
                                        AND :notePublicAccess = 1
                                END
                            END
                        END
                            OR N.person_id_faculty = :facultyId
                    ELSE CASE
                        WHEN
                            AL.activity_type = 'C'
                        THEN
                            CASE
                                WHEN
                                    AL1.activity_type IS NOT NULL
                                        AND ((AL1.activity_type = 'R'
                                        AND R1.access_private = 1)
                                        OR (AL1.activity_type = 'C'
                                        AND C1.access_private = 1)
                                        OR (AL1.activity_type = 'N'
                                        AND N1.access_private = 1))
                                THEN
                                    C.person_id_faculty = :facultyId
                                ELSE CASE
                                    WHEN
                                        C.access_team = 1
                                    THEN
                                        CT.teams_id IN (SELECT 
                                                teams_id
                                            FROM
                                                team_members
                                            WHERE
                                                person_id = :facultyId
                                                    AND teams_id IN (SELECT 
                                                        teams_id
                                                    from
                                                        contacts_teams
                                                    WHERE
                                                        contacts_id = C.id
                                                            AND deleted_at IS NULL)
                                                    AND deleted_at IS NULL)
                                            AND :contactTeamAccess = 1
                                    ELSE CASE
                                        WHEN C.access_private = 1 THEN C.person_id_faculty = :facultyId
                                        ELSE C.access_public = 1
                                            AND :contactPublicAccess = 1
                                    END
                                END
                            END
                                OR C.person_id_faculty = :facultyId
                        ELSE CASE
                            WHEN
                                AL.activity_type = 'E'
                            THEN
                                CASE
                                    WHEN
                                        AL1.activity_type IS NOT NULL
                                            AND ((AL1.activity_type = 'R'
                                            AND R1.access_private = 1)
                                            OR (AL1.activity_type = 'C'
                                            AND C1.access_private = 1)
                                            OR (AL1.activity_type = 'N'
                                            AND N1.access_private = 1))
                                    THEN
                                        E.person_id_faculty = :facultyId
                                    ELSE CASE
                                        WHEN
                                            E.access_team = 1
                                        THEN
                                            ET.teams_id IN (SELECT 
                                                    teams_id
                                                FROM
                                                    team_members
                                                WHERE
                                                    person_id = :facultyId
                                                        AND teams_id IN (SELECT 
                                                            teams_id
                                                        from
                                                            email_teams
                                                        WHERE
                                                            email_id = E.id AND deleted_at IS NULL)
                                                        AND deleted_at IS NULL)
                                                AND :emailTeamAccess = 1
                                        ELSE CASE
                                            WHEN E.access_private = 1 THEN E.person_id_faculty = :facultyId
                                            ELSE E.access_public = 1
                                                AND :emailPublicAccess = 1
                                        END
                                    END
                                END
                                    OR E.person_id_faculty = :facultyId
                            ELSE CASE
                                WHEN
                                    AL.activity_type = 'R'
                                THEN
                                    CASE
                                        WHEN
                                            R.access_team = 1
                                        THEN
                                            RT.teams_id IN (SELECT 
                                                    teams_id
                                                FROM
                                                    team_members
                                                WHERE
                                                    person_id = :facultyId
                                                        AND teams_id IN (SELECT 
                                                            teams_id
                                                        FROM
                                                            referrals_teams
                                                        WHERE
                                                            referrals_id = R.id
                                                                AND deleted_at IS NULL)
                                                        AND deleted_at IS NULL)
                                                AND ((:referralTeamAccess = 1
                                                AND R.is_reason_routed = 0)
                                                OR (:referralTeamAccessReasonRouted = 1
                                                AND R.is_reason_routed = 1))
                                        ELSE CASE
                                            WHEN R.access_private = 1 THEN R.person_id_faculty = :facultyId
                                            ELSE R.access_public = 1
                                                AND ((:referralPublicAccess = 1
                                                AND R.is_reason_routed = 0)
                                                OR (:referralPublicAccessReasonRouted = 1
                                                AND R.is_reason_routed = 1))
                                        END
                                    END
                                        OR R.person_id_assigned_to = :facultyId
                                        OR R.person_id_faculty = :facultyId
                                        OR (orgr.person_id = :facultyId
                                        AND orgr.role_id IN (:roleIdsArray)
                                        AND orgr.deleted_at IS NULL
                                        AND R.person_id_student = :studentId
                                        AND R.person_id_assigned_to IS NULL)
                                ELSE CASE
                                    WHEN
                                        AL.activity_type = 'A'
                                    THEN
                                        CASE
                                            WHEN
                                                A.access_team = 1
                                            THEN
                                               ( APT.teams_id IN (SELECT
                                                        teams_id
                                                    FROM
                                                        team_members
                                                    WHERE
                                                        person_id = :facultyId
                                                            AND teams_id IN (SELECT 
                                                                teams_id
                                                            FROM
                                                                appointments_teams
                                                            WHERE
                                                                appointments_id = A.id
                                                                    AND deleted_at IS NULL)
                                                            AND deleted_at IS NULL)
                                                    AND :appointmentTeamAccess = 1 ) OR ARS.person_id_faculty = :facultyId
                                            ELSE CASE
                                                WHEN A.access_private = 1 THEN ARS.person_id_faculty = :facultyId
                                                ELSE A.access_public = 1
                                                    AND :appointmentPublicAccess = 1 OR ARS.person_id_faculty = :facultyId
                                            END
                                        END
                                    ELSE 1 = 1
                                END
                            END
                        END
                    END
                END)
            GROUP BY AL.id 
            UNION ALL 
            (SELECT 'R' AS activity_type, AL.id
            FROM referrals_interested_parties AS rip
            LEFT join referrals as R2 ON R2.id = rip.referrals_id
            LEFT JOIN activity_log AS AL ON AL.referrals_id = R2.id
            LEFT JOIN referrals_teams AS RT ON R2.id = RT.referrals_id
            WHERE
                rip.person_id = :facultyId
                    AND R2.person_id_student = :studentId
                    AND rip.deleted_at is null
                    AND (CASE
                    WHEN
                        R2.access_team = 1
                    THEN
                        ((:referralTeamAccess = 1
                            AND R2.is_reason_routed = 0)
                            OR (:referralTeamAccessReasonRouted = 1
                            AND R2.is_reason_routed = 1))
                    ELSE CASE
                        WHEN
                            R2.access_private = 1
                        THEN
                            (R2.person_id_faculty = :facultyId
                                OR rip.person_id = :facultyId)
                        ELSE R2.access_public = 1
                            AND ((:referralPublicAccess = 1
                            AND R2.is_reason_routed = 0)
                            OR (:referralPublicAccessReasonRouted = 1
                            AND R2.is_reason_routed = 1))
                    END
                END))) merged
        GROUP BY id , activity_type";

        $parameters = [
            'studentId' => $allVariablesInArray['studentId'],
            'activityArray' => explode(',', str_replace('"', '', $allVariablesInArray['activityArray'])),
            'facultyId' => $allVariablesInArray['faculty'],
            'organizationId' => $allVariablesInArray['orgId'],
            'noteTeamAccess' => $allVariablesInArray['noteTeamAccess'],
            'notePublicAccess' => $allVariablesInArray['notePublicAccess'],
            'contactTeamAccess' => $allVariablesInArray['contactTeamAccess'],
            'contactPublicAccess' => $allVariablesInArray['contactPublicAccess'],
            'referralTeamAccess' => $allVariablesInArray['referralTeamAccess'],
            'referralPublicAccess' => $allVariablesInArray['referralPublicAccess'],
            'referralPublicAccessReasonRouted' => $allVariablesInArray['referralPublicAccessReasonRouted'],
            'referralTeamAccessReasonRouted' => $allVariablesInArray['referralTeamAccessReasonRouted'],
            'appointmentTeamAccess' => $allVariablesInArray['appointmentTeamAccess'],
            'appointmentPublicAccess' => $allVariablesInArray['appointmentPublicAccess'],
            'emailTeamAccess' => $allVariablesInArray['emailTeamAccess'],
            'emailPublicAccess' => $allVariablesInArray['emailPublicAccess'],
            'roleIdsArray' => $allVariablesInArray['roleIds']
        ];
        $parameterTypes = [
            'activityArray' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            'roleIdsArray' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
        ];

        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $results;
    }

    /**
     * Gets all activities for the student with respect to faculty and permission set
     *
     * @param array $allVariablesInArray
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getActivityAll($allVariablesInArray)
    {
        $sql = "SELECT *
        FROM
            ((SELECT 
                A.id AS AppointmentId,
                    N.id AS NoteId,
                    R.id AS ReferralId,
                    C.id AS ContactId,
                    AL.id AS activity_log_id,
                    AL.created_at AS activity_date,
                    AL.activity_type AS activity_type,
                    AL.person_id_faculty AS activity_created_by_id,
                    P.firstname AS activity_created_by_first_name,
                    P.lastname AS activity_created_by_last_name,
                    AC.id AS activity_reason_id,
                    AC.short_name AS activity_reason_text,
                    C.contact_types_id AS activity_contact_type_id,
                    CTL.description AS activity_contact_type_text,
                    R.status AS activity_referral_status,
                    C.note AS contactDescription,
                    R.note AS referralDescription,
                    A.description AS appointmentDescription,
                    N.note AS noteDescription,
                    AL.created_at AS created_date,
                    E.id AS EmailId,
                    E.email_subject AS activity_email_subject,
                    E.email_body AS activity_email_body,
                    A.start_date_time AS app_created_date,
                    C.contact_date AS contact_created_date,
                    AL.activity_date AS act_date
            FROM
                activity_log AS AL
            LEFT JOIN appointment_recepient_and_status AS ARS ON (AL.appointments_id = ARS.appointments_id)
                AND (ARS.deleted_at IS NULL)
            LEFT JOIN Appointments AS A ON (ARS.appointments_id = A.id)
            LEFT JOIN appointments_teams AS APT ON A.id = APT.appointments_id
            LEFT JOIN note AS N ON AL.note_id = N.id
            LEFT JOIN note_teams AS NT ON N.id = NT.note_id
            LEFT JOIN contacts AS C ON AL.contacts_id = C.id
            LEFT JOIN contacts_teams AS CT ON C.id = CT.contacts_id
            LEFT JOIN email AS E ON AL.email_id = E.id
            LEFT JOIN email_teams AS ET ON E.id = ET.email_id
            LEFT JOIN referrals AS R ON AL.referrals_id = R.id
            LEFT JOIN referrals_teams AS RT ON (R.id = RT.referrals_id AND RT.deleted_at IS NULL)
            LEFT JOIN activity_category AS AC ON A.activity_category_id = AC.id
                OR N.activity_category_id = AC.id
                OR R.activity_category_id = AC.id
                OR C.activity_category_id = AC.id
                OR E.activity_category_id = AC.id
            LEFT JOIN person AS P ON AL.person_id_faculty = P.id
            LEFT JOIN contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id
            LEFT JOIN organization_role AS orgr ON orgr.organization_id = AL.organization_id
            LEFT JOIN referral_routing_rules AS rr ON rr.activity_category_id = R.activity_category_id
            WHERE
                AL.person_id_student = :studentId
                    AND AL.organization_id = :organizationId
                    AND AL.activity_type IN (:activityArray)
                    AND AL.deleted_at IS NULL
                    AND A.deleted_at IS NULL
                    AND APT.deleted_at IS NULL
                    AND N.deleted_at IS NULL
                    AND NT.deleted_at IS NULL
                    AND C.deleted_at IS NULL
                    AND CT.deleted_at IS NULL
                    AND E.deleted_at IS NULL
                    AND ET.deleted_at IS NULL
                    AND R.deleted_at IS NULL
                    AND AC.deleted_at IS NULL
                    AND P.deleted_at IS NULL
                    AND CTL.deleted_at IS NULL
                    AND rr.deleted_at IS NULL
                    AND AL.id NOT IN (SELECT 
                        ALOG.id
                    FROM
                        related_activities AS related
                    INNER JOIN activity_log AS ALOG ON related.note_id = ALOG.note_id
                    WHERE
                        related.note_id IS NOT NULL
                            AND related.deleted_at IS NULL
                            AND ALOG.deleted_at IS NULL)
                    AND AL.id NOT IN (SELECT 
                        ALOG.id
                    FROM
                        related_activities AS related
                    INNER JOIN activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id
                    WHERE
                        related.contacts_id IS NOT NULL
                            AND related.deleted_at IS NULL
                            AND ALOG.deleted_at IS NULL)
                    AND AL.id NOT IN (SELECT 
                        ALOG.id
                    FROM
                        related_activities AS related
                    INNER JOIN activity_log AS ALOG ON related.email_id = ALOG.email_id
                    WHERE
                        related.email_id IS NOT NULL
                            AND related.deleted_at IS NULL
                            AND ALOG.deleted_at IS NULL)
                    AND AL.id NOT IN (SELECT 
                        ALOG.id
                    FROM
                        related_activities AS related
                    INNER JOIN activity_log AS ALOG ON related.referral_id = ALOG.referrals_id
                    WHERE
                        related.referral_id IS NOT NULL
                            AND related.deleted_at IS NULL
                            AND ALOG.deleted_at IS NULL)
                    AND AL.id NOT IN (SELECT 
                        ALOG.id
                    FROM
                        related_activities AS related
                    INNER JOIN activity_log AS ALOG ON related.appointment_id = ALOG.appointments_id
                    WHERE
                        related.appointment_id IS NOT NULL
                            AND related.deleted_at IS NULL
                            AND ALOG.deleted_at IS NULL)
                    AND (CASE
                    WHEN
                        AL.activity_type = 'N'
                    THEN
                        CASE
                            WHEN
                                N.access_team = 1
                            THEN
                                NT.teams_id IN (SELECT 
                                        teams_id
                                    FROM
                                        team_members
                                    WHERE
                                        person_id = :facultyId
                                            AND teams_id IN (SELECT 
                                                teams_id
                                            FROM
                                                note_teams
                                            WHERE
                                                note_id = N.id AND deleted_at IS NULL)
                                            AND deleted_at IS NULL)
                                    AND :noteTeamAccess = 1
                            ELSE CASE
                                WHEN N.access_private = 1 THEN N.person_id_faculty = :facultyId
                                ELSE N.access_public = 1
                                    AND :notePublicAccess = 1
                            END
                        END
                            OR N.person_id_faculty = :facultyId
                    ELSE CASE
                        WHEN
                            AL.activity_type = 'C'
                        THEN
                            CASE
                                WHEN
                                    C.access_team = 1
                                THEN
                                    CT.teams_id IN (SELECT 
                                            teams_id
                                        FROM
                                            team_members
                                        WHERE
                                            person_id = :facultyId
                                                AND teams_id IN (SELECT 
                                                    teams_id
                                                FROM
                                                    contacts_teams
                                                WHERE
                                                    contacts_id = C.id
                                                        AND deleted_at IS NULL)
                                                AND deleted_at IS NULL)
                                        AND :contactTeamAccess = 1
                                ELSE CASE
                                    WHEN C.access_private = 1 THEN C.person_id_faculty = :facultyId
                                    ELSE C.access_public = 1
                                        AND :contactPublicAccess = 1
                                END
                            END
                                OR C.person_id_faculty = :facultyId
                        ELSE CASE
                            WHEN
                                AL.activity_type = 'E'
                            THEN
                                CASE
                                    WHEN
                                        E.access_team = 1
                                    THEN
                                        ET.teams_id IN (SELECT 
                                                teams_id
                                            FROM
                                                team_members
                                            WHERE
                                                person_id = :facultyId
                                                    AND teams_id IN (SELECT 
                                                        teams_id
                                                    FROM
                                                        email_teams
                                                    WHERE
                                                        email_id = E.id AND deleted_at IS NULL)
                                                    AND deleted_at IS NULL)
                                            AND :emailTeamAccess = 1
                                    ELSE CASE
                                        WHEN E.access_private = 1 THEN E.person_id_faculty = :facultyId
                                        ELSE E.access_public = 1
                                            AND :emailPublicAccess = 1
                                    END
                                END
                                    OR E.person_id_faculty = :facultyId
                            ELSE CASE
                                WHEN
                                    AL.activity_type = 'R'
                                THEN
                                    CASE
                                        WHEN
                                            R.access_team = 1
                                        THEN
                                            RT.teams_id IN (SELECT 
                                                    teams_id
                                                FROM
                                                    team_members
                                                WHERE
                                                    person_id = :facultyId
                                                        AND teams_id IN (SELECT 
                                                            teams_id
                                                        FROM
                                                            referrals_teams
                                                        WHERE
                                                            referrals_id = R.id
                                                                AND deleted_at IS NULL)
                                                        AND deleted_at IS NULL)
                                                AND ((:referralTeamAccess = 1 AND is_reason_routed = 0)
                                                OR (:referralTeamAccessReasonRouted = 1
                                                AND is_reason_routed = 1))
                                        ELSE CASE
                                            WHEN R.access_private = 1 THEN R.person_id_faculty = :facultyId
                                            ELSE R.access_public = 1
                                                AND ((:referralPublicAccess = 1 AND is_reason_routed = 0)
                                                OR (:referralPublicAccessReasonRouted = 1
                                                AND is_reason_routed = 1))
                                        END
                                    END
                                        OR R.person_id_assigned_to = :facultyId
                                        OR R.person_id_faculty = :facultyId
                                        OR (orgr.person_id = :facultyId
                                        AND orgr.role_id IN (:roleIdsArray)
                                        AND orgr.deleted_at IS NULL
                                        AND R.person_id_student = :studentId
                                        AND R.person_id_assigned_to IS NULL)
                                ELSE CASE
                                    WHEN
                                        AL.activity_type = 'A'
                                    THEN
                                        CASE
                                            WHEN
                                                A.access_team = 1
                                            THEN
                                                ( APT.teams_id IN (SELECT
                                                        teams_id
                                                    FROM
                                                        team_members
                                                    WHERE
                                                        person_id = :facultyId
                                                            AND teams_id IN (SELECT 
                                                                teams_id
                                                            FROM
                                                                appointments_teams
                                                            WHERE
                                                                appointments_id = A.id
                                                                    AND deleted_at IS NULL)
                                                            AND deleted_at IS NULL)
                                                    AND :appointmentTeamAccess = 1 ) OR ARS.person_id_faculty = :facultyId
                                            ELSE CASE
                                                WHEN A.access_private = 1 THEN ARS.person_id_faculty = :facultyId
                                                ELSE A.access_public = 1
                                                    AND :appointmentPublicAccess = 1 OR ARS.person_id_faculty = :facultyId
                                            END
                                        END
                                    ELSE 1 = 1
                                END
                            END
                        END
                    END
                END)
            GROUP BY AL.id) 
            UNION ALL 
            (SELECT 
                NULL,
                    NULL,
                    R.id AS ReferralId,
                    NULL,
                    AL.id AS activity_log_id,
                    AL.created_at AS activity_date,
                    AL.activity_type AS activity_type,
                    AL.person_id_faculty AS activity_created_by_id,
                    P.firstname AS activity_created_by_first_name,
                    P.lastname AS activity_created_by_last_name,
                    AC.id AS activity_reason_id,
                    AC.short_name AS activity_reason_text,
                    NULL,
                    NULL,
                    R.status AS activity_referral_status,
                    NULL,
                    R.note AS referralDescription,
                    NULL,
                    NULL,
                    AL.created_at AS created_date,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    AL.activity_date AS act_date
            FROM
                activity_log AS AL
            LEFT JOIN referrals AS R ON AL.referrals_id = R.id
            LEFT JOIN referrals_teams AS RT ON R.id = RT.referrals_id
            LEFT JOIN activity_category AS AC ON R.activity_category_id = AC.id
            LEFT JOIN person AS P ON AL.person_id_faculty = P.id
            LEFT JOIN referrals_interested_parties AS rip ON rip.person_id = :facultyId
                AND R.id = rip.referrals_id
            WHERE
                rip.person_id = :facultyId
                    AND R.person_id_student = :studentId
                    AND AL.deleted_at IS NULL
                    AND R.deleted_at IS NULL
                    AND RT.deleted_at IS NULL
                    AND AC.deleted_at IS NULL
                    AND P.deleted_at IS NULL
                    AND rip.deleted_at IS NULL
                    AND (CASE
                    WHEN
                        R.access_team = 1
                    THEN
                        ((:referralTeamAccess = 1 AND is_reason_routed = 0)
                            OR (:referralTeamAccessReasonRouted = 1
                            AND is_reason_routed = 1))
                    ELSE CASE
                        WHEN
                            R.access_private = 1
                        THEN
                            (R.person_id_faculty = :facultyId
                                OR rip.person_id = :facultyId)
                        ELSE R.access_public = 1
                            AND ((:referralPublicAccess = 1 AND is_reason_routed = 0)
                            OR (:referralPublicAccessReasonRouted = 1
                            AND is_reason_routed = 1))
                    END
                END))) merger
        GROUP BY activity_log_id
        ORDER BY act_date DESC";

        $parameters = [
            'studentId' => $allVariablesInArray['studentId'],
            'activityArray' => explode(',', str_replace('"', '', $allVariablesInArray['activityArr'])),
            'facultyId' => $allVariablesInArray['faculty'],
            'organizationId' => $allVariablesInArray['orgId'],
            'noteTeamAccess' => $allVariablesInArray['noteTeamAccess'],
            'notePublicAccess' => $allVariablesInArray['notePublicAccess'],
            'contactTeamAccess' => $allVariablesInArray['contactTeamAccess'],
            'contactPublicAccess' => $allVariablesInArray['contactPublicAccess'],
            'referralTeamAccess' => $allVariablesInArray['referralTeamAccess'],
            'referralPublicAccess' => $allVariablesInArray['referralPublicAccess'],
            'referralPublicAccessReasonRouted' => $allVariablesInArray['referralPublicAccessReasonRouted'],
            'referralTeamAccessReasonRouted' => $allVariablesInArray['referralTeamAccessReasonRouted'],
            'appointmentTeamAccess' => $allVariablesInArray['appointmentTeamAccess'],
            'appointmentPublicAccess' => $allVariablesInArray['appointmentPublicAccess'],
            'emailTeamAccess' => $allVariablesInArray['emailTeamAccess'],
            'emailPublicAccess' => $allVariablesInArray['emailPublicAccess'],
            'roleIdsArray' => $allVariablesInArray['roleIds']

        ];
        $parameterTypes = [
            'activityArray' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            'roleIdsArray' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
        ];
        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $results;
    }
    /**
     * Fetching created referral for student by appropriate permission
     *
     * @param array $allVariablesInArray
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getActivityReferral($allVariablesInArray)
    {
        $sql = "SELECT 
                R.id AS activity_id,
                AL.id AS activity_log_id,
                R.referral_date AS activity_date,
                R.person_id_faculty AS activity_created_by_id,
                P.firstname AS activity_created_by_first_name,
                P.lastname AS activity_created_by_last_name,
                AC.id AS activity_reason_id,
                AC.short_name AS activity_reason_text,
                R.note AS activity_description,
                R.status AS activity_referral_status
            FROM
                activity_log AS AL
                    LEFT JOIN
                referrals AS R ON AL.referrals_id = R.id
                    LEFT JOIN
                person AS P ON R.person_id_faculty = P.id
                    LEFT JOIN
                activity_category AS AC ON R.activity_category_id = AC.id
                    LEFT JOIN
                referrals_teams AS RT ON R.id = RT.referrals_id
                    LEFT JOIN
                organization_role AS orgr ON orgr.organization_id = AL.organization_id
                    LEFT JOIN
                referral_routing_rules AS rr ON rr.activity_category_id = R.activity_category_id
            WHERE
                R.person_id_student = :studentId
                    AND R.organization_id = :organizationId
                    AND R.deleted_at IS NULL
                    AND (CASE
                    WHEN
                        access_team = 1
                    THEN
                        RT.teams_id IN (SELECT
                                teams_id
                            FROM
                                team_members
                            WHERE
                                person_id = :faculty
                                    AND teams_id IN (SELECT
                                        teams_id
                                    FROM
                                        referrals_teams
                                    WHERE
                                        referrals_id = R.id
                                            AND deleted_at IS NULL)
                                    AND deleted_at IS NULL)
                            AND ((:teamAccess = 1
                            AND is_reason_routed = 0)
                            OR (:teamAccessReasonRouted = 1
                            AND is_reason_routed = 1))
                    ELSE CASE
                        WHEN access_private = 1 THEN R.person_id_faculty = :faculty
                        ELSE R.access_public = 1
                            AND ((:publicAccess = 1
                            AND is_reason_routed = 0)
                            OR (:publicAccessReasonRouted = 1
                            AND is_reason_routed = 1))
                    END
                END
                    OR R.person_id_assigned_to = :faculty
                    OR R.person_id_faculty = :faculty
                    OR (orgr.person_id = :faculty
                    AND orgr.role_id IN (:roleIdsArray)
                    AND R.person_id_student = :studentId
                    AND orgr.deleted_at IS NULL
                    AND R.person_id_assigned_to IS NULL)
                    OR (R.id IN (SELECT
                        rip.referrals_id
                    FROM
                        referrals_interested_parties AS rip
                            LEFT JOIN
                        referrals AS R2 ON R2.id = rip.referrals_id
                    WHERE
                        rip.person_id = :faculty
                            AND R2.person_id_student = :studentId
                            AND rip.deleted_at IS NULL
                            AND (CASE
                            WHEN
                                access_team = 1
                            THEN
                               ((:teamAccess = 1
                                    AND is_reason_routed = 0)
                                    OR (:teamAccessReasonRouted = 1
                                    AND is_reason_routed = 1))
                            ELSE CASE
                                WHEN
                                    access_private = 1
                                THEN
                                    (R.person_id_faculty = :faculty
                                        OR rip.person_id = :faculty)
                                ELSE R.access_public = 1
                                    AND ((:publicAccess = 1
                                    AND is_reason_routed = 0)
                                    OR (:publicAccessReasonRouted = 1
                                    AND is_reason_routed = 1))
                            END
                        END))))
            GROUP BY R.id
            ORDER BY R.referral_date DESC";

        $parameters = [
            'studentId' => $allVariablesInArray['studentId'],
            'faculty' => $allVariablesInArray['faculty'],
            'organizationId' => $allVariablesInArray['orgId'],
            'publicAccess' => $allVariablesInArray['publicAccess'],
            'teamAccess' => $allVariablesInArray['teamAccess'],
            'publicAccessReasonRouted' => $allVariablesInArray['publicAccessReasonRouted'],
            'teamAccessReasonRouted' => $allVariablesInArray['teamAccessReasonRouted'],
            'roleIdsArray' => $allVariablesInArray['roleIds']
        ];
        $parameterTypes = [
            'roleIdsArray' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
        ];
        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $results;
    }

    /**
     * Gets all interaction activities for the student with respect to faculty and permission set
     *
     * @param array $allVariablesInArray
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getActivityAllInteraction($allVariablesInArray)
    {
        $sql = "SELECT 
                    A.id AS AppointmentId,
                    N.id AS NoteId,
                    R.id AS ReferralId,
                    C.id AS ContactId,
                    AL.id AS activity_log_id,
                    AL.created_at AS activity_date,
                    AL.activity_type AS activity_type,
                    AL.person_id_faculty AS activity_created_by_id,
                    P.firstname AS activity_created_by_first_name,
                    P.lastname AS activity_created_by_last_name,
                    AC.id AS activity_reason_id,
                    AC.short_name AS activity_reason_text,
                    C.contact_types_id AS activity_contact_type_id,
                    CTL.description AS activity_contact_type_text,
                    R.status AS activity_referral_status,
                    C.note AS contactDescription,
                    R.note AS referralDescription,
                    A.description AS appointmentDescription,
                    N.note AS noteDescription,
                    AL.created_at AS created_date,
                    A.start_date_time AS app_created_date,
                    C.contact_date AS contact_created_date,
                    AL.activity_date AS act_date
                FROM
                    activity_log AS AL
                        LEFT JOIN
                    Appointments AS A ON AL.appointments_id = A.id
                        LEFT JOIN
                    note AS N ON AL.note_id = N.id
                        LEFT JOIN
                    note_teams AS NT ON N.id = NT.note_id
                        LEFT JOIN
                    contacts AS C ON AL.contacts_id = C.id
                        LEFT JOIN
                    contacts_teams AS CT ON C.id = CT.contacts_id
                        LEFT JOIN
                    referrals AS R ON AL.referrals_id = R.id
                        LEFT JOIN
                    referrals_teams AS RT ON R.id = RT.referrals_id
                        LEFT JOIN
                    activity_category AS AC ON A.activity_category_id = AC.id
                        OR N.activity_category_id = AC.id
                        OR R.activity_category_id = AC.id
                        OR C.activity_category_id = AC.id
                        LEFT JOIN
                    person AS P ON AL.person_id_faculty = P.id
                        LEFT JOIN
                    contact_types_lang AS CTL ON C.contact_types_id = CTL.contact_types_id
                        LEFT JOIN
                    contact_types AS CONT ON C.contact_types_id = CONT.id
                        LEFT JOIN
                    organization_role AS orgr ON orgr.organization_id = AL.organization_id
                        LEFT JOIN
                    referral_routing_rules AS rr ON rr.activity_category_id = R.activity_category_id
                WHERE
                    AL.person_id_student = :studentId
                        AND AL.organization_id = :organizationId
                        AND AL.activity_type IN (:activityArray)
                        AND AL.deleted_at IS NULL
                        AND A.deleted_at IS NULL
                        AND N.deleted_at IS NULL
                        AND C.deleted_at IS NULL
                        AND R.deleted_at IS NULL
                        AND CASE
                        WHEN
                            AL.activity_type = 'C'
                        THEN
                            CONT.parent_contact_types_id = 1
                                OR CONT.id = 1
                        ELSE 1 = 1
                    END
                        AND AL.id NOT IN (SELECT
                            ALOG.id
                        FROM
                            related_activities AS related
                                LEFT JOIN
                            activity_log AS ALOG ON related.appointment_id = ALOG.appointments_id
                        WHERE
                            related.appointment_id IS NOT NULL
                                AND related.deleted_at IS NULL
                                AND ALOG.deleted_at IS NULL)
                        AND AL.id NOT IN (SELECT
                            ALOG.id
                        FROM
                            related_activities AS related
                                LEFT JOIN
                            activity_log AS ALOG ON related.referral_id = ALOG.referrals_id
                        WHERE
                            related.referral_id IS NOT NULL
                                AND related.deleted_at IS NULL
                                AND ALOG.deleted_at IS NULL)
                        AND AL.id NOT IN (SELECT
                            ALOG.id
                        FROM
                            related_activities AS related
                                LEFT JOIN
                            activity_log AS ALOG ON related.note_id = ALOG.note_id
                        WHERE
                            related.note_id IS NOT NULL
                                AND related.deleted_at IS NULL
                                AND ALOG.deleted_at IS NULL)
                        AND AL.id NOT IN (SELECT
                            ALOG.id
                        FROM
                            related_activities AS related
                                LEFT JOIN
                            activity_log AS ALOG ON related.contacts_id = ALOG.contacts_id
                        WHERE
                            related.contacts_id IS NOT NULL
                                AND related.deleted_at IS NULL
                                AND ALOG.deleted_at IS NULL)
                        AND CASE
                        WHEN
                            AL.activity_type = 'N'
                        THEN
                            CASE
                                WHEN
                                    N.access_team = 1
                                THEN
                                    NT.teams_id IN (SELECT
                                            teams_id
                                        FROM
                                            team_members
                                        WHERE
                                            person_id = :facultyId
                                                AND teams_id IN (SELECT
                                                    teams_id
                                                FROM
                                                    note_teams
                                                WHERE
                                                    note_id = N.id AND deleted_at IS NULL)
                                                AND deleted_at IS NULL)
                                        AND :noteTeamAccess = 1
                                ELSE CASE
                                    WHEN N.access_private = 1 THEN N.person_id_faculty = :facultyId
                                    ELSE N.access_public = 1
                                        AND :notePublicAccess = 1
                                END
                            END
                        ELSE CASE
                            WHEN
                                AL.activity_type = 'C'
                            THEN
                                CASE
                                    WHEN
                                        C.access_team = 1
                                    THEN
                                        CT.teams_id IN (SELECT
                                                teams_id
                                            FROM
                                                team_members
                                            WHERE
                                                person_id = :facultyId
                                                    AND teams_id IN (SELECT
                                                        teams_id
                                                    FROM
                                                        contacts_teams
                                                    WHERE
                                                        contacts_id = C.id
                                                            AND deleted_at IS NULL)
                                                    AND deleted_at IS NULL)
                                            AND :contactTeamAccess = 1
                                    ELSE CASE
                                        WHEN C.access_private = 1 THEN C.person_id_faculty = :facultyId
                                        ELSE C.access_public = 1
                                            AND :contactPublicAccess = 1
                                    END
                                END
                            ELSE CASE
                                WHEN
                                    AL.activity_type = 'R'
                                THEN
                                    CASE
                                        WHEN
                                            R.access_team = 1
                                        THEN
                                            RT.teams_id IN (SELECT
                                                    teams_id
                                                FROM
                                                    team_members
                                                WHERE
                                                    person_id = :facultyId
                                                        AND teams_id IN (SELECT
                                                            teams_id
                                                        FROM
                                                            referrals_teams
                                                        WHERE
                                                            referrals_id = R.id
                                                                AND deleted_at IS NULL)
                                                        AND deleted_at IS NULL)
                                                AND ((:referralTeamAccess = 1
                                                AND R.is_reason_routed = 0)
                                                OR (:referralTeamAccessReasonRouted = 1
                                                AND R.is_reason_routed = 1))
                                        ELSE CASE
                                            WHEN R.access_private = 1 THEN R.person_id_faculty = :facultyId
                                            ELSE R.access_public = 1
                                                AND ((:referralPublicAccess = 1
                                                AND R.is_reason_routed = 0)
                                                OR (:referralPublicAccessReasonRouted = 1
                                                AND R.is_reason_routed = 1))
                                        END
                                    END
                                        OR R.person_id_assigned_to = :facultyId
                                        OR R.person_id_faculty = :facultyId
                                        OR (orgr.person_id = :facultyId
                                        AND R.person_id_assigned_to IS NULL
                                        AND orgr.role_id IN (:roleIdsArray)
                                        AND orgr.deleted_at IS NULL)
                                        AND (rr.is_primary_coordinator = 1
                                        AND rr.person_id IS NULL)
                                ELSE CASE
                                    WHEN AL.activity_type = 'A' THEN 1 = 1
                                    ELSE 1 = 1
                                END
                            END
                        END
                    END
                GROUP BY AL.id
                ORDER BY act_date DESC";

        $parameters = [
            'studentId' => $allVariablesInArray['studentId'],
            'activityArray' => explode(',', str_replace('"', '', $allVariablesInArray['activityArr'])),
            'facultyId' => $allVariablesInArray['faculty'],
            'organizationId' => $allVariablesInArray['orgId'],
            'noteTeamAccess' => $allVariablesInArray['noteTeamAccess'],
            'notePublicAccess' => $allVariablesInArray['notePublicAccess'],
            'contactTeamAccess' => $allVariablesInArray['contactTeamAccess'],
            'contactPublicAccess' => $allVariablesInArray['contactPublicAccess'],
            'referralTeamAccess' => $allVariablesInArray['referralTeamAccess'],
            'referralPublicAccess' => $allVariablesInArray['referralPublicAccess'],
            'referralPublicAccessReasonRouted' => $allVariablesInArray['referralPublicAccessReasonRouted'],
            'referralTeamAccessReasonRouted' => $allVariablesInArray['referralTeamAccessReasonRouted'],
            'appointmentTeamAccess' => $allVariablesInArray['appointmentTeamAccess'],
            'appointmentPublicAccess' => $allVariablesInArray['appointmentPublicAccess'],
            'emailTeamAccess' => $allVariablesInArray['emailTeamAccess'],
            'emailPublicAccess' => $allVariablesInArray['emailPublicAccess'],
            'roleIdsArray' => $allVariablesInArray['roleIds']
        ];
        $parameterTypes = [
            'activityArray' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            'roleIdsArray' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY
        ];
        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $results;

    }

    /**
     * Gets the contacts activity count
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $featureId
     * @param int $participationOrgAcademicYearId
     * @return int|null
     */
    public function getContactActivityCount($facultyId, $studentId, $featureId, $participationOrgAcademicYearId)
    {
        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'featureId' => $featureId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];
        $sql = "SELECT 
                    COUNT(DISTINCT c.id) AS total_contacts
                FROM
                    (SELECT 
						DISTINCT faculty_id, 
						student_id, 
                        permissionset_id 
					FROM 
					    org_faculty_student_permission_map 
                    WHERE 
						faculty_id = :facultyId
                        AND student_id = :studentId) ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_permissionset_features opf ON opf.org_permissionset_id = op.id
                        AND opf.feature_id = :featureId
                        INNER JOIN
                    contacts c ON c.person_id_student = ofspm.student_id
                        INNER JOIN
                    person pStudent ON pStudent.id = c.person_id_student
                        LEFT JOIN
                    person pFaculty ON pFaculty.id = c.person_id_faculty
                        LEFT JOIN
                    contacts_teams ct ON ct.contacts_id = c.id
                        LEFT JOIN
                    team_members tm ON tm.teams_id = ct.teams_id
                        AND tm.person_id = ofspm.faculty_id
                WHERE
                    opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND op.accesslevel_ind_agg = 1
                        AND ((opf.public_create = 1
                        AND c.access_public = 1)
                        OR (opf.private_create = 1
                        AND c.person_id_faculty = ofspm.faculty_id)
                        OR (c.access_team = 1 AND tm.id IS NOT NULL
                        AND tm.person_id = ofspm.faculty_id))
                        AND pStudent.deleted_at IS NULL
                        AND pFaculty.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND c.deleted_at IS NULL
                        AND ct.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opf.deleted_at IS NULL
                        AND tm.deleted_at IS NULL;";
        $results = $this->executeQueryFetch($sql, $parameters);
        return $results['total_contacts'];
    }

    /**
     * Get the count of notes.
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $featureId
     * @param int $participationOrgAcademicYearId
     * @return int|null
     */
    public function getNoteActivityCount($facultyId, $studentId, $featureId, $participationOrgAcademicYearId)
    {
        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'featureId' => $featureId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];
        $sql = "SELECT
                    COUNT(DISTINCT n.id) AS total_notes
                FROM
                    (SELECT 
						DISTINCT faculty_id, 
						student_id, 
                        permissionset_id 
					FROM 
					    org_faculty_student_permission_map 
                    WHERE 
						faculty_id = :facultyId
                        AND student_id = :studentId) ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_permissionset_features opf ON opf.org_permissionset_id = op.id
                        AND opf.feature_id = :featureId
                        INNER JOIN
                    note n ON n.person_id_student = ofspm.student_id
                        INNER JOIN
                    person pStudent ON pStudent.id = n.person_id_student
                        INNER JOIN
                    person pFaculty ON pFaculty.id = n.person_id_faculty
                        LEFT JOIN
                    note_teams nt ON nt.note_id = n.id
                        LEFT JOIN
                    team_members tm ON tm.teams_id = nt.teams_id
                        AND tm.person_id = ofspm.faculty_id
                WHERE
                    opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND op.accesslevel_ind_agg = 1
                        AND ((opf.public_create = 1
                        AND n.access_public = 1)
                        OR (opf.private_create = 1
                        AND n.person_id_faculty = :facultyId)
                        OR (n.access_team = 1 AND tm.id IS NOT NULL))
                        AND pStudent.deleted_at IS NULL
                        AND pFaculty.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND n.deleted_at IS NULL
                        AND tm.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opf.deleted_at IS NULL
                        AND nt.deleted_at IS NULL;";
        $results = $this->executeQueryFetch($sql, $parameters);
        return $results['total_notes'];
    }

    /**
     * Get the count of email activity.
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $featureId
     * @param int $participationOrgAcademicYearId
     * @return int
     */
    public function getEmailActivityCount($facultyId, $studentId, $featureId, $participationOrgAcademicYearId)
    {
        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'featureId' => $featureId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];

        $sql = "SELECT
                    COUNT(DISTINCT e.id) AS total_email
                FROM
                    (SELECT 
						DISTINCT faculty_id, 
						student_id, 
                        permissionset_id 
					FROM 
					    org_faculty_student_permission_map 
                    WHERE 
						faculty_id = :facultyId
                        AND student_id = :studentId) ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_permissionset_features opf ON opf.org_permissionset_id = op.id
                        AND opf.feature_id = :featureId
                        INNER JOIN
                    email e ON e.person_id_student = ofspm.student_id
                        INNER JOIN
                    person pStudent ON pStudent.id = e.person_id_student
                        INNER JOIN
                    person pFaculty ON pFaculty.id = e.person_id_faculty
                        LEFT JOIN
                    email_teams et ON et.email_id = e.id
                        LEFT JOIN
                    team_members tm ON tm.teams_id = et.teams_id
                        AND tm.person_id = ofspm.faculty_id
                WHERE
                    opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND op.accesslevel_ind_agg = 1
                        AND ((opf.public_create = 1
                        AND e.access_public = 1)
                        OR (opf.private_create = 1
                        AND e.person_id_faculty = :facultyId)
                        OR (e.access_team = 1 AND tm.id IS NOT NULL))
                        AND pStudent.deleted_at IS NULL
                        AND pFaculty.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND e.deleted_at IS NULL
                        AND tm.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opf.deleted_at IS NULL
                        AND et.deleted_at IS NULL;";
        $results = $this->executeQueryFetch($sql, $parameters);
        return $results['total_email'];
    }

    /**
     * Get the count of appointments activity.
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $featureId
     * @param int $participationOrgAcademicYearId
     * @return int
     */
    public function getAppointmentsActivityCount($facultyId, $studentId, $featureId, $participationOrgAcademicYearId)
    {
        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'featureId' => $featureId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];
        $sql = "SELECT 
                    COUNT(DISTINCT a.id) AS total_appointments
                FROM
                    (SELECT 
						DISTINCT faculty_id, 
						student_id, 
                        permissionset_id 
					FROM 
					    org_faculty_student_permission_map 
                    WHERE 
						faculty_id = :facultyId
                        AND student_id = :studentId) ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_permissionset_features opf ON opf.org_permissionset_id = op.id
                        AND opf.feature_id = :featureId
                        INNER JOIN
                    appointment_recepient_and_status aras ON aras.person_id_student = ofspm.student_id
                        INNER JOIN
                    Appointments a ON a.id = aras.appointments_id
                        INNER JOIN
                    person pStudent ON pStudent.id = aras.person_id_student
                        INNER JOIN
                    person pFaculty ON pFaculty.id = aras.person_id_faculty
                        LEFT JOIN
                    appointments_teams appt ON appt.appointments_id = a.id
                        LEFT JOIN
                    team_members tm ON tm.teams_id = appt.teams_id
                        AND tm.person_id = ofspm.faculty_id
                WHERE
                    opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND op.accesslevel_ind_agg = 1
                        AND ((opf.public_create = 1
                        AND a.access_public = 1)
                        OR (opf.private_create = 1
                        AND a.person_id = ofspm.faculty_id)
                        OR (a.access_team = 1 AND tm.id IS NOT NULL))
                        AND pStudent.deleted_at IS NULL
                        AND pFaculty.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND a.deleted_at IS NULL
                        AND tm.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opf.deleted_at IS NULL
                        AND aras.deleted_at IS NULL
                        AND appt.deleted_at IS NULL";
        $results = $this->executeQueryFetch($sql, $parameters);
        return $results['total_appointments'];
    }

    /**
     * Gets the count of referral activity.
     *
     * @param int $facultyId
     * @param int $studentId
     * @param int $organizationId
     * @param int $featureId
     * @param int $participationOrgAcademicYearId
     * @return int
     */
    public function getReferralsActivityCount($facultyId, $studentId, $organizationId, $featureId, $participationOrgAcademicYearId)
    {
        $parameters = [
            'studentId' => $studentId,
            'facultyId' => $facultyId,
            'organizationId' => $organizationId,
            'featureId' => $featureId,
            'participationOrgAcademicYearId' => $participationOrgAcademicYearId
        ];

        $sql = "SELECT 
                    COUNT(DISTINCT r.id) AS total_referrals
                FROM
                    (SELECT 
						DISTINCT faculty_id, 
						student_id, 
                        permissionset_id 
					FROM 
					    org_faculty_student_permission_map 
                    WHERE 
						faculty_id = :facultyId
                        AND student_id = :studentId) ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_permissionset_features opf ON opf.org_permissionset_id = op.id
                        AND opf.feature_id = :featureId
                        INNER JOIN
                    referrals r ON r.person_id_student = ofspm.student_id
                        INNER JOIN
                    person pStudent ON pStudent.id = r.person_id_student
                        INNER JOIN
                    org_person_student ops ON ops.person_id = pStudent.id
                        INNER JOIN
                    referral_routing_rules rrr ON rrr.organization_id = r.organization_id
                        and rrr.activity_category_id = r.activity_category_id
                        LEFT JOIN
                    org_faculty_student_permission_map pFaculty ON pFaculty.faculty_id = r.person_id_faculty
                        and pFaculty.student_id = ofspm.student_id
                        LEFT JOIN
                    (
                          SELECT 
                            *
                        FROM
                            synapse.organization_role
                        WHERE
                            organization_id = :organizationId
                                AND organization_role.deleted_at IS NULL
                                AND role_id = 1
                        LIMIT 1
                    ) orgR ON orgR.organization_id = r.organization_id
                        and ofspm.faculty_id = orgR.person_id
                        LEFT JOIN
                    referrals_teams rt ON rt.referrals_id = r.id
                        AND rt.deleted_at IS NULL
                        LEFT JOIN
                    team_members tm ON tm.teams_id = rt.Teams_id
                        AND tm.person_id = ofspm.faculty_id
                        AND tm.deleted_at IS NULL
                WHERE
                        opsy.org_academic_year_id = :participationOrgAcademicYearId
                        AND op.accesslevel_ind_agg = 1
                        AND (
                                  (opf.public_create = 1 AND r.access_public = 1)
                              OR  (opf.private_create = 1 AND r.access_private = 1 AND r.person_id_faculty = ofspm.faculty_id)
                              OR  (r.access_team = 1 AND tm.id IS NOT NULL)
                              OR  (pFaculty.faculty_id = ofspm.faculty_id)
                              OR  (pFaculty.faculty_id IS NULL AND rrr.person_id = ofspm.faculty_id)
                              OR  (pFaculty.faculty_id IS NULL AND rrr.is_primary_campus_connection = 1 AND ops.person_id_primary_connect = ofspm.faculty_id)
                              OR  (pFaculty.faculty_id IS NULL AND rrr.is_primary_coordinator = 1 AND orgR.person_id = ofspm.faculty_id)
                            )
                        AND pStudent.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND ops.deleted_at IS NULL
                        AND r.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opf.deleted_at IS NULL";
        $results = $this->executeQueryFetch($sql, $parameters);
        return $results['total_referrals'];
    }
}