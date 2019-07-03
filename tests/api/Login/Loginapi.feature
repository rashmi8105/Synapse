Feature: Loginapi

Scenario: Try to login through Api
Given I am on skyfactor login page
When I get the access Token
Then I am able to see the content
