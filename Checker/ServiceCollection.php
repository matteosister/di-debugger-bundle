<?php
/**
 * Created by PhpStorm.
 * User: matteo
 * Date: 14/09/14
 * Time: 0.06
 */

namespace Cypress\DiDebuggerBundle\Checker;


use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\Checker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceCollection
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
     * @param ContainerInterface $container
     * @param array $serviceIds
     */
    public function __construct(ContainerInterface $container, array $serviceIds = null)
    {
        $this->serviceChecker = new Service();
        $this->serviceChecker->setContainer($container);
        if (is_null($serviceIds)) {
            $this->serviceIds = $this->serviceChecker->getContainerBuilder()->getServiceIds();
        } else {
            $this->serviceIds = $serviceIds;
        }
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
        foreach ($this->serviceChecker->getContainerBuilder()->getServiceIds() as $serviceId) {
            $this->serviceChecker->setServiceName($serviceId);
            try {
                $this->serviceChecker->check();
            } catch (DiDebuggerException $e) {
                $exceptions[] = $e;
            }
        }
        return $exceptions;
    }
} 