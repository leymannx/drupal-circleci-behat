Feature: Global Elements

  Scenario: Homepage Contact Us Link
    Given I am on the homepage
    Then I should see the link "Contact Us" in the "branding_second" region
    Then I should see the "Search" button in the "branding_second" region
    Then I should see the "div#block-system-main-menu" element in the "menu" region
