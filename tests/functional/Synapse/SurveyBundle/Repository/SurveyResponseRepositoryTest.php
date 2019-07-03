<?php

/**
 * Class SurveyResponseRepositoryTest
 */

use Codeception\TestCase\Test;

class SurveyResponseRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\SurveyBundle\Repository\SurveyResponseRepository
     */
    private $surveyResponseRepository;

    private $orgId = 203;
    private $personId = 4878750;

    public function testGetStudentsBasedQuestionPermission()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->surveyResponseRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:SurveyResponse');
        });

        $this->specify("Verify the functionality of the method getGroupStaffList", function($ebiQuesIdArr, $studentIds, $expectedResultsSize, $expectedIds){

            $results = $this->surveyResponseRepository->getStudentsBasedQuestionPermission($this->personId, $this->orgId, $ebiQuesIdArr, $studentIds);

            verify(count($results))->equals($expectedResultsSize);

            for($i = 0; $i < count($expectedIds); $i++){
                verify($results[$i]['datablock_id'])->notEmpty();
                verify($results[$i]['datablock_id'])->equals($expectedIds[$i]);
            }
        }, ["examples"=>
            [
                [[8,9],'4878808, 4878809',4, [41, 41, 30, 30]],
                [[9,10],'4878809, 4878810',4, [30, 30, 51, 51]],
                [[10,11],'4878810, 4878811',2, [51, 51]]
            ]
        ]);
    }
}