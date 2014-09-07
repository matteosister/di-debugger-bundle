<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;

class ClassChecker extends BaseChecker implements Checker
{
    /**
     * @param ServiceDescriptor $sd
     * @throws NonExistentClassException
     */
    public function check(ServiceDescriptor $sd)
    {
        if ($sd->isAlias()) {
            return;
        }
        $class = $sd->getDefinition()->getClass();
        if ($this->isParameter($class)) {
            $class = $sd->getContainer()->getParameter(trim($class, '%'));
        }
        $factoryClass = $sd->getDefinition()->getFactoryClass();
        if ($this->isParameter($factoryClass)) {
            $factoryClass = $sd->getContainer()->getParameter(trim($factoryClass, '%'));
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
            throw new NonExistentClassException(
                sprintf('the class %s for the service %s does not exists', $class, $sd->getServiceName())
            );
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 10;
    }
}
