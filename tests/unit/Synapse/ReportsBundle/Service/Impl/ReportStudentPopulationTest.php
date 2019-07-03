<?php

use Synapse\ReportsBundle\Service\Impl\ReportsService;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SurveyBundle\Service\Impl\SurveyBlockService;

class ReportStudentPopulationTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    private $orgId = 1;
    private $personId = 1;

    public function testGetStudentCountBasedCriteria()
    {
        $this->specify("Test student population count based on search criteria and status as active", function ($status, $reportShortCode) {
            /**
             *  Pre-requisite mock objects for constructor
             */
            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array('getRepository'));
            $mockLogger = $this->getMock('Logger', array('debug', 'error', 'info'));
            $mockContainer = $this->getMock('Container', array('get'));
            $mockSurveyBlockService = $this->getMock('SurveyBlockService');
            $mockCampusConnService = $this->getMock('CampusConnectionService');
            $mockCampusResourceService = $this->getMock('CampusResourceService');
            $mockPdfReportService = $this->getMock('PdfReportsService');
            $mockActivityReportService = $this->getMock('ActivityReportService');
            $mockSurveySnapshotService = $this->getMock('SurveySnapshotService');
            $mockProfileSnapshotService = $this->getMock('ProfileSnapshotService');

            /**
             * Inititializing resque to be mocked
             */
            $mockResque = $this->getMockBuilder('BCC\ResqueBundle\Resque')
                ->disableOriginalConstructor()
                ->getMock(array(
                    'enqueue'
                ));

            /**
             * Inititializing ProfileService to be mocked
             */
            $mockProfileService = $this->getMock('ProfileService');

            /**
             * Inititializing OrgProfileService to be mocked
             */
            $mockOrgProfileService = $this->getMock('OrgProfileService');

            /**
             * Doctrine2 Mock Object
             */
            $mockDoctrine = $this->getMockBuilder('Codeception\Module\Doctrine2')
                ->disableOriginalConstructor()
                ->getMock();

            /**
             * Inititializing FactorReportService to be mocked
             */
            $mockFactorReportService = $this->getMock('FactorReportService');

            /**
             * Inititializing JMSSerializer to be mocked
             */
            $mockSerializer = $this->getMock('JMSSerializer', array(
                'serialize'
            ));

            /**
             * Inititializing GPAReportService to be mocked
             */
            $mockGpaReportService = $this->getMock('GPAReportService');

            /**
             * Inititializing ReportsDtoVerificationService to be mocked
             */
            $mockDtoVerificationService = $this->getMock('ReportsDtoVerificationService');

            $mockOrgRepository = $this->getMock("Organization", ["find"]);
            $mockOrgSearchRepository = $this->getMock("OrgSearch", ["getOrgSearch"]);
            $mockReportsRepository = $this->getMock("Reports", ["getRetentionTrackStudents"]);

            $mockOrgPersonStudentRetentionTrackingGroupRepository = $this->getMock('OrgPersonStudentRetentionTrackingGroupRepository',['getRetentionTrackingGroupStudents']);
            $mockOrgPersonStudentRetentionTrackingGroupRepository->expects($this->any())
                ->method('getRetentionTrackingGroupStudents')
                ->will($this->returnValue(array(1, 2, 4)));


            $mockRepositoryResolver->expects($this->any())
                ->method('getRepository')
                ->willReturnMap(
                    [
                        [
                            "SynapseCoreBundle:Organization",
                            $mockOrgRepository
                        ],
                        [
                            "SynapseSearchBundle:OrgSearch",
                            $mockOrgSearchRepository
                        ],
                        [
                            "SynapseReportsBundle:Reports",
                            $mockReportsRepository
                        ],
                        [
                            \Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY,
                            $mockOrgPersonStudentRetentionTrackingGroupRepository
                        ]
                    ]);

            /**
             * Organization Mock Object
             */
            $mockOrg = $this->getMock('Organization', array(
                'getId'
            ));

            $mockOrgRepository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($mockOrg));

            $mockOrg->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($this->orgId));

            /**
             *  Search Service Mock
             */
            $mockSearchService = $this->getMock("SearchService", ["getStudentListBasedCriteria", "prefetchSearchKeys"]);

            $mockContainer->expects($this->any())
                ->method('get')
                ->will($this->returnValue($mockSearchService));

            $mockSearchService->expects($this->once())
                ->method('getStudentListBasedCriteria')
                ->will($this->returnValue($this->getStudentBasedCriteriaQuery()));

            $mockSearchService->expects($this->once())
                ->method('prefetchSearchKeys')
                ->will($this->returnValue($this->getPrefetchKeys()));

            $mockReportsRepository->expects($this->any())
                ->method('getRetentionTrackStudents')
                ->will($this->returnValue(array(1, 2, 4)));

            $mockContainer->expects($this->any())
                ->method('get')
                ->willReturnMap(
                    [
                        [
                            \Synapse\SurveyBundle\Service\Impl\SurveyBlockService::SERVICE_KEY,
                            $mockSurveyBlockService
                        ],
                        [
                            \Synapse\CampusConnectionBundle\Service\Impl\CampusConnectionService::SERVICE_KEY,
                            $mockCampusConnService
                        ],
                        [
                            \Synapse\CampusResourceBundle\Service\Impl\CampusResourceService::SERVICE_KEY,
                            $mockCampusResourceService
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\PdfReportsService::SERVICE_KEY,
                            $mockPdfReportService
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\ActivityReportService::SERVICE_KEY,
                            $mockActivityReportService
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\SurveySnapshotService::SERVICE_KEY,
                            $mockSurveySnapshotService
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\ProfileSnapshotService::SERVICE_KEY,
                            $mockProfileSnapshotService
                        ],
                        [
                            \Synapse\CoreBundle\SynapseConstant::RESQUE_CLASS_KEY,
                            $mockResque
                        ],
                        [
                            \Synapse\CoreBundle\Service\Impl\ProfileService::SERVICE_KEY,
                            $mockProfileService
                        ],
                        [
                            \Synapse\CoreBundle\Service\Impl\OrgProfileService::SERVICE_KEY,
                            $mockOrgProfileService
                        ],
                        [
                            \Synapse\CoreBundle\SynapseConstant::DOCTRINE_CLASS_KEY,
                            $mockDoctrine
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\FactorReportService::SERVICE_KEY,
                            $mockFactorReportService
                        ],
                        [
                            \Synapse\CoreBundle\SynapseConstant::JMS_SERIALIZER_CLASS_KEY,
                            $mockSerializer
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\GPAReportService::SERVICE_KEY,
                            $mockGpaReportService
                        ],
                        [
                            \Synapse\ReportsBundle\Service\Impl\ReportsDtoVerificationService::SERVICE_KEY,
                            $mockDtoVerificationService
                        ]
                    ]);


            $reportsService = new ReportsService($mockRepositoryResolver, $mockLogger, $mockContainer);
            $studentCountResponse = $reportsService->getStudentCountBasedCriteria($this->createCustomSearchDto($status), $this->personId, $reportShortCode);

            $this->assertInternalType("array", $studentCountResponse);
            $this->assertEquals($this->orgId, $studentCountResponse['organization_id']);
            $this->assertEquals($this->personId, $studentCountResponse['person_id']);
        }, ['examples' => [
            ["0", "AU-R"],
            ["1", "PRR"],
            ["1", "CR"]
        ]]);
    }

    private function createCustomSearchDto()
    {
        $searchAttribute = [];
        $customSearchDto = new SaveSearchDto();
        $customSearchDto->setOrganizationId($this->orgId);
        $searchAttribute["risk_indicator_ids"] = "";
        $searchAttribute["intent_to_leave_ids"] = "";
        $searchAttribute["group_ids"] = "1";
        $searchAttribute["referral_status"] = "";
        $searchAttribute["contact_types"] = "";
        $searchAttribute["student_status"] = "1";
        $searchAttribute["academic_updates"] = array(
            "isBlankAcadUpdate" => false
        );

        $searchAttribute["retention_date"] = array(
            "academic_year_id" => 1
        );

        $searchAttribute["isps"] = [];

        $customSearchDto->setSearchAttributes($searchAttribute);
        return $customSearchDto;
    }

    private function getStudentBasedCriteriaQuery()
    {
        return "
                    EXISTS (

                    /* Students accessible to Faculty with ID 95 */

                        SELECT DISTINCT
                            merged.student_id
                        FROM
                            (
                               /* Students associated with Faculty via Groups */
                                SELECT
                                    S.person_id AS student_id,
                                    F.org_permissionset_id AS permissionset_id
                                FROM
                                    org_group_students AS S
                                    INNER JOIN org_group_faculty AS F
                                        ON F.org_group_id = S.org_group_id and F.deleted_at is null
                                WHERE
                                    S.organization_id = 2
                                    AND S.deleted_at is null
                                    AND F.person_id = 95
                                    AND F.deleted_at is null

                                UNION ALL

                                /* Students associated with Faculty via Courses */
                                SELECT
                                    S.person_id AS student_id,
                                    F.org_permissionset_id AS permissionset_id
                                FROM
                                    org_course_student AS S
                                    INNER JOIN org_courses AS C
                                        ON C.id = S.org_courses_id AND C.deleted_at is null
                                    INNER JOIN org_course_faculty AS F
                                        ON F.org_courses_id = S.org_courses_id AND F.deleted_at is null
                                    INNER JOIN org_academic_terms AS OAT
                                        ON OAT.id = C.org_academic_terms_id
                                            AND OAT.deleted_at is null
                                            AND DATE(now())
                                                between OAT.start_date
                                                    and OAT.end_date
                                WHERE
                                    S.organization_id = 2
                                    AND S.deleted_at is null
                                    AND F.organization_id = 2
                                    AND F.deleted_at is null
                                    AND F.person_id = 95
                            ) AS merged
                            INNER JOIN org_person_student AS S
                                ON S.person_id = merged.student_id
                                    AND S.deleted_at IS NULL
                                    AND S.organization_id = 2
                            INNER JOIN org_permissionset OPS
                                ON OPS.id = merged.permissionset_id AND OPS.deleted_at is null
                                     AND ( OPS.accesslevel_ind_agg = 1 )
                     AND ( S.status != 0 OR S.status IS NULL )
             /* Query restricting students access for faculty ends */
              WHERE student_id = p.id
            )
             AND  EXISTS (
             /* Filter to select student in specified groups */


                    /* Filter for selecting groups */

                        SELECT DISTINCT person_id
                        FROM
                            org_group_students S
                        WHERE
                            S.organization_id = 2
                            AND S.deleted_at is null
                            AND S.org_group_id IN (1)
                            AND S.org_group_id IN
                            (
                            SELECT org_group_id
                            FROM
                                org_group_faculty F
                            WHERE
                                F.organization_id = 2
                                AND F.person_id = 95
                                AND F.deleted_at is null
                            )
                     AND person_id = p.id
            )
        ";
    }

    private function getPrefetchKeys()
    {
        return array(
            "[ORG_ID]" => $this->orgId,
            "[EBI_METADATA_CLASSLEVEL_ID]" => 56,
            "[CLASS_LEVELS]" => array(
                "1st Year/Freshman",
                "Sophomore"
            ),
            "[CURRENT_ACADEMIC_YEAR]" => 1,
            "[CURRENT_ACADEMIC_TERM]" => 1
        );
    }

}