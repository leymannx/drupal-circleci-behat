Feature: Frontpage testing.

  Scenario: Anonymous access
    Given I am on the homepage
    Then the response status code should be 200

  @javascript
  Scenario: Anonymous access
    Given I am on the homepage
    Then I should see "Hello World"
