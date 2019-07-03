<?php
require_once 'SynapseTestHelper.php';
class TeamCest extends SynapseTestHelper
{

	private $token;

	private $organization = 1;

    private $invalidOrganization = -200;

	private $langId = 1;


	private $person1 = 2;
	private $person2 = 3;
	private $invalidPerson= -1;

	private $teamId = 1;
	private $invalidTeamId = -1;


	public function _before(ApiTester $I)
	{
		$this->token = $this->authenticate($I);
	}


	public function testCreateNewTeam(ApiTester $I)
	{
		$I->wantTo('Create a Team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
        		"organization"=> $this->organization,
        		"team_name"=> uniqid("Team_",true),
        		"staff" => [
				 [
                	"person_id" => $this->person1,
                	"action" => "add",
                	"is_leader" => "0"
            	 ],
            	 [
                	"person_id"=> $this->person2,
               	    "action"=>"add",
                    "is_leader"=> "1"
           		 ]
        	]]);
        $I->seeResponseContains('team');
        $I->seeResponseContains('team_name');
        $I->seeResponseContains('id');
        $I->seeResponseContains('organization');
        $I->seeResponseContains('staff');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}


	public function testCreateNewTeamInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Create a Team with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->organization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => $this->person1,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);

		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}


	public function testCreateNewTeamInvalidOrg(ApiTester $I)
	{
		$I->wantTo('Create a Team with invalid organization by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->invalidOrganization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => $this->person1,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);
		$I->seeResponseContains('Organization Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}


	public function testCreateNewTeamInvalidTeamMembers(ApiTester $I)
	{
		$I->wantTo('Create a Team with invalid team members by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->organization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => $this->invalidPerson,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> 3,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);
		$I->seeResponseContains('Person Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}



    public function testCreateNewTeamWithOutAuthenticate(ApiTester $I)
    {
        $I->wantTo('Create a New Team Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }

    public function testCreateNewTeamWithOutOrganization(ApiTester $I)
    {
        $I->wantTo('Create a New Team Without Organization by API ');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> null,
            "team_name"=>uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();

    }


	public function testUpdateTeams(ApiTester $I){
		$I->wantTo('Update Team and team members by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->organization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => $this->person1,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);
		$teams = json_decode($I->grabResponse());

		$I-> sendPUT('teams/update',[
				"lang_id"=> $this->langId,
        		"organization"=> $this->organization,
  				"team_id"=> $teams->data->team->id,
       		 	"team_name"=> $teams->data->team->team_name,
        		"staff"=> [
           		 [
                	"person_id" => $this->person1,
               	    "action" => "delete",
                	"is_leader" => "0"
            	 ],
	             [
                	"person_id"=> $this->person2,
                	"action"=>"update",
                	"is_leader"=> "1"
            	 ]
        ]]);

        $I->seeResponseContains('staff');
        $I->seeResponseContains('team');

		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}



	public function testUpdateTeamsInvalidAuthentication(ApiTester $I){
		$I->wantTo('Update Team and team members with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I-> sendPUT('teams/update',[
				"lang_id"=> $this->langId,
				"organization"=> $this->organization,
				"team_id"=> $this->teamId,
				"team_name"=> "Synapse",
				"staff"=> [
				[
				"person_id" => $this->person1,
				"action" => "delete",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"update",
				"is_leader"=> "1"
				]
				]]);

		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}



	public function testUpdateTeamsInvalidOrg(ApiTester $I)
	{
		$I->wantTo('Update Team and team members with invalid organization by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I-> sendPUT('teams/update',[
				"lang_id"=> $this->langId,
				"organization"=> $this->invalidOrganization,
				"team_id"=> $this->teamId,
				"team_name"=> "Physics",
				"staff"=> [
				[
				"person_id" => $this->person1,
				"action" => "delete",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"update",
				"is_leader"=> "1"
				]
				]]);
		$I->seeResponseContains('Organization Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}

	public function testUpdateTeamsInvalidTeam(ApiTester $I)
	{
		$I->wantTo('Update Team and team members with invalid team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I-> sendPUT('teams/update',[
				"lang_id"=> $this->langId,
				"organization"=> $this->organization,
				"team_id"=> $this->invalidTeamId,
				"team_name"=> "Physics",
				"staff"=> [
				[
				"person_id" => $this->person1,
				"action" => "delete",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"update",
				"is_leader"=> "1"
				]
				]]);
		$I->seeResponseContains('Team Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}


	public function testUpdateTeamsInvalidTeamMembers(ApiTester $I)
	{
		$I->wantTo('Update Team and team members with invalid team members by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I-> sendPUT('teams/update',[
				"lang_id"=> $this->langId,
				"organization"=> $this->organization,
				"team_id"=> $this->teamId,
				"team_name"=> "Physics",
				"staff"=> [
				[
				"person_id" => $this->invalidPerson,
				"action" => "delete",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->invalidPerson,
				"action"=>"update",
				"is_leader"=> "1"
				]
				]]);
		$I->seeResponseContains('Person Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}

    public function testUpdateTeamsWithOutAuthenticate(ApiTester $I)
    {
        $I->wantTo('Update Teams Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 1,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
          $I-> sendPUT('teams/update',[
            "lang_id"=> $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff"=> [
                [
                    "person_id" => 2,
                    "action" => "delete",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"update",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }

    public function testUpdateTeamsWithOutOrganization(ApiTester $I)
    {
        $I->wantTo('Update Teams Without Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 1,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I-> sendPUT('teams/update',[
            "lang_id"=> $this->langId,
            "organization"=> null,
            "team_name"=> uniqid("Team_",true),
            "staff"=> [
                [
                    "person_id" => 2,
                    "action" => "delete",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"update",
                    "is_leader"=> "1"
                ]
            ]]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();

    }


	public function testGetTeams(ApiTester $I)
	{
		$I->wantTo('Get Teams for organization by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
		$I->sendGET('teams/list/'.$this->organization);
        $I->seeResponseContains('team_id');
        $I->seeResponseContains('team_no_leaders');
        $I->seeResponseContains('team_no_members');
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
	}



    public function testGetTeamsWithoutAuthenticate(ApiTester $I)
    {
        $I->wantTo('Get Teams Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->sendGET('teams/list/'.$this->organization);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function testGetTeamsInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Get Teams with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        /*$I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);*/
        $I->sendGET('teams/list/'.$this->invalidOrganization);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }



	public function testGetTeamsInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Teams for organization with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('teams/list/'.$this->organization);

		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}



	public function testGetTeamMembers(ApiTester $I)
	{
		$I->wantTo('Get Team Members for a team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->organization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => $this->person1,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> $this->person2,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);
		$teams = json_decode($I->grabResponse());
		$I->sendGET('teams/members/'.$teams->data->team->id);
		$I->seeResponseContainsJson(array('team_id' => $teams->data->team->id));
		$I->seeResponseContains('team_name');
		$I->seeResponseContains('modified_at');
		$I->seeResponseContains('staff');
		$I->seeResponseContains('person_id');
		$I->seeResponseContains('is_leader');
		$I->seeResponseCodeIs(200);
        $I->seeResponseContains('team');
        $I->seeResponseContains('team_name');
        $I->seeResponseContains('staff');
		$I->seeResponseIsJson();

	}


	public function testGetTeamMembersInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Get Team Members for a team with Invalid Authentication by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('teams/members/'.$this->teamId);

		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}

	public function testGetTeamMembersInvalid(ApiTester $I)
	{
		$I->wantTo('Get Team Members for a invalid team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendGET('teams/members/'.$this->invalidTeamId);
		$I->seeResponseContains('Team Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}

    public function testGetTeamMembersWithoutAuthenticate(ApiTester $I)
    {
        $I->wantTo('Get Team Members Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->sendGET('teams/members/'.$this->teamId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
    }

    public function  testGetTeamMembersInvalidOrganization(ApiTester $I)
    {
        $I->wantTo('Get Teams Members with Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendPOST('teams/new',[
            "lang_id" => $this->langId,
            "organization"=> $this->organization,
            "team_name"=> uniqid("Team_",true),
            "staff" => [
                [
                    "person_id" => 2,
                    "action" => "add",
                    "is_leader" => "0"
                ],
                [
                    "person_id"=> 3,
                    "action"=>"add",
                    "is_leader"=> "1"
                ]
            ]]);
        $I->sendGET('teams/members/'.$this->invalidOrganization);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testDeleteTeam(ApiTester $I)
	{
		$I->wantTo('Delete Team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendPOST('teams/new',[
				"lang_id" => $this->langId,
				"organization"=> $this->organization,
				"team_name"=> uniqid("Team_",true),
				"staff" => [
				[
				"person_id" => 2,
				"action" => "add",
				"is_leader" => "0"
				],
				[
				"person_id"=> 3,
				"action"=>"add",
				"is_leader"=> "1"
				]
				]]);
		$teams = json_decode($I->grabResponse());
		$I->sendDELETE('teams/delete/'.$teams->data->team->id);
		$I->seeResponseContainsJson(array('id' => $teams->data->team->id));
		$I->seeResponseContains('deleted_at');
		$I->seeResponseContains('team_name');
		$I->seeResponseContains('organization');
		$I->seeResponseCodeIs(201);
        $I->seeResponseContains('team');
        $I->seeResponseContains('organization');
        $I->seeResponseContains('team_name');
        $I->seeResponseContains('id');
		$I->seeResponseIsJson();
	}



	public function testDeleteTeamInvalidAuthentication(ApiTester $I)
	{
		$I->wantTo('Delete Team by with Invalid Authentication API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendDELETE('teams/delete/'.$this->teamId);

		$I->seeResponseCodeIs(403);
		$I->seeResponseIsJson();
	}

	public function testDeleteTeamInvalid(ApiTester $I)
	{
		$I->wantTo('Delete Invalid Team by API');
		$I->haveHttpHeader('Content-Type', 'application/json');
		$I->amBearerAuthenticated( $this->token);
		$I->haveHttpHeader('Accept', 'application/json');
		$I->sendDELETE('teams/delete/'.$this->invalidTeamId);
		$I->seeResponseContains('Team Not Found');
		$I->seeResponseCodeIs(400);
		$I->seeResponseIsJson();
	}


    public function testGetOrganizationTeamsWithoutAuthentication(ApiTester $I)
    {
        $I->wantTo('Get Organization Teams Without Authentication by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendGET('teams/orgId/'.$this->organization.'/userId/'.$this->teamId);
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();

    }


    public function testGetOrganizationTeamsInvalidOrganization(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Organization Teams With Invalid Organization by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('teams/orgId/'.$this->invalidOrganization.'/userId/'.$this->teamId);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }

    public function testGetOrganizationTeamsInvalidUser(ApiTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('Get Organization Teams With Invalid User by API');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('teams/orgId/'.$this->organization.'/userId/'.$this->invalidOrganization);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
    }
    
    public function testGetTeamActivitiesDetailsToCSV(ApiTester $I, $scenario)
    {
        $I->wantTo('Get Team Activities Details To CSV');
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->amBearerAuthenticated( $this->token);
        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('teams/activitiesdetail?team-id=1&team-member-id=1,2&activity_type=all&filter=week&start-date=&end-date=&output-format=csv');
        $res = json_decode($I->grabResponse());
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

}
