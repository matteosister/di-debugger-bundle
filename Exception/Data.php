<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 17/09/14
 * Time: 22.02
 */

namespace Cypress\DiDebuggerBundle\Exception;

class Data
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $factoryService;

    /**
     * @var string
     */
    private $factoryClass;

    /**
     * @var string
     */
    private $factoryMethod;

    /**
     * @var
     */
    private $otherData;

    /**
     * @param $serviceName
     * @param $class
     * @param $factoryService
     * @param $factoryClass
     * @param $factoryMethod
     */
    private function __construct($serviceName, $class, $factoryService, $factoryClass, $factoryMethod)
    {
        $this->serviceName = $serviceName;
        $this->class = $class;
        $this->factoryService = $factoryService;
        $this->factoryClass = $factoryClass;
        $this->factoryMethod = $factoryMethod;
    }

    /**
     * @param $serviceName
     * @param $class
     * @param $factoryService
     * @param $factoryClass
     * @param $factoryMethod
     * @return Data
     */
    public static function create($serviceName, $class, $factoryService, $factoryClass, $factoryMethod)
    {
        return new self($serviceName, $class, $factoryService, $factoryClass, $factoryMethod);
    }

    /**
     * @param $name
     * @param $args
     */
    public function __call($name, $args)
    {
        if ('get' === substr($name, 0, 3)) {
            // getter
            $prop = lcfirst(substr($name, 3));
            if (!array_key_exists($prop, $this->otherData)) {
                throw new \InvalidArgumentException(sprintf('There is no %s data', $prop));
            }
            return $this->otherData[$prop];
        }
        if ('set' === substr($name, 0, 3)) {
            // setter
            $prop = lcfirst(substr($name, 3));
            $this->otherData[$prop] = $args[0];
        }
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getFactoryClass()
    {
        return $this->factoryClass;
    }

    /**
     * @return string
     */
    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }

    /**
     * @return string
     */
    public function getFactoryService()
    {
        return $this->factoryService;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
} 