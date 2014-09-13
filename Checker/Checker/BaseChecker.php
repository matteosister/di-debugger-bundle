<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 05/09/14
 * Time: 23.08
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseChecker
{
    const ABSTAIN = 'abstain';
    const BLOCK_CHECKS = 'block_checks';

    /**
     * @var ServiceDescriptor
     */
    protected $sd;

    /**
     * @param ServiceDescriptor $serviceDescriptor
     */
    public function setServiceDescriptor(ServiceDescriptor $serviceDescriptor)
    {
        $this->sd = $serviceDescriptor;
    }

    /**
     * @param $name
     * @return int
     */
    public function isParameter($name)
    {
        if (false !== preg_match('/%.+%/', $name, $matches)) {
            return 1 == count($matches);
        }
        throw new \RuntimeException('There was an error finding out if %s is a parameter. This should be reported');
    }

    /**
     * @param $marker
     * @return string
     */
    public function parameterName($marker)
    {
        return trim($marker, '%');
    }

    /**
     * return the arg if it's a class
     * if it's a parameter resolve the parameter name and return the class
     *
     * @param $class
     * @return mixed
     */
    public function getRealClassName($class)
    {
        if ($this->isParameter($class)) {
            $class = $this->sd->getContainer()->getParameter(trim($class, '%'));
        }
        return $class;
    }
}
