Feature: Login testing.

#  Scenario: Testing the login form.
#    Given I visit "user"
#    And I fill in "Username" with "@user-name"
#    And I fill in "Password" with "@user-pass"
#    When I press "Log in"
#    Then I should see "@user-name"

  Scenario: Testing the user can"t login with bas credentials.
    Given I visit "user/login"
    And I fill in "Username" with "foo"
    And I fill in "Password" with "bar"
    When I press "Log in"
    Then I should see "Unrecognized username or password."
