<?php

use Codeception\TestCase\Test;
use Synapse\SurveyBundle\Repository\IssueRepository;

class IssueRepositoryTest extends Test
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
     * @var \Synapse\SurveyBundle\Repository\IssueRepository
     */
    private $issueRepository;

    public function testGetIssuesList()
    {
        $this->specify("Verify the functionality of the method getIssuesList", function ($surveyId, $expectedResults) {
            $this->container = $this->getModule('Symfony2')->kernel->getContainer();
            $this->repositoryResolver = $this->container->get('repository_resolver');
            $this->issueRepository = $this->repositoryResolver->getRepository(IssueRepository::REPOSITORY_KEY);
            $result = $this->issueRepository->getIssuesList($surveyId);
            $this->assertEquals($result, $expectedResults);
        }, [
            'examples' => [
//                Example 1: Test to list issues when numeric value is given
                [
                    11,
                    array(
                        [
                            'id' => 1,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 3,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 4,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 5,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 6,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 7,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 9,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 10,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 11,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 12,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 13,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 14,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 15,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 16,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 17,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 19,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 20,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 21,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 22,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 23,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 24,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 25,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 26,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 27,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ]
                    )
                ],
//                Example 2: Test to list issues when non numeric value is given
                [
                    'all',
                    array(
                        [
                            'id' => 1,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 3,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 4,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 5,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 6,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 7,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 9,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 10,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 11,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 12,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 13,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 14,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 15,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 16,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 17,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 19,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 20,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 21,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 22,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 23,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 24,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 25,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 26,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 27,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 28,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 29,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 30,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 31,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 32,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 33,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 34,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 35,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 36,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 37,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 38,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 39,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 40,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 41,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 42,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 43,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 44,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 45,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 46,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 47,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 48,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 49,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 50,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 51,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 52,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 53,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 54,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 55,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 56,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 57,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 58,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 59,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 60,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 61,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 62,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 63,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 64,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 65,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 66,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 67,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 68,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 69,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 70,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 71,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 72,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 73,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 74,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 75,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 76,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 77,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 78,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 79,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 80,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 81,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 82,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 83,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 84,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 85,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 86,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 87,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 88,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 89,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 90,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 91,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 92,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 93,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 94,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 95,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 96,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 97,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 98,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 99,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 100,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 101,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 102,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 103,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 104,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 105,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 106,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 107,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 108,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 109,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 110,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 111,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 112,
                            'issue_name' => 'Struggling in at least 2 courses',
                            'issue_icon' => 'large-report-icon-courses.png'
                        ],
                        [
                            'id' => 113,
                            'issue_name' => 'Missed 2 or more classes',
                            'issue_icon' => 'large-report-icon-missedclasses.png'
                        ],
                        [
                            'id' => 114,
                            'issue_name' => 'Not committed to continuing',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 115,
                            'issue_name' => 'Plan to study 5 hours or fewer a week',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 116,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 117,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 118,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 119,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 120,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 121,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 122,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 123,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 124,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 125,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 126,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 127,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 128,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 129,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 130,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 131,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 132,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 133,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 134,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 135,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 136,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 137,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 138,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 139,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 140,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 141,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 142,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 143,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 144,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 145,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 146,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 147,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 148,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 149,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 150,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 151,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 152,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 153,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 154,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 155,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 156,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 157,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 158,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 159,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 160,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 161,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 162,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 163,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 164,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 165,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 166,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 167,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 168,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 169,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 170,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 171,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 172,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 173,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 174,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 175,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 176,
                            'issue_name' => 'Low communication skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 177,
                            'issue_name' => 'Low analytical skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 178,
                            'issue_name' => 'Low time management',
                            'issue_icon' => 'large-report-icon-study.png'
                        ],
                        [
                            'id' => 179,
                            'issue_name' => 'Not confident about finances',
                            'issue_icon' => 'large-report-icon-finances.png'
                        ],
                        [
                            'id' => 180,
                            'issue_name' => 'Low basic academic skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 181,
                            'issue_name' => 'Low advanced academic behaviors',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 182,
                            'issue_name' => 'Low academic self-efficacy',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 183,
                            'issue_name' => 'Low academic resiliency',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 184,
                            'issue_name' => 'Low peer connections',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 185,
                            'issue_name' => 'Homesick (separation)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 186,
                            'issue_name' => 'Homesick (distressed)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 187,
                            'issue_name' => 'Low academic integration',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 188,
                            'issue_name' => 'Low social integration',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 189,
                            'issue_name' => 'Low satisfaction with the institution',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 190,
                            'issue_name' => 'Low social aspects (on-campus living)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 191,
                            'issue_name' => 'Low living environment (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 192,
                            'issue_name' => 'Low roommate relationships (on-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 193,
                            'issue_name' => 'Low living environment (off-campus)',
                            'issue_icon' => 'large-report-icon-homesick.png'
                        ],
                        [
                            'id' => 194,
                            'issue_name' => 'Test Anxiety',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ],
                        [
                            'id' => 195,
                            'issue_name' => 'Low advanced study skills',
                            'issue_icon' => 'large-report-icon-academic.png'
                        ]

                    )
                ]
            ]
        ]);
    }
}