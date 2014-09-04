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
        $definition = $serviceDescriptor->getDefinition();
        $class = $serviceDescriptor->getDefinition()->getClass();
        $factoryClass = $serviceDescriptor->getDefinition()->getFactoryClass();
        if (interface_exists($class) && is_null($factoryClass)) {
            return;
        } else {
            if (! class_exists($class)) {
                if ($this->is)
                throw new NonExistentClassException(
                    sprintf('the class %s for the service %s does not exists', $class, $serviceDescriptor->getServiceName())
                );
            }
        }
    }

    /**
     * @param $name
     * @return int
     */
    public function isParameter($name)
    {
        return preg_match('/\%.*\%/', $name);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 10;
    }
}
