Feature: Login testing.

  Scenario: Wrong credentials
    Given I visit "user/login"
    And I fill in "name" with "foo"
    And I fill in "pass" with "bar"
    When I press "op"
    Then I should see an ".error" element

