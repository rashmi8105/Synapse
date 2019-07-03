<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\SurveyBundle\Repository\FactorRepository;


class FactorReportRepository extends Test
{
    use Codeception\Specify;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var FactorRepository
     */
    private $factorRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->factorRepository = $this->repositoryResolver->getRepository(FactorRepository::REPOSITORY_KEY);
    }

    public function testGetFactorQuestions() {

        $this->specify('test get factor questions', function($factorId, $surveyId, $expectedResults) {
            $functionResults = $this->factorRepository->getFactorQuestions($factorId, $surveyId);
            verify($functionResults)->equals($expectedResults);
        }, ['examples' => [
            //Test 1: Valid survey ID, valid factor ID.
            [
                1,
                11,
                [
                    0 => [
                        'question' => 'To what degree are you committed to completing a: Degree, certificate, or licensure at this institution?',
                        'question_id' => '9'
                    ],
                    1 => [
                        'question' => 'To what degree do you intend to come back to this institution for the: Next term?',
                        'question_id' => '81'
                    ],
                    2 => [
                        'question' => 'To what degree do you intend to come back to this institution for the: Next academic year?',
                        'question_id' => '82'
                    ]
                ]
            ],
            //Test 2: Valid survey ID, invalid factor ID.
            [
                'books',
                11,
                []
            ],
            //Test 3: Invalid survey ID, valid factor ID.
            [
                1,
                'tablet',
                []
            ],
            //Test 4: Invalid survey ID, invalid factor ID.
            [
                'books',
                'tablet',
                []
            ],
            //Test 5: Valid survey ID, valid factor ID.
            [
                1,
                14,
                [
                    0 => [
                        'question' => 'To what degree are you committed to completing a: Degree, certificate, or licensure at this institution?',
                        'question_id' => '655'
                    ],
                    1 => [
                        'question' => 'To what degree do you intend to come back to this institution for the: Next academic term?',
                        'question_id' => '727'
                    ],
                    2 => [
                        'question' => 'To what degree do you intend to come back to this institution for the: Next academic year?',
                        'question_id' => '728'
                    ]
                ]
            ]
        ]]);
    }

}