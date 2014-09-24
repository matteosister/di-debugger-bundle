<?php

namespace Cypress\DiDebuggerBundle\Exception;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;

class TooFewParametersException extends WrongParametersCountException
{
    public function setServiceDescriptor(ServiceDescriptor $sd)
    {
        parent::setServiceDescriptor($sd);
        $this->message .= "\n<error>Too few parameters configured in the container</error>";
    }
}
