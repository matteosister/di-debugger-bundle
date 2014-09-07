<?php

namespace Spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TooManyParametersSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\TooManyParameters');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
        $this->shouldbeAnInstanceOf('Cypress\DiDebuggerBundle\Exception\WrongParametersCount');
    }
}
