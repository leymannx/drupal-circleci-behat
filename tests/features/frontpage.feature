Feature: Frontpage testing.

  Scenario: Anonymous access
    Given I am on the homepage
    Then I should see "Welcome to ixde.dev"

  Scenario: Anonymous access
    Given I am on the homepage
    Then the response status code should be 200
    