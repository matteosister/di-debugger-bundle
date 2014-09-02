<?php

namespace spec\Cypress\DiDebuggerBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DiDebugCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Command\DiDebugCommand');
    }
}
