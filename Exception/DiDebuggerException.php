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

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $factoryService;

    /**
     * @var string
     */
    protected $factoryClass;

    /**
     * @var string
     */
    protected $factoryMethod;

    /**
     * @param ServiceDescriptor $sd
     */
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

    /**
     * @return Data
     */
    public function getData()
    {
        return parent::getBaseData();
    }

    /**
     * @return Data
     */
    protected function getBaseData()
    {
        return Data::create(
            $this->serviceName,
            $this->class,
            $this->factoryService,
            $this->factoryClass,
            $this->factoryMethod
        );
    }
}