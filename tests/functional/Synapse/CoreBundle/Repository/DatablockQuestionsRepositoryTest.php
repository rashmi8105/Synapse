<?php

use Codeception\TestCase\Test;

class DatablockQuestionsRepositoryTest extends Test
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var \Synapse\CoreBundle\Repository\DatablockQuestionsRepository
     */
    private $datablockQuestionsRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->datablockQuestionsRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:DatablockQuestions');
    }


    public function testGetDatablocksNeededToViewStudentSurveyReport()
    {
        $expectedResults = [32, 33, 34, 35, 36, 42];
        $results = $this->datablockQuestionsRepository->getDatablocksNeededToViewStudentSurveyReport();

        verify(array_diff($results, $expectedResults))->equals([]);
        verify(array_diff($expectedResults, $results))->equals([]);
        verify(count($results))->equals(count($expectedResults));
    }

}