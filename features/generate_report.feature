Feature: Php-metrics-dashboard features

  Background: Generate a PhpMetric report for a project
    Given I am in the root directory
    And I have a config file with content:
    """
    {
        "sf-bootstrap": {
            "url": "https://github.com/romibuzi/sf-bootstrap",
            "excluded-dirs": ["app", "vendor"]
        }
    }
    """
    When I run "generate-reports" command
    Then I should get "sf-bootstrap" directory in projects sources folder
    And "sf-bootstrap" sources folder should be a git repository
    Then I should get "sf-bootstrap" directory in projects reports folder
    And I should get a report file in "sf-bootstrap" reports folder

  Scenario: View this report on the web interface
    When I call GET "/"
    Then The response code should be 200
    And The reponse should contains "List of your scanned projects"
    And The reponse should contains "sf-bootstrap"
    When I call GET "/sf-bootstrap"
    Then The response code should be 200
    And The reponse should contains "List of reports for the project sf-bootstrap"
    And The reponse should contains a report link for the actual date