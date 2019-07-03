<?php

/**
 * Created by PhpStorm.
 * User: jlowenthal
 * Date: 1/8/16
 * Time: 2:53 PM
 */
class SurveyLangRepositoryTest extends \Codeception\TestCase\Test
{
    use Codeception\Specify;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    private $repositoryResolver;

    private $surveyLangRepository;

    public function testGetAllSurveys()
    {
        $this->beforeSpecify(function () {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->surveyLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:SurveyLang');
        });

        $this->specify("Verify that the SQL injection hole in getAllSurveys no longer returns results or does anything else", function($orgIdIn, $langIdIn,$expectedResultsSize){

            $results = $this->surveyLangRepository->getAllSurveys($orgIdIn, $langIdIn);
            $this->assertEquals($expectedResultsSize, count($results));
        }, ["examples"=>
                [
                    [1,1,3],
                    [1,2,0],
                    [2,1,0],
                    [null, 1,3],
                    ["11; INSERT INTO person ('id') VALUES (-90000); ##", 1, 0],
                    ["11 or wl.org_id = 1; ##", 1, 0]
                ]
            ]);
    }

}