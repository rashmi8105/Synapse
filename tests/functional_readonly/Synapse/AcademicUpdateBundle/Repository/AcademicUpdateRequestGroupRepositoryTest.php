<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestGroupRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;

class AcademicUpdateRequestGroupRepositoryTest extends \Codeception\TestCase\Test
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
     * @var AcademicUpdateRequestGroupRepository
     */
    private $academicUpdateRequestGroupRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->academicUpdateRequestGroupRepository = $this->repositoryResolver->getRepository(AcademicUpdateRequestGroupRepository::REPOSITORY_KEY);
    }

    public function testIsAUExistsForGroup()
    {
        $this->specify("Verify the functionality of the method isAUExistsForGroup", function ($groupId, $expectedArray, $expectedResultCount) {

            $results = $this->academicUpdateRequestGroupRepository->isAUExistsForGroup($groupId);
            verify($results)->equals($expectedArray);
            verify(count($results))->equals($expectedResultCount);
        }
            ,
            [
                "examples" => [

                    // Test 01 - Valid group id = 366486 will return result array and expected result count as 5
                    [
                        366486,
                        [
                            0 => ['id' => 3],
                            1 => ['id' => 17],
                            2 => ['id' => 50],
                            3 => ['id' => 75],
                            4 => ['id' => 95]
                        ], 5],

                    // Test 02 - Valid group id = 290606 will return result array and expected result count as 5
                    [
                        290606,
                        [
                            0 => ['id' => 4],
                            1 => ['id' => 18],
                            2 => ['id' => 51],
                            3 => ['id' => 77],
                            4 => ['id' => 99]
                        ], 5],

                    // Test 03 - Invalid group id will return empty result array and expected result count as 0
                    [-1,[],0],

                    // Test 04 - Group id as null will return empty result array and expected result count as 0
                    [null,[],0]
                ]
            ]);
    }
}