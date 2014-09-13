<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 06/09/14
 * Time: 0.01
 */

namespace Cypress\DiDebuggerBundle\Exception;


use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Exception;

class DiDebuggerException extends \Exception
{
    const SEPARATOR = '--------';

    protected $serviceName;
    protected $class;
    protected $factoryService;
    protected $factoryClass;
    protected $factoryMethod;

    public function setServiceDescriptor(ServiceDescriptor $sd)
    {
        $this->serviceName = $sd->getServiceName();
        $this->class = $sd->getDefinition()->getClass();
        $this->factoryService = $sd->getDefinition()->getFactoryService();
        $this->factoryClass = $sd->getDefinition()->getFactoryClass();
        $this->factoryMethod = $sd->getDefinition()->getFactoryMethod();
        $this->message = sprintf("\n%s\nProblem found in service: <info>%s</info>", self::SEPARATOR, $this->serviceName);
        if ($this->class != null) {
            $this->message .= sprintf("\nclass: <comment>%s</comment>", $this->class);
        }
    }
} 