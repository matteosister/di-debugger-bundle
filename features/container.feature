Feature: DiDebuggerBundle
  Scenario: Service name check with correct definition
    Given I have a container definition file
      """
      <services>
        <service id="service1" class="Behat\Test\Service1" />
      </services>
      """
    When I add the "Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker" cheker
    And check the service "service1"
    Then I should get no error

  Scenario: Service name check with wrong class name
    Given I have a container definition file
      """
      <services>
        <service id="service1" class="Behat\Test\NonExistent" />
      </services>
      """
    When I add the "Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker" cheker
    And check the service "service1"
    Then I should get "NonExistentClassException" error