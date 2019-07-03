<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class SearchCest extends SynapseRestfulTestBase
{

    // In all of these tests, Minerva McGonagall is sharing a search which includes all students in Gryffindor.

    private $staffM = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
        'id' => 99440,
        'orgId' => 542,
        'langId' => 1
    ];

    // Permission set Hogwarts_FullAccess.  He has access to some of the students in Gryffindor.
    private $staffS = [
        'email' => 'severus.snape@mailinator.com',
        'password' => 'password1!',
        'id' => 99447,
        'orgId' => 542,
        'langId' => 1
    ];

    // Permission set Hogwarts_AggregateOnlyAccess.  Some of the Gryffindor students are in a group he's in, but he shouldn't be able to see them.
    private $staffA = [
        'email' => 'argus.filch@mailinator.com',
        'password' => 'password1!',
        'id' => 99436,
        'orgId' => 542,
        'langId' => 1
    ];

    // Permission set Hogwarts_MinimumAccess.  He has access to some of the students in Gryffindor.
    private $staffR = [
        'email' => 'remus.lupin@mailinator.com',
        'password' => 'password1!',
        'id' => 99710,
        'orgId' => 542,
        'langId' => 1
    ];

    // Data for sharing a search with Percy Weasley
    private $shareP = [
        'organization_id' => 542,
        'saved_search_id' => -1,
        'saved_search_name' => 'G',
        'shared_by_person_id' => 99440,
        'shared_with_person_ids' => '99442'
    ];

    // Data for sharing a search with "Bad Guy" from another university.
    // I'm unsure which organization_id should be used, but neither should be a valid share.
    private $invalidShare = [
        'organization_id' => 542,
        'saved_search_id' => -1,
        'saved_search_name' => 'Gr',
        'shared_by_person_id' => 99440,
        'shared_with_person_ids' => '99705'
    ];

    private $invalidShare2 = [
        'organization_id' => 543,
        'saved_search_id' => -1,
        'saved_search_name' => 'Gry',
        'shared_by_person_id' => 99440,
        'shared_with_person_ids' => '99705'
    ];

    // Sending this in a POST returns the Gryffindor students.
    private $searchM = [
        'organization_id' => '542',
        'date_created' => '2015-07-15T13:36:47-0500',
        'person_id' => '99440',
        'saved_search_id' => 1,
        'saved_search_name' => 'Gryffindor',
        'search_attributes' => [
            'group_ids' => '1266',
            'risk_indicator_ids' => '',
            'intent_to_leave_ids' => '',
            'referral_status' => '',
            'contact_types' => '',
            'courses' => [
                'department_id' => '',
                'subject_id' => '',
                'course_ids' => '',
                'section_ids' => ''
            ],
            'isps' => [],
            'datablocks' => [[
                'profile_block_id' => '',
                'profile_items' => ''
            ]]
        ],
        'totalSearchAttr' => 1
    ];

    // When Severus clicks on the name of the shared search, this is sent in a POST to retrieve the list.
    private $postToViewSharedSearchSeverus = [
        'organization_id' => '542',
        'date_created' => '2015-07-15T13:36:47-0500',
        'person_id' => '99447',
        'saved_search_id' => 2,
        'saved_search_name' => 'Gryffindor',
        'search_attributes' => [
            'group_ids' => '1266',
            'risk_indicator_ids' => '',
            'intent_to_leave_ids' => '',
            'referral_status' => '',
            'contact_types' => '',
            'courses' => [
                'department_id' => '',
                'subject_id' => '',
                'course_ids' => '',
                'section_ids' => ''
            ],
            'isps' => [],
            'datablocks' => [[
                'profile_block_id' => '',
                'profile_items' => ''
            ]]
        ],
        'totalSearchAttr' => 1
    ];

    private $postToViewSharedSearchArgus = [
        'organization_id' => '542',
        'person_id' => '99436',
        'saved_search_id' => 4,
        'saved_search_name' => 'Gryffindor1',
        'search_attributes' => [
            'group_ids' => '1266',
            'risk_indicator_ids' => '',
            'intent_to_leave_ids' => '',
            'referral_status' => '',
            'contact_types' => '',
            'courses' => [
                'department_id' => '',
                'subject_id' => '',
                'course_ids' => '',
                'section_ids' => ''
            ],
            'isps' => [],
            'datablocks' => [[
                'profile_block_id' => '',
                'profile_items' => ''
            ]]
        ],
        'totalSearchAttr' => 1
    ];

    private $postToViewSharedSearchRemus = [
        'organization_id' => '542',
        'person_id' => '99710',
        'saved_search_id' => 6,
        'saved_search_name' => 'Gryffindor2',
        'search_attributes' => [
            'group_ids' => '1266',
            'risk_indicator_ids' => '',
            'intent_to_leave_ids' => '',
            'referral_status' => '',
            'contact_types' => '',
            'courses' => [
                'department_id' => '',
                'subject_id' => '',
                'course_ids' => '',
                'section_ids' => ''
            ],
            'isps' => [],
            'datablocks' => [[
                'profile_block_id' => '',
                'profile_items' => ''
            ]]
        ],
        'totalSearchAttr' => 1
    ];

    private $gryffindorStudents = [
        ['student_id' => 99410, 'student_first_name' => 'Alicia ', 'student_last_name' => 'Spinnet'],
        ['student_id' => 99411, 'student_first_name' => 'Angelina ', 'student_last_name' => 'Johnson'],
        ['student_id' => 99415, 'student_first_name' => 'Colin ', 'student_last_name' => 'Creevey'],
        ['student_id' => 99417, 'student_first_name' => 'Fred ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99418, 'student_first_name' => 'George ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99419, 'student_first_name' => 'Ginny ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99422, 'student_first_name' => 'Harry ', 'student_last_name' => 'Potter'],
        ['student_id' => 99423, 'student_first_name' => 'Hermione ', 'student_last_name' => 'Granger'],
        ['student_id' => 99425, 'student_first_name' => 'Katie ', 'student_last_name' => 'Bell'],
        ['student_id' => 99429, 'student_first_name' => 'Neville ', 'student_last_name' => 'Longbottom'],
        ['student_id' => 99432, 'student_first_name' => 'Ron ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99433, 'student_first_name' => 'Seamus ', 'student_last_name' => 'Finnigan']
    ];

    // Students in Gryffindor that Severus should have access to
    private $accessibleStudents = [
        ['student_id' => 99411, 'student_first_name' => 'Angelina ', 'student_last_name' => 'Johnson']
    ];

    // Students in Gryffindor that Severus should not have access to
    private $inaccessibleStudents = [
        ['student_id' => 99410, 'student_first_name' => 'Alicia ', 'student_last_name' => 'Spinnet'],
        ['student_id' => 99415, 'student_first_name' => 'Colin ', 'student_last_name' => 'Creevey'],
        ['student_id' => 99417, 'student_first_name' => 'Fred ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99418, 'student_first_name' => 'George ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99419, 'student_first_name' => 'Ginny ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99422, 'student_first_name' => 'Harry ', 'student_last_name' => 'Potter'],
        ['student_id' => 99423, 'student_first_name' => 'Hermione ', 'student_last_name' => 'Granger'],
        ['student_id' => 99425, 'student_first_name' => 'Katie ', 'student_last_name' => 'Bell'],
        ['student_id' => 99429, 'student_first_name' => 'Neville ', 'student_last_name' => 'Longbottom'],
        ['student_id' => 99432, 'student_first_name' => 'Ron ', 'student_last_name' => 'Weasley'],
        ['student_id' => 99433, 'student_first_name' => 'Seamus ', 'student_last_name' => 'Finnigan']
    ];

    private $nonGryffindorStudents = [
        ['student_id' => 99412, 'student_first_name' => 'Arian ', 'student_last_name' => 'Pucey'],
        ['student_id' => 99413, 'student_first_name' => 'Cedric ', 'student_last_name' => 'Diggory'],
        ['student_id' => 99414, 'student_first_name' => 'Cho ', 'student_last_name' => 'Chang'],
        ['student_id' => 99416, 'student_first_name' => 'Draco ', 'student_last_name' => 'Malfoy'],
        ['student_id' => 99420, 'student_first_name' => 'Gregory ', 'student_last_name' => 'Goyle'],
        ['student_id' => 99421, 'student_first_name' => 'Hannah ', 'student_last_name' => 'Abbott'],
        ['student_id' => 99424, 'student_first_name' => 'Justin ', 'student_last_name' => 'Finch-Fletchley'],
        ['student_id' => 99426, 'student_first_name' => 'Luna ', 'student_last_name' => 'Lovegood'],
        ['student_id' => 99427, 'student_first_name' => 'Miles ', 'student_last_name' => 'Bletchley'],
        ['student_id' => 99428, 'student_first_name' => 'Millicent ', 'student_last_name' => 'Bulstrode'],
        ['student_id' => 99430, 'student_first_name' => 'Padma ', 'student_last_name' => 'Patil'],
        ['student_id' => 99431, 'student_first_name' => 'Pansy ', 'student_last_name' => 'Parkinson'],
        ['student_id' => 99434, 'student_first_name' => 'Vincent ', 'student_last_name' => 'Crabbe']
    ];



    public function testShareSearch(ApiAuthTester $I)
    {
        $I->wantTo('Share a search with another faculty within my organization.');
        $this->_postAPITestRunner($I, $this->staffM, 'sharedsearches', $this->shareP, 201, [$this->shareP]);
    }

    public function testInvalidShareSearch(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Share a search with someone from another organization.');
        $this->_postAPITestRunner($I, $this->staffM, 'sharedsearches', $this->invalidShare, 403, []);
    }

    // A nearly identical test to the previous one, as neither choice of organization_id should succeed.
    public function testInvalidShareSearch2(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Share a search with someone from another organization.');
        $this->_postAPITestRunner($I, $this->staffM, 'sharedsearches', $this->invalidShare2, 403, []);
    }

    // Make sure the list is as expected before checking what the recipients can see.
    public function testSharerViewSharedSearch(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify students in shared search before sharing.');
        $this->_postAPITestRunner($I, $this->staffM, 'search', $this->searchM, 200, $this->gryffindorStudents);
    }

    public function testRecipientViewSharedSearchAccessibleStudents(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Verify accessible students all show up in the shared search.');
        $this->_postAPITestRunner($I, $this->staffS, 'search', $this->postToViewSharedSearchSeverus, 200, $this->accessibleStudents);
    }

    public function testRecipientViewSharedSearchInaccessibleStudents(ApiAuthTester $I, $scenario)
    {
        $I->wantTo("Verify students that recipient has no connection to don't show up in the shared search.");
        $this->_postAPITestRunner($I, $this->staffS, 'search', $this->postToViewSharedSearchSeverus, 200, []);

        foreach($this->inaccessibleStudents as $student) {
            $I->dontSeeResponseContainsJson($student);
        }
    }

    public function testRecipientViewSharedSearchStudentsNotInSearch(ApiAuthTester $I, $scenario)
    {
        $I->wantTo("Verify students that weren't in original search don't show up in the shared search.");
        $this->_postAPITestRunner($I, $this->staffS, 'search', $this->postToViewSharedSearchSeverus, 200, []);

        foreach($this->nonGryffindorStudents as $student) {
            $I->dontSeeResponseContainsJson($student);
        }
    }

    // This test should be updated once we have risk indicator and intent to leave in our data.
    public function testSharedSearchIncludesRiskIndicatorAndIntentToLeave(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Verify a staff member with a full access permission set can see risk indicator and intent to leave in a shared search.');
        $riskIndicatorAndIntentToLeave = [
            'student_id' => 99411,
            'student_risk_status' => '',
            'student_intent_to_leave' => ''
        ];
        $this->_postAPITestRunner($I, $this->staffS, 'search', $this->postToViewSharedSearchSeverus, 200, [$riskIndicatorAndIntentToLeave]);
    }

    public function testAggOnlyViewSharedSearch(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo("Verify no students show up in a search shared with a staff member with an aggregate only permission set.");
        $this->_postAPITestRunner($I, $this->staffA, 'search', $this->postToViewSharedSearchArgus, 200, [['data' => []]]);
    }

    public function testMinAccessViewSharedSearch(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo("Verify a staff member with minimum access permission set can see a student but can't see the student's referral.");
        $missingReferral = [
            'student_id' => 99429,      // Neville has a referral in our data.
            'last_activity' => ''
        ];
        $this->_postAPITestRunner($I, $this->staffR, 'search', $this->postToViewSharedSearchRemus, 200, [$missingReferral]);
    }

    // This test assumes these parameters wouldn't show up in the JSON at all.  It could also be valid for them to be empty strings.
    public function testMinAccessRiskIndicatorAndIntentToLeave(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('Verify a staff member with a minimum access permission set cannot see risk indicator and intent to leave in a shared search.');
        $this->_postAPITestRunner($I, $this->staffR, 'search', $this->postToViewSharedSearchRemus, 200, []);
        $I->dontSeeResponseContains('student_risk_status');
        $I->dontSeeResponseContains('student_intent_to_leave');
    }

    public function testViewSharedSearchIntendedForSomeoneElse(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('Verify a staff member cannot access a shared search that was only shared with someone else.');
        $this->_postAPITestRunner($I, $this->staffS, 'search', $this->postToViewSharedSearchRemus, 403, [['data' => []]]);
    }

    public function databaseReload()
    {
        // Cleaning up data at end of file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }


}
