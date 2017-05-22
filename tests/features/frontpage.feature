Feature: Frontpage testing.

  Scenario: Anonymous access
    Given I am on the homepage
    Then I should see "Log in"
    