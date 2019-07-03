<?php

namespace Synapse\ReportsBundle\Service\Impl;

use Codeception\Specify;
use Codeception\Test\Unit;

class ActivityReportServiceTest extends Unit
{
    use Specify;

    public function testReplacePlaceHoldersInQuery()
    {
        $this->specify("test replacePlaceHoldersInQuery", function($expectedResults, $query, $args){

            $mockContainer = $this->getMock('Container', array('get'));
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error'));
            $mockCache = $this->getMock('cache', array('run'));
            $mockResque = $this->getMock('resque', array('enqueue'));

            $activityReportService = new ActivityReportService($mockRepositoryResolver, $mockLogger, $mockContainer, $mockCache, $mockResque);
            $functionResults = $activityReportService->replacePlaceHoldersInQuery($query, $args);
            $this->assertEquals($expectedResults, $functionResults);

        }, [
            'examples' => [
                // Single query with single replaceable string
                [
                    "SELECT * FROM synapse.person WHERE organization_id = 1",
                    "SELECT * FROM synapse.person WHERE organization_id = {org_id}",
                    [
                        'orgId' => 1,
                        'start_date' => null,
                        'end_date' => null,
                        'reporting_on_student_ids' => null,
                        'reporting_on_faculty_ids' => null
                    ]
                ],
                // Single query with multiple replaceable strings
                [
                    "SELECT * FROM synapse.person WHERE organization_id = 1 AND start_date = str_to_date( \"2016-01-01 00:00:00\", \"%Y-%m-%d\")",
                    "SELECT * FROM synapse.person WHERE organization_id = {org_id} AND start_date = {reporting_start_date}",
                    [
                        'orgId' => 1,
                        'start_date' => '2016-01-01 00:00:00',
                        'end_date' => null,
                        'reporting_on_student_ids' => null,
                        'reporting_on_faculty_ids' => null
                    ]
                ],
                // Multiple queries with single replaceable value
                [
                    "SELECT * FROM synapse.person WHERE organization_id = 1  SELECT * FROM synapse.person WHERE organization_id = 1",
                    "SELECT * FROM synapse.person WHERE organization_id = {org_id} ##SELECT * FROM synapse.person WHERE organization_id = {org_id}",
                    [
                        'orgId' => 1,
                        'start_date' => '2016-01-01 00:00:00',
                        'end_date' => null,
                        'reporting_on_student_ids' => null,
                        'reporting_on_faculty_ids' => null
                    ]

                ],
                // Multiple queries with multiple replaceable values
                [
                    "SELECT * FROM synapse.person WHERE organization_id = 1 AND start_date = str_to_date( \"2016-01-01 00:00:00\", \"%Y-%m-%d\")  SELECT * FROM synapse.person WHERE organization_id = 1 AND start_date = str_to_date( \"2016-01-01 00:00:00\", \"%Y-%m-%d\")",
                    "SELECT * FROM synapse.person WHERE organization_id = {org_id} AND start_date = {reporting_start_date} ##SELECT * FROM synapse.person WHERE organization_id = {org_id} AND start_date = {reporting_start_date}",
                    [
                        'orgId' => 1,
                        'start_date' => '2016-01-01 00:00:00',
                        'end_date' => null,
                        'reporting_on_student_ids' => null,
                        'reporting_on_faculty_ids' => null
                    ]

                ],
                // empty query
                [
                    "",
                    "",
                    [
                        'orgId' => 1,
                        'start_date' => '2016-01-01 00:00:00',
                        'end_date' => null,
                        'reporting_on_student_ids' => null,
                        'reporting_on_faculty_ids' => null
                    ]
                ],
                // empty args has a fatal error, but since this function will eventually be removed, further refactoring was not done. 
            ]
        ]);
    }

}