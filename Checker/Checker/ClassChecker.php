<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

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
        if ($this->sd->isSynthetic()) {
            return BaseChecker::BLOCK_CHECKS;
        }
        if (interface_exists($class) && is_null($factoryClass)) {
            return;
        }
        if (interface_exists($class) && class_exists($factoryClass)) {
            return;
        }
        if (! class_exists($class)) {
            $e = new NonExistentClassException();
            $e->setServiceDescriptor($this->sd);
            throw $e;
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
