<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 03/09/14
 * Time: 23.52
 */

namespace Cypress\DiDebuggerBundle\Checker\Checker;

use Cypress\DiDebuggerBundle\Exception\UnusedArgumentException;

class UnusedArgumentChecker extends BaseChecker implements Checker
{
    /**
     * @var string
     */
    private $fileContent;

    /**
     * @throws UnusedArgumentException
     * @return void
     */
    public function check()
    {
        if ($this->sd->isAlias() || $this->sd->isSynthetic()) {
            return;
        }
        $className = $this->getRealClassName($this->sd->getDefinition()->getClass());
        $refl = new \ReflectionClass($className);
        $filename = $refl->getFileName();
        if (false === $filename) {
            return;
        }
        $this->fileContent = file_get_contents($filename);
        $constr = $refl->getConstructor();
        if (is_null($constr)) {
            return;
        }
        /** @var \ReflectionParameter $param */
        foreach ($constr->getParameters() as $param) {
            if ($this->isNotUsed($param->getName())) {
                $e = new UnusedArgumentException();
                $e->setServiceDescriptor($this->sd);
                throw $e;
            }
        }
    }

    /**
     * @param $argName
     * @return bool
     */
    private function isNotUsed($argName)
    {
        $count = preg_match_all(
            preg_quote("#\$this->$argName#"),
            $this->fileContent,
            $matches
        );
        return $count < 2;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 40;
    }
}
