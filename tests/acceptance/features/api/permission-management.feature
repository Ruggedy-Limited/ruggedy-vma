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

  Notes:
  If an authenticated user attempts to change the permissions for another user on a component that they own and
  everything in the request is valid, but that user does not already have any permission on that particular project,
  then a new permission will be created for that User and a successful response will be returned.
  If the user does already have the same permissions on the component as the changed permission option, then a
  successful response will be returned.
  Likewise, if a revoke permission is called by an authenticated User on a component that the User owns and no
  permission exists for that User on the given component, a successful response will be returned

  Background:
    Given the following existing Users:
      | id | name           | email                        | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 1  | John Smith     | johnsmith@dispostable.com    | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 2  | Greg Symons    | gregsymons@dispostable.com   | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 2               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
      | 3  | Another Person | another@dispostable.com      | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | AUS          | 08134582354 | NULL                  | 3               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
      | 4  | Tom Bombadill  | tombombadill@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
      | 5  | Bilbo Baggins  | bilbobaggins@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
      | 6  | Frodo Baggins  | frodobaggins@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
      | 7  | Samwise Gangee | samgangee@dispostable.com    | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
      | 8  | Aragorn        | aragorn@dispostable.com      | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
      | 9  | Gimli          | gimli@dispostable.com        | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /aphoto.jpg  | 0                    | NULL     | USA          | 09134582354 | NULL                  |                 | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-06-01 11:51:29 | 2016-06-01 11:51:43 |
    And the following existing Teams:
      | id | owner_id | name        | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
      | 1  | 1        | John's Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
      | 2  | 2        | Greg's Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-06-01 14:39:01 | 2016-06-01 14:39:01 |
      | 3  | 4        | Tom's Team  | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-06-01 14:39:01 | 2016-06-01 14:39:01 |
    And the following Users in Team 1:
      | id | role   |
      | 1  | owner  |
      | 2  | member |
    And the following existing Projects:
      | id | name              | user_id | created_at          | updated_at          |
      | 1  | John's Project    | 1       | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Project | 2       | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Project   | 3       | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
      | 4  | Shared Project    | 1       | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
    And the following existing Workspaces:
      | id | name                | user_id  | project_id | created_at          | updated_at          |
      | 1  | John's Workspace    | 1        | 1          | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Workspace | 2        | 2          | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Workspace   | 3        | 3          | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
      | 4  | Shared Workspace    | 1        | 4          | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
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
      | 1  | 1            | 1           | rw         | 5       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | 1            | 1           | r          | 6       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | 2            | 1           | rw         | 7       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | 2            | 1           | r          | 8       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | 3            | 4           | rw         | 9       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 6  | 3            | 4           | r          | 3       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 7  | 4            | 4           | rw         | 9       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 8  | 4            | 4           | r          | 3       |         | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 9  | 3            | 4           | rw         |         | 1       | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 10 | 3            | 4           | r          |         | 2       | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 11 | 4            | 4           | rw         |         | 1       | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 12 | 4            | 4           | r          |         | 2       | 1          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Grant read/write permissions for a Project to a user
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "rw"
    And the response has a "project_permissions" property
    And the type of the "project_permissions" property is array
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 1           | 2       | rw        |

  Scenario: Grant read only permissions for a Project to a user
    Given that I want to make a new "Permission"
    And that its "userID" is "2"
    And that its "permission" is "r"
    When I request "/api/acl/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "r"
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 1           | 2       | rw        |

  Scenario: I attempt to grant permissions for a Project that I don't own
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/project/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: I attempt to grant permissions for a non-existent Project
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/project/9"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: I attempt to grant permissions for a Project, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "delete"
    When I request "/api/acl/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option. Please use only 'r' or 'rw'."

  Scenario: I attempt to grant permissions for a Project, but the given user ID does not exist
    Given that I want to make a new "Permission"
    And that its "userId" is "20"
    And that its "permission" is "rw"
    When I request "/api/acl/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that User does not exist."

  Scenario: Change a users permissions on a Project from read/write to read only
    Given that I want to update a "Permission"
    And that its "userID" is "9"
    And that its "permission" is "r"
    When I request "/api/acl/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "9"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "r"
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | r         |
    | 4           | 3       | r         |

  Scenario: Change a users permissions on a Project from read only to read/write
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/project/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "3"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "rw"
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |
    | 4           | 3       | rw        |

  Scenario: Attempt to change permissions on a Project I own, but provide an invalid permission option
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "delete"
    When I request "/api/acl/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option. Please use only 'r' or 'rw'."
    
  Scenario: Attempt to change permissions on a Project I don't own
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/project/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: Attempt to change permissions on a non-existent Project
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/project/40"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: Attempt to change permissions for a Project, but the given user ID does not exist
    Given that I want to update a "Permission"
    And that its "userID" is "30"
    And that its "permission" is "rw"
    When I request "/api/acl/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that User does not exist."

  Scenario: Revoke all permissions related to a Project for a user
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/project/4/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "3"
    And the response has a "permission" property
    And the "permission" property equals "null"
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |

  Scenario: Attempt to revoke permissions on a Project I don't own
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/project/2/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: Attempt to revoke permissions on a non-existent Project
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/project/40/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: Retrieve the current permissions for a particular Project
    Given that I want to get information about "Permission"
    When I request "/api/acl/project/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the "project_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |
    | 4           | 3       | r         |

  Scenario: I attempt to retrieve the permissions for a project that I don't own
    Given that I want to get information about "Permission"
    When I request "/api/acl/project/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you can only see permissions for things that you own."

  Scenario: I attempt to retrieve the permissions for a non-existent project
    Given that I want to get information about "Permission"
    When I request "/api/acl/project/40"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Project does not exist."

  Scenario: Grant read/write permissions for a Workspace to a user
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "rw"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 2       | rw        |
    | 4           | 3       | r         |
    | 4           | 9       | rw        |

  Scenario: Grant read only permissions for a Workspace to a user
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "r"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "2"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "r"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 2       | r         |
    | 4           | 3       | r         |
    | 4           | 9       | rw        |

  Scenario: I attempt to grant permissions for a Workspace that I don't own
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: I attempt to grant permissions for a Workspace, but I provide an invalid workspace ID
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/12"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."

  Scenario: I attempt to grant permissions for a Workspace, but I provide an invalid permission
    Given that I want to make a new "Permission"
    And that its "userId" is "2"
    And that its "permission" is "delete"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option."

  Scenario: I attempt to grant permissions for a Workspace, but the given user ID does not exist
    Given that I want to make a new "Permission"
    And that its "userId" is "20"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that User does not exist."

  Scenario: Change a users permissions on a Workspace from read/write to read only
    Given that I want to update a "Permission"
    And that its "userID" is "9"
    And that its "permission" is "r"
    When I request "/api/acl/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "9"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "r"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | r         |
    | 4           | 3       | r         |

  Scenario: Change a users permissions on a Workspace from read only to read/write
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "3"
    And the response has a "permission" property
    And the type of the "permission" property is string
    And the "permission" property equals "rw"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |
    | 4           | 3       | rw        |

  Scenario: Attempt to change permissions on a Workspace I own, but provide an invalid permission option
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "delete"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, there is no such permission option. Please use only 'r' or 'rw'."

  Scenario: Attempt to change permissions on a Workspace I don't own
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: Attempt to change permissions on a non-existent Workspace
    Given that I want to update a "Permission"
    And that its "userID" is "3"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/40"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."

  Scenario: Attempt to change permissions for a Workspace, but the given user ID does not exist
    Given that I want to update a "Permission"
    And that its "userID" is "30"
    And that its "permission" is "rw"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that User does not exist."

  Scenario: Revoke all permissions related to a Workspace for a user
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/workspace/4/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the response has a "user_id" property
    And the type of the "user_id" property is integer
    And the "user_id" property equals "3"
    And the response has a "permission" property
    And the "permission" property equals "null"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |

  Scenario: Attempt to revoke permissions on a Workspace I don't own
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/workspace/2/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, we couldn't change/add those permissions. You can only modify permissions on your own things."

  Scenario: Attempt to revoke permissions on a non-existent Workspace
    Given that I want to remove a "Permission" from my team
    When I request "/api/acl/workspace/40/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."

  Scenario: Retrieve the current permissions for a particular Workspace
    Given that I want to get information about "Permission"
    When I request "/api/acl/workspace/4"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "4"
    And the "workspace_permissions" array property has the following items:
    | instance_id | user_id | permisson |
    | 4           | 9       | rw        |
    | 4           | 3       | r         |

  Scenario: I attempt to retrieve the permissions for a workspace that I don't own
    Given that I want to get information about "Permission"
    When I request "/api/acl/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you can only see permissions for things that you own."

  Scenario: I attempt to retrieve the permissions for a non-existent workspace
    Given that I want to get information about "Permission"
    When I request "/api/acl/workspace/40"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."