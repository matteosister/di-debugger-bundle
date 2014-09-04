<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentServiceException;

class ExistenceChecker implements Checker
{
    /**
     * @param ServiceDescriptor $serviceDescriptor
     * @throws NonExistentServiceException
     */
    public function check(ServiceDescriptor $serviceDescriptor)
    {
        if (! $serviceDescriptor->exists()) {
            throw new NonExistentServiceException(
                sprintf('the service %s do not exists', $serviceDescriptor->getServiceName())
            );
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}
