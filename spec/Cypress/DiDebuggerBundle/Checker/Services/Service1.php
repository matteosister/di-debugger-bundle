<?php

namespace spec\Cypress\DiDebuggerBundle\Checker\Services;

class Service1
{
    private $arg1;

    public function __construct($arg1)
    {
        $this->arg1 = $arg1;
    }

    public function aMethod()
    {
        return $this->arg1 * 2;
    }
}
