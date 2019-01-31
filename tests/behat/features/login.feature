Feature: Login testing.

  Scenario: Wrong credentials
    Given I visit "user/login"
    And I fill in "name" with "foo"
    And I fill in "pass" with "bar"
    When I press "op"
    Then I should see an ".error" element

  @api @javascript
  Scenario: Login in as authenticated user
    Given I am logged in as a user with the "Authenticated user" role
    Then I should see an "#toolbar-administration" element
