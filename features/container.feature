Feature: DiDebuggerBundle
  Scenario: Service name check with correct definition
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service1.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="service1" class="%service1.class%" />
      </services>
      """
    And I add the "ClassChecker" cheker
    When I check the service "service1"
    Then I should get no error

  Scenario: Service check with factory class
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory1.class">Behat\Test\Factory1</parameter>
        <parameter key="service1.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="service1" class="Behat\Test\Service1" factory-class="%factory1.class%" factory-method="create" />
      </services>
      """
    And I add the "ClassChecker" cheker
    When I check the service "service1"
    Then I should get no error

  Scenario: Service check with non-existent factory class
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory1.class">Behat\Test\FactoryNonExistent</parameter>
        <parameter key="service1.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="service1" class="Behat\Test\Service1" factory-class="%factory1.class%" factory-method="create" />
      </services>
      """
    And I add the "ClassChecker" cheker
    When I check the service "service1"
    Then I should get "NonExistentFactoryClassException" error

  Scenario: Service check with non-existent factory method
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory1.class">Behat\Test\Factory1</parameter>
        <parameter key="service1.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="service1" class="Behat\Test\Service1" factory-class="%factory1.class%" factory-method="doNotExist" />
      </services>
      """
    And I add the "ClassChecker" cheker
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service1"
    Then I should get "NonExistentFactoryMethodException" error

  Scenario: Service name check with wrong class name
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service1.class">Behat\Test\NonExistent</parameter>
      </parameters>
      <services>
        <service id="service1" class="%service1.class%" />
      </services>
      """
    And I add the "ClassChecker" cheker
    When I check the service "service1"
    Then I should get "NonExistentClassException" error

  Scenario: Service definition with less parameters than the constructor function
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service2.class">Behat\Test\Service2</parameter>
      </parameters>
      <services>
        <service id="service2" class="%service2.class%" />
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooFewParameters" error

  Scenario: Service definition with more parameters than the factory class method
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory2.class">Behat\Test\Factory2</parameter>
        <parameter key="service2.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="service2" class="%service2.class%"  factory-class="%factory2.class%" factory-method="create">
          <argument>one</argument>
          <argument>two</argument>
        </service>
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooManyParameters" error

  Scenario: Service definition with more parameters than the factory service method
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory2.class">Behat\Test\Factory2</parameter>
        <parameter key="service2.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="factory2.service" class="%factory2.class%" />
        <service id="service2" class="%service2.class%"  factory-service="factory2.service" factory-method="create">
          <argument>one</argument>
          <argument>two</argument>
        </service>
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooManyParameters" error

  Scenario: Service definition with less parameters than the factory service method
    Given I have a container definition file
    """
      <parameters>
        <parameter key="factory2.class">Behat\Test\Factory2</parameter>
        <parameter key="service2.class">Behat\Test\Service1</parameter>
      </parameters>
      <services>
        <service id="factory2.service" class="%factory2.class%" />
        <service id="service2" class="%service2.class%"  factory-service="factory2.service" factory-method="create"></service>
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooFewParameters" error

  Scenario: Service definition with less parameters than the constructor function
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service2.class">Behat\Test\Service2</parameter>
      </parameters>
      <services>
        <service id="service2" class="%service2.class%" />
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooFewParameters" error

  Scenario: Service definition with more parameters than the constructor function
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service2.class">Behat\Test\Service2</parameter>
      </parameters>
      <services>
        <service id="service2" class="%service2.class%">
          <argument>one</argument>
          <argument>two</argument>
        </service>
      </services>
      """
    And I add the "ArgumentsCountChecker" cheker
    When I check the service "service2"
    Then I should get "TooManyParameters" error

  Scenario: Service definition with correct args, but unused
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service2.class">Behat\Test\Service2</parameter>
      </parameters>
      <services>
        <service id="service2" class="%service2.class%">
          <argument>one</argument>
        </service>
      </services>
      """
    And I add the "UnusedArgumentChecker" cheker
    When I check the service "service2"
    Then I should get "UnusedArgument" error

  Scenario: Service definition with correct args, all used
    Given I have a container definition file
    """
      <parameters>
        <parameter key="service3.class">Behat\Test\Service3</parameter>
      </parameters>
      <services>
        <service id="service3" class="%service3.class%">
          <argument>one</argument>
        </service>
      </services>
      """
    And I add the "UnusedArgumentChecker" cheker
    When I check the service "service3"
    Then I should get no error