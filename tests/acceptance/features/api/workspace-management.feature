Feature: As an account or team owner
  I want to be able to create, update and delete Workspaces
  Workspaces will help me logically group Assets so that I can report on my assets
  (Vulnerabilities, Exploits, Open Ports, etc.) and easily mange "triggers / events"

  I want to be able to add custom "tags" to Workspaces for reporting and grouping.

  As an administrator
  I want the ability to change the default / given name of "Workspaces" to anything
  So that I can manage and brand this accordingly to meet my requirements.

  Background:
    Given the following existing users:
      | id | name           | email                      | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 1  | John Smith     | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 2  | Greg Symons    | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
      | 3  | Another Person | another@dispostable.com    | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | AUS          | 08134582354 | NULL                  | 3               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing teams:
      | id | owner_id | name       | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
      | 1  | 1        | Johns Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
    And the following existing Projects:
      | id | name              | user_id | created_at          | updated_at          |
      | 1  | John's Project    | 1       | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Project | 2       | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Project   | 3       | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
    And the following existing Workspaces:
      | id | name                | user_id  | project_id | created_at          | updated_at          |
      | 1  | John's Workspace    | 1        | 1          | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Workspace | 2        | 2          | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Workspace   | 3        | 3          | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Create a new Workspace on my account
    Given that I want to make a new "Workspace"
    And that its "name" is "My New Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My New Workspace"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "1"
    And the response has a "project_id" property
    And the type of the "project_id" property is integer
    And the "project_id" property equals "1"

  Scenario: Create a new Workspace on someone else's account where I have write access
    Given that I want to make a new "Workspace"
    And that its "name" is "My New Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My New Workspace"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "project_id" property
    And the type of the "project_id" property is integer
    And the "project_id" property equals "2"

  Scenario: I attempt to create a Workspace on someone else's account where I don't have write access
    Given that I want to make a new "Workspace"
    And that its "name" is "My New Workspace"
    When I request "/api/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to create Workspaces in that Project."

  Scenario: I attempt to create a Workspace on non-existent Project
    Given that I want to make a new "Workspace"
    And that its "name" is "My New Workspace"
    When I request "/api/workspace/10"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: Delete a Workspace from my account
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "false"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a workspace will delete all the data related to that workspace. This is not reversable. Please confirm."

  Scenario: Delete and confirm deletion of a Workspace from my account
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "false"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a workspace will delete all the data related to that workspace. This is not reversable. Please confirm."
    When I request "/api/workspace/1/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"

  Scenario: Delete a Workspace on someone else's account where I have write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "false"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a workspace will delete all the data related to that workspace. This is not reversable. Please confirm."

  Scenario: Delete and confirm deletion of a Workspace on someone else's account where I have Workspace write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "false"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a workspace will delete all the data related to that workspace. This is not reversable. Please confirm."
    When I request "/api/workspace/2/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"

  Scenario: I attempt to delete a Workspace on someone else's account where I don't have Workspace write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to delete Workspaces from that Project."

  Scenario: I attempt to delete a non-existent Workspace
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we that Workspace does not exist."

  Scenario: Edit the name of one of my workspaces
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Workspace"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "1"
    And the response has a "project_id" property
    And the type of the "project_id" property is integer
    And the "project_id" property equals "1"

  Scenario: Edit the name of someone else's Workspace where I have write permission
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Workspace"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "project_id" property
    And the type of the "project_id" property is integer
    And the "project_id" property equals "2"

  Scenario: I attempt to edit the name of someone else's Workspace where I don't have read/write permission
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to make changes to that Workspace."

  Scenario: I attempt to edit the name of a workspace, but I give a workspace ID that does not exist
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."