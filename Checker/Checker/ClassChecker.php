<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;

class ClassChecker implements Checker
{
    /**
     * @param ServiceDescriptor $serviceDescriptor
     * @throws NonExistentClassException
     */
    public function check(ServiceDescriptor $serviceDescriptor)
    {
        if ($serviceDescriptor->getContainerBuilder()->hasAlias($serviceDescriptor->getServiceName())) {
            return;
        }
        $definition = $serviceDescriptor->getDefinition();
        $class = $serviceDescriptor->getDefinition()->getClass();
        if ($this->isParameter($class)) {
            $class = $serviceDescriptor->getContainer()->getParameter(trim($class, '%'));
        }
        $factoryClass = $serviceDescriptor->getDefinition()->getFactoryClass();
        if ($this->isParameter($factoryClass)) {
            $factoryClass = $serviceDescriptor->getContainer()->getParameter(trim($factoryClass, '%'));
        }
        if (is_null($class) && is_null($factoryClass)) {
            return;
        }
        if (interface_exists($class) && is_null($factoryClass)) {
            return;
        }
        if (interface_exists($class) && class_exists($factoryClass)) {
            return;
        }
        if (! class_exists($class)) {
            var_dump($definition);die;
            throw new NonExistentClassException(
                sprintf('the class %s for the service %s does not exists', $class, $serviceDescriptor->getServiceName())
            );
        }
    }

    /**
     * @param $name
     * @return int
     */
    public function isParameter($name)
    {
        if (false !== preg_match('/%.+%/', $name, $matches)) {
            return 1 == count($matches);
        }
        throw new \RuntimeException('There was an error finding out if %s is a parameter. This should be reported');
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 10;
    }
}
