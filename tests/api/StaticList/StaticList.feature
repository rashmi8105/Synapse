Feature: StaticList

Background:
Try to login through Api
Given I am on skyfactor login page
When I get the access Token

Scenario: Create Static List
Given I am on Static Page
When I create a Static List
Then I see static List in the list
