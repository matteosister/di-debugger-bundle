<?php

namespace Cypress\DiDebuggerBundle\Exception;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;

class TooFewParameters extends WrongParametersCount
{
    public function setServiceDescriptor(ServiceDescriptor $sd)
    {
        parent::setServiceDescriptor($sd);
        $this->message .= "\n<error>Too few parameters configured in the container</error>";
    }
}
