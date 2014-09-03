<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 03/09/14
 * Time: 23.52
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Checker\ServiceDescriptor;
use Cypress\DiDebuggerBundle\Exception\UnusedArgument;

class UnusedArgumentChecker implements Checker
{
    /**
     * @var string
     */
    private $fileContent;

    /**
     * @param ServiceDescriptor $serviceDescriptor
     * @throws UnusedArgument
     * @return void
     */
    public function check(ServiceDescriptor $serviceDescriptor)
    {
        $refl = new \ReflectionClass($serviceDescriptor->getDefinition()->getClass());
        $filename = $refl->getFileName();
        if (false === $filename) {
            return;
        }
        $this->fileContent = file_get_contents($filename);
        $constr = $refl->getConstructor();
        /** @var \ReflectionParameter $param */
        foreach ($constr->getParameters() as $param) {
            if ($this->isNotUsed($param->getName())) {
                throw new UnusedArgument($param->getName());
            }
        }
    }

    /**
     * @param $argName
     * @return bool
     */
    private function isNotUsed($argName)
    {
        $regExp = "#\$this->$argName#";
        return preg_match_all(preg_quote($regExp), $this->fileContent) < 2;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 40;
    }
}
