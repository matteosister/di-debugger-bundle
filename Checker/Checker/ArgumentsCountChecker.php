<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 03/09/14
 * Time: 23.28
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryClassException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryMethodException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryServiceMethodException;
use Cypress\DiDebuggerBundle\Exception\TooFewConstructorCountArguments;
use Cypress\DiDebuggerBundle\Exception\TooFewParameters;
use Cypress\DiDebuggerBundle\Exception\TooManyConstructorCountArguments;
use Cypress\DiDebuggerBundle\Exception\TooManyParameters;

class ArgumentsCountChecker extends BaseChecker implements Checker
{
    /**
     * @throws NonExistentFactoryMethodException
     * @throws TooFewParameters
     * @throws TooManyParameters
     * @return void
     */
    public function check()
    {
        if ($this->sd->isAlias()) {
            return;
        }
        $definition = $this->sd->getDefinition();
        $class = $this->getRealClassName($definition->getClass());
        $reflection = new \ReflectionClass($class);
        if (($factoryClass = $definition->getFactoryClass()) !== null) {
            $factoryClass = $this->getRealClassName($factoryClass);
            $this->checkFactoryClass($this->sd, $factoryClass);
            return;
        }
        if (($factoryService = $definition->getFactoryService()) !== null) {
            $factoryService = $this->sd->getContainer()->get($factoryService);
            $this->checkFactoryService($this->sd, $factoryService);
            return;
        }
        $constructor = $reflection->getConstructor();
        if (is_null($constructor) && 0 === count($definition->getArguments())) {
            return;
        }
        if (is_null($constructor)) {
            $this->compare($this->sd, $definition->getArguments(), array());
        } else {
            $this->compare($this->sd, $definition->getArguments(), $constructor->getParameters());
        }
    }

    /**
     * @param ServiceDescriptor $sd
     * @param $factoryClass
     * @throws NonExistentFactoryMethodException
     */
    private function checkFactoryClass(ServiceDescriptor $sd, $factoryClass)
    {
        $definition = $sd->getDefinition();
        $reflection = new \ReflectionClass($factoryClass);
        $factoryMethod = $definition->getFactoryMethod();
        if (! $reflection->hasMethod($factoryMethod)) {
            $e = new NonExistentFactoryMethodException();
            $e->setServiceDescriptor($sd);
            throw $e;
        }
        $method = $reflection->getMethod($factoryMethod);
        $this->compare($this->sd, $definition->getArguments(), $method->getParameters());
    }

    /**
     * @param ServiceDescriptor $sd
     * @param $factoryService
     * @throws NonExistentFactoryServiceMethodException
     */
    private function checkFactoryService(ServiceDescriptor $sd, $factoryService)
    {
        $definition = $sd->getDefinition();
        $reflection = new \ReflectionClass($factoryService);
        $factoryMethod = $definition->getFactoryMethod();
        if (! $reflection->hasMethod($factoryMethod)) {
            $e = new NonExistentFactoryServiceMethodException();
            $e->setServiceDescriptor($sd);
            throw $e;
        }
        $method = $reflection->getMethod($factoryMethod);
        $this->compare($sd, $definition->getArguments(), $method->getParameters());
    }

    /**
     * @param ServiceDescriptor $sd
     * @param array $definitionArguments
     * @param \ReflectionParameter[] $methodParameters
     * @throws TooFewParameters
     * @throws TooManyParameters
     */
    private function compare(ServiceDescriptor $sd, $definitionArguments, $methodParameters) {
        $min = array_reduce($methodParameters, function ($min, \ReflectionParameter $parameter) {
            return $parameter->isOptional() ? $min : $min + 1;
        });
        if (count($definitionArguments) > count($methodParameters)) {
            $e = new TooManyParameters();
            $e->setServiceDescriptor($sd);
            $e->setArguments($definitionArguments, $methodParameters);
            throw $e;
        }
        if (count($definitionArguments) < $min) {
            $e = new TooFewParameters();
            $e->setServiceDescriptor($sd);
            $e->setArguments($definitionArguments, $methodParameters);
            throw $e;
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
