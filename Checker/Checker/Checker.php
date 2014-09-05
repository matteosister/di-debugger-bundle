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
     * @param ServiceDescriptor $sd
     * @return void
     */
    public function check(ServiceDescriptor $sd);

    /**
     * @return int
     */
    public function getOrder();
}
