<?php

namespace Spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TooManyParametersExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\TooManyParametersException');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
        $this->shouldbeAnInstanceOf('Cypress\DiDebuggerBundle\Exception\WrongParametersCountException');
    }
}
