Feature: As a registered user
  I want to be able to access the homepage

  Scenario: Access the homepage
    When I am on the homepage
    Then the response status code should be 200
    And I should see "Login"
    And I should see "Register"