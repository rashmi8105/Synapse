<?php

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository;


class OrgRiskGroupModelRepositoryTest extends Test
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
     * @var OrgRiskGroupModelRepository
     */
    private $orgGroupModelRepository;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->orgGroupModelRepository = $this->repositoryResolver->getRepository(\Synapse\RiskBundle\Repository\OrgRiskGroupModelRepository::REPOSITORY_KEY);
    }


    public function testGetRiskGroupsForOrganization()
    {
        $this->specify("Verify the functionality of the method getRiskGroupsForOrganization", function ($organizationId, $expectedCount, $expectedResults) {

            $results = $this->orgGroupModelRepository->getRiskGroupsForOrganization($organizationId);
            verify(count($results))->equals($expectedCount);
            verify($results)->equals($expectedResults);

        }, ["examples" =>
            [
                // organization with results
                [62, 3,
                    [
                        0 => [
                            "risk_model_id" => null,
                            "risk_model_name" => null,
                            "model_state" => null,
                            "calculation_start_date" => null,
                            "calculation_stop_date" => null,
                            "enrollment_end_date" => null,
                            "risk_group_id" => "3",
                            "risk_group_name" => "2015-2016 No Risk Model Students ID: 3",
                            "student_count" => "15920",
                            "risk_group_description" => "This risk group should include students who are not on a model for risk calculation."],
                        1 => [
                            "risk_model_id" => "34",
                            "risk_model_name" => "1516 4yr Waffles",
                            "model_state" => "Unassigned",
                            "calculation_start_date" => "2015-08-14 00:00:00",
                            "calculation_stop_date" => "2016-06-30 00:00:00",
                            "enrollment_end_date" => "2016-03-31 00:00:00",
                            "risk_group_id" => "2",
                            "risk_group_name" => "2015-2016 Continuing Student ID: 2",
                            "student_count" => "7420",
                            "risk_group_description" => "This group should include 3rd-semester freshmen, sophomores, and new transfer students who enter the institution as either freshmen or sophomores during the 2015-16 academic year. This group should not include upperclass students."],
                        2 => [
                            "risk_model_id" => "13",
                            "risk_model_name" => "1517 4yr Corn Flakes",
                            "model_state" => "Unassigned",
                            "calculation_start_date" => "2015-08-13 00:00:00",
                            "calculation_stop_date" => "2017-06-30 00:00:00",
                            "enrollment_end_date" => "2016-03-31 00:00:00",
                            "risk_group_id" => "1",
                            "risk_group_name" => "2015-2016 New Students ID: 1",
                            "student_count" => "6310",
                            "risk_group_description" => "This group should include new, first-time freshmen (often referred to as first-time matriculates) who begin during the 2015-16 academic year. This group should not include new transfer students or third-semester freshmen."]],
                ],
                // invalid organization Id test
                [-1, 0, []],
            ]
        ]);
    }
}