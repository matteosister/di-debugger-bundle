<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var string
     */
    private $containerXmlFile;

    /**
     * @var \Cypress\DiDebuggerBundle\Checker\Service
     */
    private $serviceChecker;

    /**
     * @var array
     */
    private $errors;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->containerBuilder = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $this->serviceChecker = new \Cypress\DiDebuggerBundle\Checker\Service();
        $this->serviceChecker->setContainerBuilder($this->containerBuilder);
        $this->serviceChecker->setContainer($this->containerBuilder);
    }

    /**
     * @Given /^I have these services$/
     */
    public function iHaveTheseServices(PyStringNode $string)
    {
        eval($string->getRaw());
    }

    /**
     * @Given /^I have a container definition file$/
     */
    public function iHaveAContainerDefinitionFile(PyStringNode $string)
    {
        $this->containerXmlFile = tempnam(sys_get_temp_dir(), 'di-debugger-bundle');
        $data = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
%s
</container>
EOF;
        file_put_contents($this->containerXmlFile, sprintf($data, $string->getRaw()));
        $loader = new \Symfony\Component\DependencyInjection\Loader\XmlFileLoader($this->containerBuilder, new \Symfony\Component\Config\FileLocator(sys_get_temp_dir()));
        $loader->load($this->containerXmlFile);
    }


    /**
     * @Given /^I add the "([^"]*)" cheker$/
     */
    public function iAddTheCheker($class)
    {
        $className = 'Cypress\\DiDebuggerBundle\\Checker\\Checker\\'.$class;
        $this->serviceChecker->addChecker(new $className());
    }

    /**
     * @When /^I check the service "([^"]*)"$/
     */
    public function iCheckTheService($serviceName)
    {
        $this->serviceChecker->setServiceName($serviceName);
    }

    /**
     * @Then /^I should get no error$/
     */
    public function iShouldGetNoError()
    {
        $this->serviceChecker->check();
    }

    /**
     * @Then /^I should get "([^"]*)" error$/
     */
    public function iShouldGetError($exceptionName)
    {
        try {
            $this->serviceChecker->check();
        } catch (\Cypress\DiDebuggerBundle\Exception\DiDebuggerException $e) {
            if (get_class($e) === 'Cypress\\DiDebuggerBundle\\Exception\\'.$exceptionName) {
                return;
            }
            throw new \Exception(sprintf('expected %s exception, but got %s', $exceptionName, get_class($e)));
        }
        throw new \Exception(sprintf('expected error %s but got nothing', $exceptionName));
    }

    /**
     * @AfterScenario
     */
    public function clearContainerFiles()
    {
        unlink($this->containerXmlFile);
    }
}
