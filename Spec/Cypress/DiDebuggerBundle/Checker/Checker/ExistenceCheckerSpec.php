<?php

namespace Spec\Cypress\DiDebuggerBundle\Checker\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExistenceCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Checker\Checker\ExistenceChecker');
    }

    function it_implements_checker_interface()
    {
        $this->shouldImplement('Cypress\DiDebuggerBundle\Checker\Checker\Checker');
    }
}
