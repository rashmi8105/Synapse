<?php
use Codeception\TestCase\Test;
use Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository;

class RetentionCompletionVariableNameRepositoryTest extends Test
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
     * @var \Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository
     */
    private $retentionCompletionVariableNameRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->retentionCompletionVariableNameRepository = $this->repositoryResolver->getRepository(RetentionCompletionVariableNameRepository::REPOSITORY_KEY);
    }

    public function testGetRetentionVariablesOrderedByYearType()
    {

        $this->specify("Verify the functionality of the method getRetentionVariablesOrderedByYearType", function ($retentionTrackingYear, $yearLimit, $expectedResults) {
            $results = $this->retentionCompletionVariableNameRepository->getRetentionVariablesOrderedByYearType($retentionTrackingYear, $yearLimit);
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                //6 years between retention track and current year, should display all variables
                [201516, 202021, [
                    ["years_from_retention_track" => 0, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 1"],
                    ["years_from_retention_track" => 1, "is_midyear_variable" => 0, "retention_variable" => "Retained to Start of Year 2"],
                    ["years_from_retention_track" => 1, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 2"],
                    ["years_from_retention_track" => 2, "is_midyear_variable" => 0, "retention_variable" => "Retained to Start of Year 3"],
                    ["years_from_retention_track" => 2, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 3"],
                    ["years_from_retention_track" => 3, "is_midyear_variable" => 0, "retention_variable" => "Retained to Start of Year 4"],
                    ["years_from_retention_track" => 3, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 4"]
                ]],
                // 2 years between retention track and current year, should display year 1 and 2 variables (years from retention track = 1)
                [201516, 201617, [
                    ["years_from_retention_track" => 0, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 1"],
                    ["years_from_retention_track" => 1, "is_midyear_variable" => 0, "retention_variable" => "Retained to Start of Year 2"],
                    ["years_from_retention_track" => 1, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 2"]
                ]],
                // 1 year between retention track and current year, should display year 1 and 2 variables (years from retention track = 0)
                [201516, 201516, [
                    ["years_from_retention_track" => 0, "is_midyear_variable" => 1, "retention_variable" => "Retained to Midyear Year 1"]
                ]],
                //Invalid, Can't show retention Tracks in the future
                [201516, 201415, []]
            ]
        ]);
    }

    public function testGetAllVAriableNames()
    {

        $this->specify("Verify the functionality of the method getAllVariableNames", function ($expectedResults) {
            $results = $this->retentionCompletionVariableNameRepository->getAllVariableNames();
            verify($results)->equals($expectedResults);
        }, ["examples" =>
            [
                [[
                    "Completed Degree in 1 Year or Less",
                    "Completed Degree in 2 Years or Less",
                    "Completed Degree in 3 Years or Less",
                    "Completed Degree in 4 Years or Less",
                    "Completed Degree in 5 Years or Less",
                    "Completed Degree in 6 Years or Less",
                    "Retained to Midyear Year 1",
                    "Retained to Midyear Year 2",
                    "Retained to Midyear Year 3",
                    "Retained to Midyear Year 4",
                    "Retained to Start of Year 2",
                    "Retained to Start of Year 3",
                    "Retained to Start of Year 4"
                ]]
            ]
        ]);

    }

}