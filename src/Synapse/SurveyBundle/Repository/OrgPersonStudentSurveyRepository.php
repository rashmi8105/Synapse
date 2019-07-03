<?php
namespace Synapse\SurveyBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\SurveyBundle\Entity\OrgPersonStudentSurvey;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;


class OrgPersonStudentSurveyRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseSurveyBundle:OrgPersonStudentSurvey';

    /**
     *
     * This would return the survey name and external_id for a year_id
     *
     * @param $yearId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getSurveyExternalIds($yearId)
    {

        $sql = "SELECT external_id,`name`  
                  FROM survey 
                  INNER JOIN survey_lang ON survey.id =  survey_lang.survey_id 
                  WHERE year_id = :yearId ";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, array(
            'yearId' => $yearId
        ));

        $resultSet = $stmt->fetchAll();

        $surveyArr = [];
        foreach ($resultSet as $result) {
            $surveyArr[$result['name']] = $result['external_id'];
        }
        return $surveyArr;
    }

}

