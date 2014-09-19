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
    }

    /**
     * @return Data
     */
    public function getData()
    {
        return $this->getBaseData();
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

    /**
     * @return string
     */
    public function getClass()
    {
        $refl = new \ReflectionClass($this);
        return $refl->getShortName();
    }
}