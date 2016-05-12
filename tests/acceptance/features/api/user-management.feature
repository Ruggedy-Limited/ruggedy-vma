Feature: As a user of the API framework
  I want the ability to manage users through the API
  So that I can create, edit and delete user accounts inside my account instance
  So that I can report on the state of users through the API
  (last login, account state - active / inactive, use of 2FA (Yes / No)

  As an administrator and user of the API framework
  I want the ability to enter personally identifiable data
  So that:
  * My email address is available for notifications
  * My password is changeable
  * My personal data such as mobile numbers and addresses are changeable.

  Background:
    Given the following existing users:
    | id        | name        | email                      | password                                                     | remember_token | photo_url | uses_two_factor_auth | authy_id | country_code | phone | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
    | 99999998  | John Smith  | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL      | 0                    | NULL     | NULL         | NULL  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
    | 99999999  | Greg Symons | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | NULL      | 0                    | NULL     | NULL         | NULL  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing teams:
    | id        | owner_id | name       | photo_url | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | created_at          | updated_at          |
    | 99999999  | 99999998 | Johns Team | NULL      | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 | 2016-05-09 14:39:01 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"

  Scenario: Adding a person to one of the teams on my account
    Given that I want to add a "Person" to my team
    And that their "email" is "garethpeter@gmail.com"
    When I request "/api/users/1"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "email" property
    And the type of the "email" property is string
    And the "email" property equals "garethpeter@gmail.com"
    And the response has a "token" property
    And the type of the "token" property is string

  Scenario: Removing a person from one of the teams on my account

  Scenario: Edit my account information