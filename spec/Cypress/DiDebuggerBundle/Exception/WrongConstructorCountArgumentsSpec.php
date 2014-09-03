<?php

namespace spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WrongConstructorCountArgumentsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\WrongConstructorCountArguments');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
    }
}
