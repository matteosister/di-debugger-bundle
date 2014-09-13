<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 03/09/14
 * Time: 0.16
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;

interface Checker
{
    /**
     * @return void
     */
    public function check();

    /**
     * @return int
     */
    public function getOrder();

    /**
     * @param ServiceDescriptor $serviceDescriptor
     */
    public function setServiceDescriptor(ServiceDescriptor $serviceDescriptor);
}
