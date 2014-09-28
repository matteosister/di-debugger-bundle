<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 14/09/14
 * Time: 0.06
 */

namespace Cypress\DiDebuggerBundle\Checker;


use Cypress\DiDebuggerBundle\Checker\Checker\Checker;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceCollection implements \Countable
{
    /**
     * @var ServiceDescriptor
     */
    private $serviceChecker;

    /**
     * @var array
     */
    private $serviceIds;

    /**
     * @var
     */
    private $pattern;

    /**
     * @param ContainerInterface $container
     * @param array $serviceIds
     * @param $pattern
     */
    public function __construct(ContainerInterface $container, array $serviceIds = null, $pattern)
    {
        $this->serviceChecker = new Service();
        $this->serviceChecker->setContainer($container);
        if (is_null($serviceIds)) {
            $this->serviceIds = $this->serviceChecker->getContainerBuilder()->getServiceIds();
        } else {
            $this->serviceIds = $serviceIds;
        }
        $this->pattern = $pattern;
    }

    /**
     * @param Checker $checker
     */
    public function addChecker(Checker $checker)
    {
        $this->serviceChecker->addChecker($checker);
    }

    /**
     * @return array
     */
    public function check()
    {
        $exceptions = array();
        foreach ($this->serviceIds as $serviceId) {
            if (0 === preg_match(sprintf('/%s/', $this->pattern), $serviceId)) {
                continue;
            }
            $this->serviceChecker->setServiceName($serviceId);
            try {
                $this->serviceChecker->check();
            } catch (DiDebuggerException $e) {
                $exceptions[] = $e;
            }
        }
        return $exceptions;
    }

    private function getFilteredServiceIds()
    {
        return array_filter($this->serviceIds, function($serviceId) {
            return 1 === preg_match(sprintf('/%s/', $this->pattern), $serviceId);
        });
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {

        return count($this->getFilteredServiceIds());
    }
}
