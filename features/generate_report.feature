Feature: Php-metrics-dashboard features

  Background: Generate a PhpMetric report for projects
    Given I am in the root directory
    And I have a config file with content:
    """
    {
        "majordome": {
            "url": "https://github.com/romibuzi/majordome",
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
    Then I should get "majordome" directory in projects sources folder
    And "majordome" sources folder should be a git repository
    Then I should get "majordome" directory in projects reports folder
    And I should get a report file in "majordome" reports folder
    And "projetSNCF" git repository should be at "V2" branch

  Scenario: View theese report on the web interface
    When I call GET "/"
    Then The response code should be 200
    And The reponse should contains "List of your scanned projects"
    And The reponse should contains "majordome"
    And The reponse should contains "projetSNCF"
    When I call GET "/majordome"
    Then The response code should be 200
    And The reponse should contains "List of reports for the project majordome"
    And The reponse should contains a report link for the actual date
