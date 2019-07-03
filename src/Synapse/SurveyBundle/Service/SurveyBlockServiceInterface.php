<?php
namespace Synapse\SurveyBundle\Service;

interface SurveyBlockServiceInterface
{

    public function createSurveyBlock($surveyBlock);

    public function getSurveyBlocks();

    public function getAllSurveys($orgId);

    public function getSurveyBlockDetails($id);

    public function deleteSurveyBlock($id);

    public function getDataForBlocks($type, $surveyId);

    public function deleteSurveyBlockQuestion($id, $dataid);

    public function editSurveyBlock($surveyBlock);
}