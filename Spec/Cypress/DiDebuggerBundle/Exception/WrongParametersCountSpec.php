<?php

namespace Spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WrongParametersCountSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\WrongParametersCount');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
    }
}
