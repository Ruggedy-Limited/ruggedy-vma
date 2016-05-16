Feature: As an account or team owner
  I want to be able to create, update and delete Project Spaces
  Project spaces will help me group logical functions such as Workspaces under a common group

  I want to be able to add custom "tags" to Projects for reporting and grouping.

  As an administrator
  I want the ability to change the default / given name of "Projects" to anything
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
    And the following Objects:
      | id | name            | created_at          | updated_at          |
      | 1  | User Account    | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | Project         | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | Workspace       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | Asset           | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | Scanner App     | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 6  | Event           | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following UserPermissions:
      | object_id | object_type | user_id | permission_id | created_at          | updated_at          |
      | 2         | 1           | 1       | 3             | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following Permissions:
      | id | name       | created_at          | updated_at          |
      | 1  | read only  | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | read/write | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Create a new Project on my account
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "1"

  Scenario: Create a new Project on someone else's account where I have Project write access
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
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
    And the "message" property equals "Sorry, we couldn't create this Project. You don't have permission to create Projects on that account."

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
    And the "message" property equals "Sorry, we couldn't create this Project. We could not find an existing account with that ID."

  Scenario: Delete a Project from my account
    Given that I want to delete a "Project"
    When I request "/api/project/1/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a project will delete all the data related to that project. This is not reversable. Please confirm by repeating this request."

  Scenario: Delete and confirm deletion of a Project from my account
    Given that I want to delete a "Project"
    When I request "/api/project/1/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a project will delete all the data related to that project. This is not reversable. Please confirm by repeating this request."
    When I request "/api/project/1/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "We have deleted that project as requested."

  Scenario: Delete a Project on someone else's account where I have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/2/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a project will delete all the data related to that project. This is not reversable. Please confirm by repeating this request."

  Scenario: Delete and confirm deletion of a Project on someone else's account where I have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/2/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Deleting a project will delete all the data related to that project. This is not reversable. Please confirm by repeating this request."
    When I request "/api/project/2/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "We have deleted that project as requested."

  Scenario: I attempt to delete a Project on someone else's account where I don't have Project write access
    Given that I want to delete a "Project"
    When I request "/api/project/3/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't delete that Project. You don't have permission to delete Projects on that account."

  Scenario: I attempt to delete a Project on non-existent account
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/3/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't delete this Project. We could not find the account you were looking for."

  Scenario: I attempt to delete a non-existent Project
    Given that I want to make a new "Project"
    And that its "name" is "My New Project"
    When I request "/api/project/4/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't that Project to delete it."

  Scenario: Edit the name of one of my projects
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Project"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Project updated successfully."

  Scenario: Edit the name of someone else's Project where I have read/write permission
    Given that I want to update a "Project"
    And that I want to change it's "name" to "Renamed Project"
    When I request "/api/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Project"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Project updated successfully."

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
    And the "message" property equals "Sorry, we could not update that Project. You don't have permission to change it."

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
    And the "message" property equals "Sorry, we could not update that Project. We could not find that project."

  Scenario: Get a list of Projects on my account
    Given that I want to get information about my "Projects"
    When I request "/api/projects/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "projects" property
    And the type of the "projects" property is array

  Scenario: I attempt to get a list of projects for an account that I don't own
    Given that I want to get information about my "Projects"
    When I request "/api/projects/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we could not give information about the projects on that account."