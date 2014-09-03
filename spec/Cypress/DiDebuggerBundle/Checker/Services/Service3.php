<?php

namespace spec\Cypress\DiDebuggerBundle\Checker\Services;

class Service3
{
    private $arg1;
    private $arg2;

    public function __construct($arg1, $arg2)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function aMethod()
    {
        return $this->arg1 * 2;
    }
}
