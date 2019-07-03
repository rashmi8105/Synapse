<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CoreBundle\Entity\EbiMetadata;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrgTalkingPointsRepository;
use Synapse\CoreBundle\Service\ContactsServiceInterface;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Entity\TalkingPoints;
use Synapse\RestBundle\Entity\TalkingPointsResponseDto;
use Synapse\CoreBundle\Entity\TalkingPointsLang;
use Synapse\CoreBundle\Util\Constants\TalkingPointConstant;

/**
 * @DI\Service("talkingpoint_service")
 */
class TalkingPointService extends AbstractService
{

    const SERVICE_KEY = 'talkingpoint_service';

    const TALK_POINTS = 'talking_points';

    const QUESTIONID = 'questionId';

    const QUESTION_TXT = 'questionText';

    const WEEKNESS = 'weakness';

    const STRENGTH = 'strength';

    const MODIFIED_AT = 'modifiedAt';



    /**
     * @var OrgTalkingPointsRepository
     */
    private $orgTalkingPointsRepository;

    /**
     * @var Container
     */
    private $container;



    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger"),
     *
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->orgTalkingPointsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgTalkingPoints');
    }

    function getTalkingPoints()
    {
        $this->logger->info("Get Talking Points");
        // $this->cacheTalkingPoints();
        $cache = $this->container->get('synapse_redis_cache');
        $getAllTalkingPoints = $cache->fetch(self::TALK_POINTS);

        if (! $getAllTalkingPoints) {
            $this->talkingPoints = $this->repositoryResolver->getRepository(TalkingPointConstant::SYNAPSE_TALKINGPOINTS_REPO);
            $getAllTalkingPoints = $this->talkingPoints->getAllTalkingPoints();
            $cache->save(self::TALK_POINTS, $getAllTalkingPoints, 7200);
        }

        $talkingPointArr = array();
        $repArr = array();

        foreach ($getAllTalkingPoints as $talkingPoint) {

            if ($talkingPoint['type'] == "S") {
                $arrKey = "Sur_" . $talkingPoint['qusetonid'];
                $talkingPointArr[$arrKey]['kind'] = "Survey";
                $talkingPointArr[$arrKey][self::QUESTIONID] = $talkingPoint['qusetonid'];
                $talkingPointArr[$arrKey][self::QUESTION_TXT] = $talkingPoint[self::QUESTION_TXT];
            }
            if ($talkingPoint['type'] == "P") {
                $arrKey = "Pro_" . $talkingPoint['metadateid'];
                $talkingPointArr[$arrKey]['kind'] = "Profile";
                $talkingPointArr[$arrKey][self::QUESTIONID] = $talkingPoint['metadateid'];
                $talkingPointArr[$arrKey][self::QUESTION_TXT] = $talkingPoint['metaName'];
            }
            if ($talkingPoint['talkingPointsType'] == "W") {
                $talkingPointArr[$arrKey][self::WEEKNESS] = $talkingPoint['description'];
                $talkingPointArr[$arrKey]['weakness_min'] = $talkingPoint['minRange'];
                $talkingPointArr[$arrKey]['weakness_max'] = $talkingPoint['maxRange'];
            } else {
                $talkingPointArr[$arrKey][self::STRENGTH] = $talkingPoint['description'];
                $talkingPointArr[$arrKey]['strength_max'] = $talkingPoint['maxRange'];
                $talkingPointArr[$arrKey]['strength_min'] = $talkingPoint['minRange'];
            }
        }

        $cnt = count($getAllTalkingPoints) - 1;
        if (! isset($getAllTalkingPoints[$cnt][self::MODIFIED_AT])) {
            $getAllTalkingPoints[$cnt][self::MODIFIED_AT] = '';
        }
        $repArr = $this->generateRespDto($talkingPointArr, $getAllTalkingPoints[$cnt][self::MODIFIED_AT]);
        $this->logger->info("Get Talking Points");
        return $repArr;
    }

    public function generateRespDto($talkingPointArr, $lastUpdated = '')
    {
        $repArr = array();
        foreach ($talkingPointArr as $talkingPoint) {
            $repObj = new TalkingPointsResponseDto();
            $repObj->setKind($talkingPoint['kind']);
            $repObj->setQuestionProfileItem($talkingPoint[self::QUESTIONID]);
            if (isset($talkingPoint[self::WEEKNESS])) {
                $repObj->setWeaknessText($talkingPoint[self::WEEKNESS]);
                $repObj->setWeaknessMin($talkingPoint['weakness_min']);
                $repObj->setWeaknessMax($talkingPoint['weakness_max']);
            }
            if (isset($talkingPoint[self::STRENGTH])) {
                $repObj->setStrengthText($talkingPoint[self::STRENGTH]);
                $repObj->setStrengthMin($talkingPoint['strength_min']);
                $repObj->setStrengthMax($talkingPoint['strength_max']);
            }
            if (! $talkingPoint[self::QUESTION_TXT]) {
                $talkingPoint[self::QUESTION_TXT] = '';
            }
            $repObj->setQuestionText($talkingPoint[self::QUESTION_TXT]);
            $repArr[] = $repObj;
        }

        $finalArr['last_updated'] = $lastUpdated;
        $finalArr['total_talking_points'] = count($talkingPointArr);
        $finalArr[self::TALK_POINTS] = $repArr;
        return $finalArr;
    }

    public function cacheTalkingPoints()
    {
        $this->logger->info("Cache Talking Points");
        $cache = $this->container->get('synapse_redis_cache');
        $this->talkingPoints = $this->repositoryResolver->getRepository(TalkingPointConstant::SYNAPSE_TALKINGPOINTS_REPO);
        $getAllTalkingPoints = $this->talkingPoints->getAllTalkingPoints();
        $cache->save(self::TALK_POINTS, $getAllTalkingPoints, 7200);
    }

    public function saveTalkingPoints($ebiMetadata, $type, $strengthLow, $strengthHigh, $weaknessLow, $weaknessHigh, $existingTalkingPoints = null)
    {
       $this->logger->info(" Saving Talking Points");
        $this->talkingPoints = $this->repositoryResolver->getRepository(TalkingPointConstant::SYNAPSE_TALKINGPOINTS_REPO);
        $typesOfTalkingPoints = array(
            'W',
            'S'
        );
        $talkingPointsIDs = [];

        if ($existingTalkingPoints) {
            $existingTalkingPoints = array_reverse($existingTalkingPoints);
        }

        foreach ($typesOfTalkingPoints as $idx => $typesOfTalkingPoint) {
            $talkingPoints = (! $existingTalkingPoints) ? new TalkingPoints() : $existingTalkingPoints[$idx];
            $talkingPoints->setEbiMetadata($ebiMetadata);
            $talkingPoints->setType($type);
            $talkingPoints->setTalkingPointsType($typesOfTalkingPoint);
            if ($typesOfTalkingPoint == 'W') {
                $talkingPoints->setMinRange($weaknessLow);
                $talkingPoints->setMaxRange($weaknessHigh);
            } else {
                $talkingPoints->setMinRange($strengthLow);
                $talkingPoints->setMaxRange($strengthHigh);
            }
            $talkingPoints = (! $existingTalkingPoints) ? $this->talkingPoints->persist($talkingPoints) : $this->talkingPoints->update($talkingPoints);
            $talkingPointsIDs[] = $talkingPoints->getID();
        }
        $this->logger->info(" Saved Talking Points Successfully");
        return $talkingPointsIDs;
    }

    /**
     * Deletes the last talking point for a given person based on a provided EBI Profile item
     *
     * @param int $organizationId
     * @param int $personId
     * @param int $metadataId
     * @param int $orgAcademicYearId
     * @param int $orgAcademicTermsId
     * @return bool
     */
    public function deleteLastOrgTalkingPointForPersonAndProfileItem($organizationId, $personId, $metadataId, $orgAcademicYearId, $orgAcademicTermsId)
    {
        $orgTalkingPointId = $this->orgTalkingPointsRepository->getLastOrgTalkingPointIdBasedOnStudentAndProfileItem($organizationId, $personId, $metadataId, $orgAcademicYearId, $orgAcademicTermsId);

        if ($orgTalkingPointId) {
            $orgTalkingPoint = $this->orgTalkingPointsRepository->find($orgTalkingPointId);
            if ($orgTalkingPoint) {
                $this->orgTalkingPointsRepository->delete($orgTalkingPoint);
                $this->logger->info("Talking Point ID:". $orgTalkingPointId . " deleted successfully");
            } else {
                $this->logger->info("Talking Point ID:". $orgTalkingPointId . " does not exist");
                return false;
            }
            return true;
        }

        $this->logger->info("No Talking Point associated with this personId and metadataId.");
        return false;
    }
}
