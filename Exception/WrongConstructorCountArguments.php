<?php

namespace Cypress\DiDebuggerBundle\Exception;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Exception;

class WrongConstructorCountArguments extends DiDebuggerException
{
    /**
     * @var array
     */
    protected $containerDefinedArguments;

    /**
     * @var \ReflectionParameter[]
     */
    protected $classDefinedArguments;

    /**
     * @param mixed $classDefinedArguments
     */
    public function setArguments($containerDefinedArguments, $classDefinedArguments)
    {
        $this->classDefinedArguments = $classDefinedArguments;
        $this->containerDefinedArguments = $containerDefinedArguments;

        $containerDefinedList = "\n  - ".implode("\n  - ", $this->containerDefinedArguments);
        $this->message .= sprintf(
            "\nthe container is configured to accepts <comment>%s parameters</comment>: %s"
            , count($this->containerDefinedArguments)
            , $containerDefinedList
        );

        $classDefinedArgumentsNames = array_map(function (\ReflectionParameter $parameter) {
            return sprintf($parameter->getName());
        }, $this->classDefinedArguments);
        $classDefinedArgumentsList = "\n  - ".implode("\n  - ", $classDefinedArgumentsNames);
        $this->message .= sprintf(
            "\nwhile the class accepts <comment>%s parameters</comment>: %s"
            , count($this->classDefinedArguments)
            , $classDefinedArgumentsList
        );
    }
}
