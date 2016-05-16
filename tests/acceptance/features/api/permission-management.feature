Feature: As an owner of a team
  I want the ability to create permissions so that I can control access to:
  * Projects
  * Workspaces
  * Assets
  * Scanner Modules
  * Event Manager

  Possible permissions on each of the above are:
  * none 
  * read only (default)
  * read/write

  Background:
    Given the following existing Users:
      | id        | name        | email                      | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 99999998  | John Smith  | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 99999999  | Greg Symons | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing Teams:
      | id        | owner_id | name       | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
      | 99999999  | 99999998 | Johns Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
    And the following existing Projects:
      | id       | name              | owner_id | created_at          | updated_at          |
      | 99999999 | John's Project    | 99999998 | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 99999998 | Someone's Project | 99999999 | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
    And the following existing Workspaces:
      | id       | name                | owner_id | project_id | created_at          | updated_at          |
      | 99999999 | John's Workspace    | 99999998 | 99999999   | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 99999998 | Someone's Workspace | 99999999 | 99999998   | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
    And the following existing Assets:
      | id       | name                | owner_id | workspace_id | created_at          | updated_at          |
      | 99999999 | John's Asset        | 99999998 | 99999999     | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 99999998 | Someone's Asset     | 99999999 | 99999998     | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
    And the following existing ScannerApps:
      | id       | name                | created_at          | updated_at          |
      | 99999999 | Nmap Port Scanner   | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 99999998 | Nessus Scanner      | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
    And the following existing Events:
      | id       | name              | asset_id | created_at          | updated_at          |
      | 99999999 | Open Port         | 99999999 | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 99999998 | XSS Vulnerability | 99999998 | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
    And the following existing Components:
      | id | name            | created_at          | updated_at          |
      | 1  | Project         | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | Workspace       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | Asset           | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | Scanner App     | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | Event           | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following existing UserPermissions:
      | id       | cog_id | cog_type | user_id | permission_id |
      | 99999999 | cog_id | cog_type | user_id | permission_id |
    And the following existing ScannerAppPermissions:
      | id       | scanner_id | asset_id | user_id  | permission_id |
      | 99999998 | 99999999   | 99999999 | 99999998 | 1             |
    And the following existing Permissions:
      | id | name       | created_at          | updated_at          |
      | 1  | none       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | read only  | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | read/write | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Grant read/write permissions for a Project to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/project/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read/write"

  Scenario: Grant read only permissions for a Project to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read only"
    When I request "/api/permission/project/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read only"

  Scenario: Revoke all permissions related to a Project for a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "none"
    When I request "/api/permission/project/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "none"

  Scenario: I attempt to grant permissions for a Project that I don't own
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/project/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on things you own."

  Scenario: I attempt to grant permissions for a Project, but I provide an invalid project ID
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/project/99997998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't find that project in your account."

  Scenario: I attempt to grant permissions for a Project, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "delete"
    When I request "/api/permission/project/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."

  Scenario: Grant read/write permissions for a Workspace to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/workspace/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read/write"

  Scenario: Grant read only permissions for a Workspace to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read only"
    When I request "/api/permission/workspace/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read only"

  Scenario: Revoke all permissions related to a Workspace for a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "none"
    When I request "/api/permission/workspace/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "none"

  Scenario: I attempt to grant permissions for a Workspace that I don't own
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/workspace/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on things you own."

  Scenario: I attempt to grant permissions for a Workspace, but I provide an invalid workspace ID
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/workspace/99997998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't find that workspace in your account."

  Scenario: I attempt to grant permissions for a Workspace, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "delete"
    When I request "/api/permission/workspace/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."

  Scenario: Grant read/write permissions for a Asset to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/asset/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read/write"

  Scenario: Grant read only permissions for a Asset to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read only"
    When I request "/api/permission/asset/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read only"

  Scenario: Revoke all permissions related to a Asset for a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "none"
    When I request "/api/permission/asset/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "none"

  Scenario: I attempt to grant permissions for a Asset that I don't own
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/asset/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on things you own."

  Scenario: I attempt to grant permissions for a Asset, but I provide an invalid asset ID
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/asset/99997998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't find that asset in your account."

  Scenario: I attempt to grant permissions for a Asset, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "delete"
    When I request "/api/permission/asset/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."

  Scenario: Grant read/write permissions for a Scanner-app to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/scanner-app/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read/write"

  Scenario: Grant read only permissions for a Scanner-app to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read only"
    When I request "/api/permission/scanner-app/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read only"

  Scenario: Revoke all permissions related to a Scanner-app for a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "none"
    When I request "/api/permission/scanner-app/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "none"

  Scenario: I attempt to grant permissions for a Scanner-app that I don't own
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/scanner-app/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on things you own."

  Scenario: I attempt to grant permissions for a Scanner-app, but I provide an invalid scanner-app ID
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/scanner-app/99997998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't find that scanner-app in your account."

  Scenario: I attempt to grant permissions for a Scanner-app, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "delete"
    When I request "/api/permission/scanner-app/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."

  Scenario: Grant read/write permissions for a Event to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/event/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read/write"

  Scenario: Grant read only permissions for a Event to a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read only"
    When I request "/api/permission/event/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "read only"

  Scenario: Revoke all permissions related to a Event for a user
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "none"
    When I request "/api/permission/event/99999999"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "99999999"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "99999998"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "none"

  Scenario: I attempt to grant permissions for a Event that I don't own
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/event/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on things you own."

  Scenario: I attempt to grant permissions for a Event, but I provide an invalid event ID
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "read/write"
    When I request "/api/permission/event/99997998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't find that event in your account."

  Scenario: I attempt to grant permissions for a Event, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "user" is "99999998"
    And that its "access" is "delete"
    When I request "/api/permission/event/99999998"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."