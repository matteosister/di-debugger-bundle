<?php

namespace Cypress\DiDebuggerBundle\Exception;

class UnusedArgumentException extends DiDebuggerException
{
    private $unusedParameterName;

    /**
     * @param mixed $unusedParameterName
     */
    public function setUnusedParameterName($unusedParameterName)
    {
        $this->unusedParameterName = $unusedParameterName;
    }

    /**
     * @return mixed
     */
    public function getUnusedParameterName()
    {
        return $this->unusedParameterName;
    }
}
