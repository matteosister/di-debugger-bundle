<?php

namespace spec\Cypress\DiDebuggerBundle\Checker\Services;

class Service2
{
    private $arg1;

    public function __construct($arg1)
    {
        $this->arg1 = $arg1;
    }
}
