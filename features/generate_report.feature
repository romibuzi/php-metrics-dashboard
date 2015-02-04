Feature: Php-metrics-dashboard features

  Background: Generate a PhpMetric report for projects
    Given I am in the root directory
    And I have a config file with content:
    """
    {
        "sf-bootstrap": {
            "url": "https://github.com/romibuzi/sf-bootstrap",
            "excluded-dirs": ["app", "vendor"]
        },
        "projetSNCF": {
            "url": "https://github.com/BuZzi/projetSNCF",
            "branch": "V2",
            "excluded-dirs": ["app", "vendor"]
        }
    }
    """
    When I run "generate-reports" command
    Then I should get "sf-bootstrap" directory in projects sources folder
    And "sf-bootstrap" sources folder should be a git repository
    Then I should get "sf-bootstrap" directory in projects reports folder
    And I should get a report file in "sf-bootstrap" reports folder
    And "projetSNCF" git repository should be at "V2" branch

  Scenario: View theese report on the web interface
    When I call GET "/"
    Then The response code should be 200
    And The reponse should contains "List of your scanned projects"
    And The reponse should contains "sf-bootstrap"
    And The reponse should contains "projectSNCF"
    When I call GET "/sf-bootstrap"
    Then The response code should be 200
    And The reponse should contains "List of reports for the project sf-bootstrap"
    And The reponse should contains a report link for the actual date