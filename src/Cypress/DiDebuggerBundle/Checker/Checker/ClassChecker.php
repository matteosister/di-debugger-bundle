<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;

class ClassChecker implements Checker
{
    public function check(ServiceDescriptor $serviceDescriptor)
    {
        if (! class_exists($serviceDescriptor->getDefinition()->getClass())) {
            throw new NonExistentClassException;
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
