<?php

namespace spec\Cypress\DiDebuggerBundle\Checker\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClassCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker');
    }

    function it_implements_checker_interface()
    {
        $this->shouldImplement('Cypress\DiDebuggerBundle\Checker\Checker\Checker');
    }
}
