<?php

namespace Cypress\DiDebuggerBundle\Exception;

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
     * @var
     */
    protected $difference;

    /**
     * @param array $containerDefinedArguments
     * @param \ReflectionParameter[] $classDefinedArguments
     */
    public function setArguments($containerDefinedArguments, $classDefinedArguments)
    {
        $this->classDefinedArguments = $classDefinedArguments;
        $this->containerDefinedArguments = $containerDefinedArguments;
        $this->difference = abs(count($this->classDefinedArguments) - count($this->containerDefinedArguments));
    }

    public function getData()
    {
        $data = parent::getData();
        $data->setContainerDefinedArguments($this->containerDefinedArguments);
        $data->setClassDefinedArguments($this->classDefinedArguments);
        $data->setDifference($this->difference);
        return $data;
    }
}
