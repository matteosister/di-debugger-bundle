<?php

namespace Cypress\DiDebuggerBundle\Exception;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;

class TooManyParameters extends WrongParametersCount
{
    public function setServiceDescriptor(ServiceDescriptor $sd)
    {
        parent::setServiceDescriptor($sd);
        $this->message .= "\n<error>Too many parameters configured in the container</error>";
    }
}
