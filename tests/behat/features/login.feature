Feature: Login testing.

  Scenario: Wrong credentials
    Given I visit "user/login"
    And I fill in "Username" with "foo"
    And I fill in "Password" with "bar"
    When I press "Log in"
    Then I should see "Unrecognized username or password."
