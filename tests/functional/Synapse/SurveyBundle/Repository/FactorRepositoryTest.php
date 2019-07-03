<?php

use Codeception\TestCase\Test;


class FactorRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\SurveyBundle\Repository\FactorRepository
     */
    private $factorRepository;


    public function testGetStudentFactorValues()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->factorRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        });

        $this->specify("Verify the functionality of the method testGetStudentFactorValues", function ($studentId, $surveyId, $expectedResult) {

            $results = $this->factorRepository->getStudentFactorValues($studentId, $surveyId);
            for ($i = 0; $i < count($expectedResult); $i++) {
                verify($results[$i]['survey_id'])->notEmpty();
                verify($results[$i]['survey_id'])->equals($expectedResult[$i]['survey_id']);
                verify($results[$i]['factor_id'])->notEmpty();
                verify($results[$i]['factor_id'])->equals($expectedResult[$i]['factor_id']);
                verify($results[$i]['factor_text'])->notEmpty();
                verify($results[$i]['factor_text'])->equals($expectedResult[$i]['factor_text']);
                verify($results[$i]['mean_value'])->notEmpty();
                verify($results[$i]['mean_value'])->equals($expectedResult[$i]['mean_value']);
            }

        }, ["examples" => [[4891036, [ 11 ], [
            [
                'survey_id' => 11,
                'factor_id' => 13,
                'factor_text' => 'Homesickness: Distressed',
                'mean_value' => '2.0000'

            ],
            [
                'survey_id' => 11,
                'factor_id' => 12,
                'factor_text' => 'Homesickness: Separation',
                'mean_value' => '2.3333'

            ],
            [
                'survey_id' => 11,
                'factor_id' => 5,
                'factor_text' => 'Self-Assessment: Time Management',
                'mean_value' => '2.6667'

            ],
            [
                'survey_id' => 11,
                'factor_id' => 3,
                'factor_text' => 'Self-Assessment: Analytical Skills',
                'mean_value' => '3.0000'

            ]
        ]]]]);
    }
}