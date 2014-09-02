<?php

namespace spec\Cypress\DiDebuggerBundle\Checker;

use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ExistenceChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ServiceSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->addChecker(new ClassChecker());
        $this->addChecker(new ExistenceChecker());
        $container->getParameter('debug.container.dump')->willReturn(__DIR__.'/Resources/container.xml');
        $container->has('non_existent')->willReturn(false);
        $container->has(Argument::any())->willReturn(true);
        $this->setContainer($container);
        $this->setServiceName('non_existent');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Checker\Service');
    }

    function it_implements_ServiceDescriptor()
    {
        $this->shouldHaveType('Cypress\DiDebuggerBundle\Checker\ServiceDescriptor');
    }

    function it_has_a_setContainer_method(ContainerInterface $container)
    {
        $this->setContainer($container)->shouldReturn($this);
    }

    function it_should_throw_an_exception_with_non_existing_service(ContainerInterface $container)
    {
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\NonExistentServiceException')
            ->duringCheck();
    }

    function it_should_throw_an_exception_with_non_existing_class(ContainerInterface $container)
    {
        $this->setServiceName('wrong_class');
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\NonExistentClassException')
            ->duringCheck();
    }
}
