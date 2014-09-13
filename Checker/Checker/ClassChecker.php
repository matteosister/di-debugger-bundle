<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;

class ClassChecker extends BaseChecker implements Checker
{
    /**
     * @throws NonExistentClassException
     */
    public function check()
    {
        if ($this->sd->isAlias()) {
            return;
        }
        $class = $this->getRealClassName($this->sd->getDefinition()->getClass());
        $factoryClass = $this->getRealClassName($this->sd->getDefinition()->getFactoryClass());
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
                sprintf('the class %s for the service %s does not exists', $class, $this->sd->getServiceName())
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
