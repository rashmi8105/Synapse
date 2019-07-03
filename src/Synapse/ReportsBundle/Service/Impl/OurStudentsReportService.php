<?php

namespace Synapse\ReportsBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\ReportsBundle\DAO\OurStudentsReportDAO;


/**
 * @DI\Service("our_students_report_service")
 */
class OurStudentsReportService extends AbstractService
{

    const SERVICE_KEY = 'our_students_report_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var OurStudentsReportDAO
     */
    private $ourStudentsReportDAO;


    /**
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // DAO
        $this->ourStudentsReportDAO = $this->container->get('our_students_report_dao');
    }


    /**
     * Gets and formats the data for the sections of the Our Students Report which are based on survey responses and factors.
     *
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param int $surveyId
     * @param array $studentIds
     * @return array
     */
    public function getSurveyBasedSections($loggedInUserId, $organizationId, $surveyId, $studentIds)
    {
        // Get the aggregated data.
        $elementsFromFactors = $this->ourStudentsReportDAO->getFactorResponseCounts($loggedInUserId, $organizationId, $surveyId, $studentIds);

        $elementsFromQuestions = $this->ourStudentsReportDAO->getSurveyResponseCounts($loggedInUserId, $organizationId, $surveyId, $studentIds);

        $elements = array_merge($elementsFromFactors, $elementsFromQuestions);

        // Group the data by section, with section names as the array keys.
        $sections = [];

        foreach ($elements as $element) {
            $sectionName = $element['section_name'];

            if (!array_key_exists($sectionName, $sections)) {
                $sections[$sectionName] = [];
            }

            $percentage = round(100 * ($element['numerator_count'] / $element['denominator_count']), 1);

            $sections[$sectionName][] = [
                'name' => $element['element_name'],
                'count' => $element['denominator_count'],
                'percentage' => $percentage,
                'section_id' => $element['section_id'],
                'element_id' => $element['element_id'],
            ];
        }

        // Sort and format the data.
        $sectionsToReturn = [];

        foreach ($sections as $sectionName => $elements) {
            // Sort the elements in each section in descending order by percentages.
            usort($elements, function($a, $b) {
                if ($a['percentage'] == $b['percentage']) {
                    return 0;
                }
                return ($a['percentage'] < $b['percentage']) ? 1 : -1;
            });

            $elementsToReturn = [];

            foreach ($elements as $element) {
                $elementsToReturn[] = [
                    'element_id' => $element['element_id'],
                    'name' => $element['name'],
                    'count' => $element['count'],
                    'percentage' => $element['percentage']
                ];
            }

            $sectionsToReturn[] = [
                'section_id' => $elements[0]['section_id'],
                'title' => $sectionName,
                'elements' => $elementsToReturn
            ];
        }

        return $sectionsToReturn;
    }

}