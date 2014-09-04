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

    function it_should_find_a_param()
    {
        $this->isParameter('%ss%')->shouldReturn(true);
        $this->isParameter('%s.s%')->shouldReturn(true);
        $this->isParameter('%s.s.s%')->shouldReturn(true);
        $this->isParameter('ss%')->shouldReturn(false);
        $this->isParameter('')->shouldReturn(false);
        $this->isParameter('%%')->shouldReturn(false);
    }
}
