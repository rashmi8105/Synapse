<?php
namespace Synapse\CampusResourceBundle\Repository;

use Synapse\CampusResourceBundle\Entity\OrgAnnouncementsLang;
use Synapse\CampusResourceBundle\Util\Constants\CampusResourceConstants;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CampusResourceBundle\Util\Constants\CampusAnnouncementConstants;

class OrgCampusAnnouncementLangRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCampusResourceBundle:OrgAnnouncementsLang';

    public function listCampusAnnouncements($type, $orgId, $loggedInUser, $currentDateTime)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(CampusAnnouncementConstants::OA_ID, 'oa.startDatetime as start_date_time', 'oa.stopDatetime as end_date_time', CampusAnnouncementConstants::OA_DISPLAY_TYPE, CampusAnnouncementConstants::OAL_MESSAGE,CampusAnnouncementConstants::OA_MESSAGE_DURATION);
        $qb->from(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_REPO, 'oa');
        $qb->join(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_LANG_REPO, 'oal', \Doctrine\ORM\Query\Expr\Join::WITH, CampusAnnouncementConstants::ID_EQUALS_ORG_ANNOUNCEMENTS);
        $qb->where('oa.orgId = :organizationid');
        if ($type == "scheduled") {
            $qb->andWhere('oa.stopDatetime >= :currentDateTime');
                $qb->andWhere(CampusAnnouncementConstants::OA_CREATOR_PERSON_ID);
                $qb->setParameters(array(
                    CampusAnnouncementConstants::ORGANIZATIN_ID => $orgId,
                    CampusAnnouncementConstants::PERSON_ID => $loggedInUser,
                    CampusAnnouncementConstants::CUR_DATE_TIME => $currentDateTime
                ));
        }
         else {
            $qb->andWhere('oa.stopDatetime < :currentDateTime');
            $qb->andWhere(CampusAnnouncementConstants::OA_CREATOR_PERSON_ID);
            $qb->setParameters(array(
                CampusAnnouncementConstants::ORGANIZATIN_ID => $orgId,
                CampusAnnouncementConstants::PERSON_ID => $loggedInUser,
                CampusAnnouncementConstants::CUR_DATE_TIME => $currentDateTime
            ));
        }
        $qb->orderBy(CampusAnnouncementConstants::OA_ID, 'desc');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getCampusAnnouncement($id, $orgId, $loggedInUser)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(CampusAnnouncementConstants::OA_ID, 'oa.startDatetime as start_date_time', 'oa.stopDatetime as end_date_time', CampusAnnouncementConstants::OA_DISPLAY_TYPE, CampusAnnouncementConstants::OAL_MESSAGE,CampusAnnouncementConstants::OA_MESSAGE_DURATION);
        $qb->from(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_REPO, 'oa');
        $qb->join(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_LANG_REPO, 'oal', \Doctrine\ORM\Query\Expr\Join::WITH, CampusAnnouncementConstants::ID_EQUALS_ORG_ANNOUNCEMENTS);
        $qb->where('oa.orgId = :organizationid');
        $qb->andWhere(CampusAnnouncementConstants::OA_CREATOR_PERSON_ID);
        $qb->andWhere('oa.id = :id');
        $qb->setParameters(array(
            CampusAnnouncementConstants::ORGANIZATIN_ID => $orgId,
            CampusAnnouncementConstants::PERSON_ID => $loggedInUser,
            'id' => $id
        ));
        $qb->orderBy(CampusAnnouncementConstants::OA_ID, 'desc');
        $query = $qb->getQuery();
        return $query->getResult();
    }


    public function listCampusAnnouncementsForFaculty($currentDateTime, $orgnazation)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(CampusAnnouncementConstants::OA_ID, CampusAnnouncementConstants::OA_DISPLAY_TYPE, CampusAnnouncementConstants::OAL_MESSAGE, 'oa.createdAt');
        $qb->from(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_REPO, 'oa');
        $qb->join(CampusAnnouncementConstants::ORG_ANNOUNCEMENTS_LANG_REPO, 'oal', \Doctrine\ORM\Query\Expr\Join::WITH, CampusAnnouncementConstants::ID_EQUALS_ORG_ANNOUNCEMENTS);
        $qb->where('oa.startDatetime <= :currentDateTime AND oa.stopDatetime >= :currentDateTime');
        $qb->andWhere('oa.displayType = :displayType');
        $qb->andWhere(CampusAnnouncementConstants::OA_ORGANIZATION_ID);
        $qb->setParameters(array(
            CampusAnnouncementConstants::CUR_DATE_TIME => $currentDateTime,
            'displayType' => 'alert bell',
            CampusAnnouncementConstants::ORGANIZATIN_ID => $orgnazation
        ));
        $qb->orderBy(CampusAnnouncementConstants::OA_ID, 'desc');
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Get all banner announcements for the logged in user.
     *
     * @param int $orgId
     * @param int $loggedInUser
     * @param string $currentDateTime -> Y-m-d H:i:s format
     * @return array
     */
    public function listBannerOrgAnnouncements($orgId, $loggedInUser, $currentDateTime)
    {
    	$parameters = [
    	    'organizationId' => $orgId,
            'personId' => $loggedInUser,
            'currentDateTime' => $currentDateTime
        ];

    	$sql = "
    	    SELECT 
                oa.start_datetime,
                oa.stop_datetime, 
                oa.display_type, 
                oal.message, 
                oa.message_duration,
                oa.id AS org_announcements_id
            FROM 
                synapse.org_announcements oa 
                    JOIN 
                synapse.org_announcements_lang oal ON oa.id = oal.org_announcements_id
                    LEFT JOIN 
                synapse.alert_notifications an ON an.org_announcements_id = oa.id 
                        AND an.deleted_at IS NULL 
                        AND an.person_id = :personId
            WHERE 
                oa.org_id = :organizationId
                AND an.id IS NULL
                AND oa.start_datetime <= :currentDateTime
                AND oa.stop_datetime >= :currentDateTime
                AND oa.display_type = 'banner'
            ORDER BY oa.id DESC
    	";

    	$results = $this->executeQueryFetchAll($sql, $parameters);

    	return $results;
    }
}