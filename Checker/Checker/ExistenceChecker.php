<?php

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentServiceException;

class ExistenceChecker extends BaseChecker implements Checker
{
    /**
     * @throws NonExistentServiceException
     */
    public function check()
    {
        if (! $this->sd->exists()) {
            /*var_dump($serviceDescriptor->getServiceName());
            var_dump($serviceDescriptor->getContainerBuilder()->get($serviceDescriptor->getServiceName()));
            die;*/
            throw new NonExistentServiceException(
                sprintf('the service %s do not exists', $this->sd->getServiceName())
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
