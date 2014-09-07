<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 07/09/14
 * Time: 23.40
 */

namespace Test\CypressLab\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;

class ArgumentsCountCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function test_compare()
    {
        $checker = new ArgumentsCountChecker();
        $this->assertTrue(true);
    }
} 