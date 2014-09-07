<?php

namespace spec\Cypress\DiDebuggerBundle\Checker;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ExistenceChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\UnusedArgumentChecker;
use Cypress\DiDebuggerBundle\Exception\UnusedArgument;
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
        $this->addChecker(new ArgumentsCountChecker());
        $this->addChecker(new UnusedArgumentChecker());
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

    function it_should_throw_an_exception_with_wrong_constructor_arguments_count()
    {
        $this->setServiceName('service1');
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\WrongParametersCount')
            ->duringCheck();
    }

    function it_should_work_with_correct_constructor_arguments()
    {
        $this->setServiceName('service2');
        $this->check()->shouldReturn(null);
    }

    function it_should_throw_an_exception_with_more_constructor_arguments_count()
    {
        $this->setServiceName('service3');
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\TooManyParameters')
            ->duringCheck();
    }

    function it_should_throw_an_exception_with_less_constructor_arguments_count()
    {
        $this->setServiceName('service4');
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\TooFewParameters')
            ->duringCheck();
    }

    function it_should_throw_an_exception_if_the_argument_is_not_used()
    {
        $this->setServiceName('service5');
        $this
            ->shouldThrow('Cypress\DiDebuggerBundle\Exception\UnusedArgument')
            ->duringCheck();
    }

    function it_should_throw_an_exception_if_one_argument_is_not_used()
    {
        $this->setServiceName('service6');
        $this
            ->shouldThrow(new UnusedArgument('arg2'))
            ->duringCheck();
    }
}
