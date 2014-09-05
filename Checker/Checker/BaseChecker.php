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
}
