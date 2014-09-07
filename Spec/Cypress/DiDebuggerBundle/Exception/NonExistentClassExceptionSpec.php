<?php

namespace Spec\Cypress\DiDebuggerBundle\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NonExistentClassExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Exception\NonExistentClassException');
    }

    function it_extends_exception()
    {
        $this->shouldbeAnInstanceOf('\Exception');
    }
}
