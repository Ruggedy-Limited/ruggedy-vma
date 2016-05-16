Feature: As an administrator of my account
  I want the ability to integrate with DUO 2FA
  So that all users accessing my account are able to leverage 2FA

  Background:
    Given the following existing users:
      | id | name        | email                      | password                                                     | remember_token | photo_url    | uses_two_factor_auth | authy_id | country_code | phone       | two_factor_reset_code | current_team_id | stripe_id | current_billing_plan | card_brand | card_last_four | card_country | billing_address | billing_address_line_2 | billing_city | billing_state | billing_zip | billing_country | vat_id | extra_billing_information | trial_ends_at       | last_read_announcements_at | created_at          | updated_at          |
      | 1  | John Smith  | johnsmith@dispostable.com  | $2y$10$IPgIlPVo/NW6fQMx0gJUyesYjV1N4LwC1fH2rj94s0gq.xDjMisNm | NULL           | NULL         | 0                    | NULL     | ZAR          | 0716852996  | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-19 14:39:01 | 2016-05-09 14:39:01        | 2016-05-09 14:39:01 | 2016-05-09 14:39:02 |
      | 2  | Greg Symons | gregsymons@dispostable.com | $2y$10$0WLCM1EUuJce.zSlS1N4h.XRn7u8uDbyxslTkFOI0ka0fxSIXmjhC | NULL           | /myphoto.jpg | 0                    | NULL     | NZ           | 06134582354 | NULL                  | 1               | NULL      | NULL                 | NULL       | NULL           | NULL         | NULL            | NULL                   | NULL         | NULL          | NULL        | NULL            | NULL   | NULL                      | 2016-05-20 11:51:29 | 2016-05-10 11:51:43        | 2016-05-10 11:51:29 | 2016-05-10 11:51:43 |
    And the following existing API tokens:
      | id                                   | user_id | name           | token                                                        | metadata | transient | last_used_at | expires_at | created_at          | updated_at          |
      | c459797b-40c1-4549-8b6f-d1977c3d36f6 |       1 | My First Token | OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN | []       | 0         | NULL         | NULL       | 2016-05-09 14:43:37 | 2016-05-09 14:43:37 |
    And a valid API key "OaLLlZl4XB9wgmSGg7uai1nvtTiDsLpSBCfFoLKv18GCDdiIxxPLslKZmcPN"
  
  Scenario: Turn two-factor authentication on for my account
    Given that I want to update my "Account"
    And that I want to change it's "uses_two_factor_auth" to "1"
    When I request "/api/2fa"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "uses_two_factor_auth" property
    And the type of the "uses_two_factor_auth" property is integer
    And the "uses_two_factor_auth" property equals "1"
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Account updated successfully."

  Scenario: Turn two-factor authentication off for my account
    Given that I want to update my "Account"
    And that I want to change it's "uses_two_factor_auth" to "0"
    When I request "/api/2fa"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "uses_two_factor_auth" property
    And the type of the "uses_two_factor_auth" property is integer
    And the "uses_two_factor_auth" property equals "0"
    And the response has a "success" property
    And the type of the "success" property is boolean
    And the "success" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Account updated successfully."

  Scenario: Turn two-factor authentication on for my account, but use and invalid value
    Given that I want to update my "Account"
    And that I want to change it's "uses_two_factor_auth" to "3"
    When I request "/api/2fa"
    Then the HTTP response code should be 200
    And the response is JSON
    And the response has a "error" property
    And the type of the "error" property is boolean
    And the "error" property equals "true"
    And the response has a "message" property
    And the type of the "message" property is string
    And the "message" property equals "Account could not be updated. That is not a valid value for the field you're trying to update."