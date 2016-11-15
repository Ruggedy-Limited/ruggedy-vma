Feature: As an account or team owner
  I want to be able to create, update and delete Workspaces
  Workspaces will help me logically group Assets so that I can report on my assets
  (Vulnerabilities, Exploits, Open Ports, etc.) and easily mange "triggers / events"

  I want to be able to add custom "tags" to Workspaces for reporting and grouping.

  As an administrator
  I want the ability to change the default / given name of "Workspaces" to anything
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
    And the following existing Workspaces:
      | id | name                | user_id  | created_at          | updated_at          |
      | 1  | John's Workspace    | 1        | 2016-05-13 11:06:00 | 2016-05-13 11:06:00 |
      | 2  | Someone's Workspace | 2        | 2016-05-13 10:06:00 | 2016-05-13 10:06:00 |
      | 3  | Another Workspace   | 3        | 2016-05-13 09:06:00 | 2016-05-13 09:06:00 |
    And the following existing Files:
      | id | path                                              | format | size    | user_id | workspace_id | scanner_app_id | processed | deleted | created_at          | updated_at          |
      | 1  | scans/xml/nmap/1/nmap-adv-multiple-node-dns.xml   | xml    | 18646   | 1       | 1            | 1              | 1         | 0       | 2016-10-10 06:51:18 | 2016-11-14 15:00:19 |
      | 2  | scans/xml/burp/1/burp-multiple-auth-dns+ip.xml    | xml    | 4660178 | 1       | 1            | 2              | 1         | 0       | 2016-10-10 06:51:35 | 2016-11-14 15:00:19 |
      | 3  | scans/xml/nexpose/1/full-multiple-dns.xml         | xml    | 3662061 | 1       | 1            | 3              | 1         | 0       | 2016-10-10 06:51:53 | 2016-11-14 15:00:19 |
      | 4  | scans/xml/netsparker/1/single-dns.xml             | xml    | 568818  | 1       | 1            | 4              | 1         | 0       | 2016-10-17 19:25:33 | 2016-11-14 15:00:19 |
      | 5  | scans/xml/nessus/1/full-multiple-dns.nessus       | xml    | 1841174 | 2       | 2            | 5              | 1         | 0       | 2016-10-24 07:26:59 | 2016-11-14 16:58:45 |
      | 8  | scans/xml/nessus/1/full-audit-multiple-dns.nessus | xml    | 2664096 | 1       | 1            | 5              | 1         | 0       | 2016-11-07 07:22:00 | 2016-11-14 15:00:19 |
    And the following existing ScannerApps:
      | id | name       | description                      | created_at          | updated_at          |
      | 1  | nmap       | NMAP Port Scanner Utility        | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 2  | burp       | Burp Vulnerability Scanner       | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 3  | netsparker | Netsparker Vulnerability Scanner | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 4  | nexpose    | Nexpose Vulnerability Scanner    | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
      | 5  | nessus     | Nessus Vulnerability Scanner     | 2016-07-28 23:17:04 | 2016-07-28 23:17:04 |
    And the following existing Components:
      | id | name            | class_name | created_at          | updated_at          |
      | 1  | User Account    | User       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 2  | Team            | Team       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 3  | Workspace       | Workspace  | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 4  | Asset           | Asset      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 5  | Scanner App     | ScannerApp | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 6  | Event           | Event      | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
      | 7  | Rules           | Rule       | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And the following existing ComponentPermissions:
      | id | component_id | instance_id | permission | user_id | team_id | granted_by | created_at          | updated_at          |
      | 1  | 1            | 2           | rw         | 1       | NULL    | 2          | 2016-05-10 00:00:00 | 2016-05-10 00:00:00 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  ##
  # Creating Workspaces
  ##
  Scenario: Create a new Workspace on my own User account
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
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"

  Scenario: Create a new Workspace on someone else's User account where I have write access
    Given that I want to make a new "Workspace"
    And that its "name" is "My New Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "My New Workspace"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"

  Scenario: I attempt to create a Workspace on someone else's User account where I don't have write access
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
    And the "message" property equals "Sorry, you don't have permission to create Workspaces on that User account."

  Scenario: I attempt to create a Workspace on non-existent User account
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
    And the "message" property equals "Sorry, that person does not exist."

  ##
  # Deleting Workspaces
  ##
  Scenario: Delete a Workspace from my own User account
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "John's Workspace"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is bool
    And the "isDeleted" property equals "false"

  Scenario: Delete and confirm deletion of a Workspace from my own User account
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/1/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "John's Workspace"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is bool
    And the "isDeleted" property equals "true"

  Scenario: Delete a Workspace on someone else's User account where I have write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Someone's Workspace"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is bool
    And the "isDeleted" property equals "false"

  Scenario: Delete and confirm deletion of a Workspace on someone else's User account where I have Workspace write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/2/confirm"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Someone's Workspace"
    And the response has a "isDeleted" property
    And the type of the "isDeleted" property is bool
    And the "isDeleted" property equals "true"

  Scenario: I attempt to delete a Workspace on someone else's User account where I don't have Workspace write access
    Given that I want to delete a "Workspace"
    When I request "/api/workspace/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to delete that Workspace."

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
    And the "message" property equals "Sorry, that Workspace does not exist."

  ##
  # Editing Workspaces
  ##
  Scenario: Edit the name of a Workspace on my own User account
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "1"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Workspace"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "1"

  Scenario: Edit the name of a Workspace on someone else's User account where I have write permission
    Given that I want to update a "Workspace"
    And that I want to change it's "name" to "Renamed Workspace"
    When I request "/api/workspace/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the response has a "id" property
    And the type of the "id" property is integer
    And the "id" property equals "2"
    And the response has a "name" property
    And the type of the "name" property is string
    And the "name" property equals "Renamed Workspace"
    And the response has a "ownerId" property
    And the type of the "ownerId" property is integer
    And the "ownerId" property equals "2"

  Scenario: I attempt to edit the name of a Workspace on someone else's User account where I don't have read/write permission
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

  Scenario: I attempt to edit the name of a Workspace, but I give a Workspace ID that does not exist
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

  ##
  # Listing Workspaces
  ##
  Scenario: Get a list of Workspaces on my own User account
    Given that I want to get information about "Workspaces"
    When I request "/api/workspaces/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the array response has the following items:
      | id | name             | ownerId | assets | files | isDeleted |
      | 1  | John's Workspace | 1       | *      | *     | false     |

  Scenario: Get a list of Workspaces on someone else's User account where I have at least read access
    Given that I want to get information about "Workspaces"
    When I request "/api/workspaces/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the array response has the following items:
      | id | name                | ownerId | assets | files | isDeleted |
      | 2  | Someone's Workspace | 2       | *      | *     | false     |

  Scenario: Attempt to get a list of Workspaces on someone else's User account where I don't have read access
    Given that I want to get information about "Workspaces"
    When I request "/api/workspaces/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to list those Workspaces."

  Scenario: Attempt to list Workspaces on a non-existent User account
    Given that I want to get information about "Workspaces"
    When I request "/api/workspaces/99"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that person does not exist."

  ##
  # Listing the Scanner Apps that have been used in a Workspace
  ##
  Scenario: Get a list of Apps used in one of my Workspaces
    Given that I want to get information about "Apps"
    When I request "/api/workspace/apps/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the array response has the following items:
      | id | name       | description                      |
      | 1  | nmap       | NMAP Port Scanner Utility        |
      | 2  | burp       | Burp Vulnerability Scanner       |
      | 3  | netsparker | Netsparker Vulnerability Scanner |
      | 4  | nexpose    | Nexpose Vulnerability Scanner    |
      | 5  | nessus     | Nessus Vulnerability Scanner     |

  Scenario: Get a list of Apps in someone else's Workspace where I have at least read access
    Given that I want to get information about "Apps"
    When I request "/api/workspace/apps/2"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response does not have a "error" property
    And the array response has the following items:
      | id | name       | description                      |
      | 5  | nessus     | Nessus Vulnerability Scanner     |

  Scenario: Attempt to get a list of Apps in someone else's Workspace where I don't have read access
    Given that I want to get information about "Apps"
    When I request "/api/workspace/apps/3"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, you don't have permission to list those Apps."

  Scenario: Attempt to get a list of Apps on a non-existent Workspace
    Given that I want to get information about "Apps"
    When I request "/api/workspace/apps/99"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Sorry, that Workspace does not exist."