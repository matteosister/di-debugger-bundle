<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 03/09/14
 * Time: 23.28
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\TooFewConstructorCountArguments;
use Cypress\DiDebuggerBundle\Exception\TooManyConstructorCountArguments;

class ArgumentsCountChecker implements Checker
{
    /**
     * @param ServiceDescriptor $serviceDescriptor
     * @throws TooFewConstructorCountArguments
     * @throws TooManyConstructorCountArguments
     * @return void
     */
    public function check(ServiceDescriptor $serviceDescriptor)
    {
        $definition = $serviceDescriptor->getDefinition();
        $reflection = new \ReflectionClass($definition->getClass());
        $constructor = $reflection->getConstructor();
        $constructorParametersCount = count($constructor->getParameters());
        $definitionArgumentsCount = count($definition->getArguments());
        if (is_null($constructor) && 0 === count($definition->getArguments())) {
            return;
        }
        if ($constructorParametersCount > $definitionArgumentsCount) {
            throw new TooFewConstructorCountArguments;
        }
        if ($constructorParametersCount < $definitionArgumentsCount) {
            throw new TooManyConstructorCountArguments;
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }
}
