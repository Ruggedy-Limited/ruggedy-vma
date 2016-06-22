Feature: As an account or team owner
  I want to be able to create, update and delete Project Spaces
  Project spaces will help me group logical functions such as Workspaces under a common group

  I want to be able to add custom "tags" to Projects for reporting and grouping.

  As an administrator
  I want the ability to change the default / given name of "Projects" to anything
  So that I can manage and brand this accordingly to meet my requirements.

  Background:
    Given the following existing Users:
      | id | name           | email                      | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 1  | John Smith     | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 2  | Greg Symons    | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
      | 3  | Another Person | another@dispostable.com    | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | AUS          | 08134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing Teams:
      | id | owner_id | name        | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
      | 1  | 1        | Johns Team  | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
      | 2  | 3        | Jack's Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-11 11:39:01 | 2016-05-11 11:39:01 | 2016-05-09 14:39:01 |
    And the following Users in Team 1:
      | id | role   |
      | 1  | owner  |
      | 2  | member |
    And the following existing Projects:
      | id | name        | user_id | deleted | created_at          | updated_at          |
      | 1  | My Project  | 1       | 0       | 2016-05-17 00:00:00 | 2016-05-17 00:00:00 |
      | 2  | A Project   | 2       | 0       | 2016-05-18 00:00:00 | 2016-05-18 00:00:00 |
      | 3  | The Project | 3       | 0       | 2016-05-19 00:00:00 | 2016-05-19 00:00:00 |
    And the following existing Components:
      | id | name            | class_name | created_at          | updated_at          |
      | 1  | User Account    | User       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | Team            | Team       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | Project         | Project    | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | Workspace       | Workspace  | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | Asset           | Asset      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 6  | Scanner App     | ScannerApp | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 7  | Event           | Event      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 8  | Rules           | Rule       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following existing ComponentPermissions:
      | id | component_id | instance_id | permission | user_id | team_id | granted_by | created_at          | updated_at          |
      | 1  | 1            | 2           | rw         | 1       | NULL    | 2          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Create a new Project on my account
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My New Project"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "1"

  Scenario: Create a new Project on someone else's account where I have Project write access
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My New Project"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    
  Scenario: I attempt to create a Project on someone else's account where I don't have Project write access
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to create Projects on that account."

  Scenario: I attempt to create a Project on non-existent account
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/10"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that person does not exist."

  Scenario: Delete a Project from my account
    Given that I want to delete a "Project"
    When I request "/api/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My Project"
    And the response has a "deleted" property
    And the type of the "deleted" property is bool
    And the "deleted" property equals "false"

  Scenario: Delete and confirm deletion of a Project from my account
    Given that I want to delete a "Project"
    When I request "/api/project/1/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My Project"
    And the response has a "deleted" property
    And the type of the "deleted" property is bool
    And the "deleted" property equals "true"

  Scenario: Delete a Project on someone else's account where I have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "A Project"
    And the response has a "deleted" property
    And the type of the "deleted" property is bool
    And the "deleted" property equals "false"

  Scenario: Delete and confirm deletion of a Project on someone else's account where I have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/2/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "A Project"
    And the response has a "deleted" property
    And the type of the "deleted" property is bool
    And the "deleted" property equals "true"

  Scenario: I attempt to delete a Project on someone else's account where I don't have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to delete that Project."

  Scenario: I attempt to delete a non-existent Project
    Given that I want to delete a "Project"
    When I request "/api/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: Edit the name of one of my projects
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Project"

  Scenario: Edit the name of someone else's Project where I have read/write permission
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Project"

  Scenario: I attempt to edit the name of someone else's Project where I don't have read/write permission
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to make changes to that Project."

  Scenario: I attempt to edit the name of a project, but I give a project ID that does not exist
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/5"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: Get a list of Projects on my account
    Given that I want to get information about my "Projects"
    When I request "/api/projects/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the type of the response is array
    And the array response has the following items:
    | id | name       | user_id  | created_at          | updated_at          |
    | 1  | My Project | 1        | 2016-05-17 00:00:00 | 2016-05-17 00:00:00 |

  Scenario: I attempt to get a list of projects for an account that I don't own
    Given that I want to get information about my "Projects"
    When I request "/api/projects/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to list those Projects."