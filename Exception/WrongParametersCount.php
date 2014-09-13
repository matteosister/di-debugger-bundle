<?php

namespace Cypress\DiDebuggerBundle\Exception;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Exception;

class WrongParametersCount extends DiDebuggerException
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
     * @param array $containerDefinedArguments
     * @param \ReflectionParameter[] $classDefinedArguments
     */
    public function setArguments($containerDefinedArguments, $classDefinedArguments)
    {
        $this->classDefinedArguments = $classDefinedArguments;
        $this->containerDefinedArguments = $containerDefinedArguments;

        if (0 === count($this->containerDefinedArguments)) {
            $this->message .= "\nthe container is configured to instantiate the service with <comment>0 parameters</comment>";
        } else {
            array_walk($this->containerDefinedArguments, function (&$value) {
                $value = '' !== $value ? $value : '<fg=black>empty argument</fg=black>';
            });
            $containerDefinedList = "\n  - ".implode("\n  - ", $this->containerDefinedArguments);
            $this->message .= sprintf(
                "\nthe container is configured to instantiate the service with <comment>%s parameters</comment>: %s"
                , count($this->containerDefinedArguments)
                , $containerDefinedList
            );
        }

        $classDefinedArgumentsNames = array_map(function (\ReflectionParameter $parameter) {
            return sprintf($parameter->getName());
        }, $this->classDefinedArguments);
        if (0 === count($classDefinedArgumentsNames)) {
            $this->message .= "\nwhile the class accepts <comment>0 parameters</comment>";
        } else {
            $classDefinedArgumentsList = "\n  - ".implode("\n  - ", $classDefinedArgumentsNames);
            $this->message .= sprintf(
                "\nwhile the class accepts <comment>%s parameters</comment>: %s"
                , count($this->classDefinedArguments)
                , $classDefinedArgumentsList
            );
        }
    }
}
