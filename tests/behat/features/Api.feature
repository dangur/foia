@foia_api
Feature: FOIA API
  In order to retrieve form fields for a specific Agency component
  As a front-end interface
  I should be able to query for a component and get form fields in return

  @api
  Scenario: Get webform fields from API
    Given I am in the "jsonapi/node/agency_component" path
    When
    Then I should not see the text ''