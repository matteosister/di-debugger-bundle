<?php

namespace spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TooFewConstructorCountArgumentsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\TooFewConstructorCountArguments');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
        $this->shouldbeAnInstanceOf('Cypress\DiDebuggerBundle\Exception\WrongConstructorCountArguments');
    }
}
