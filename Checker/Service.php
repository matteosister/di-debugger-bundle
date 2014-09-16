<?php

namespace Cypress\DiDebuggerBundle\Checker;

use Cypress\DiDebuggerBundle\Checker\Checker\BaseChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\Checker;
use PhpCollection\Sequence;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Service implements ServiceDescriptor
{
    /**
     * @var Sequence
     */
    private $checkers;

    /**
     * @var string
     */
    public $serviceName;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->checkers = new Sequence();
    }

    /**
     * @param $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    public function setContainerBuilder($containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param Checker $checker
     */
    public function addChecker(Checker $checker)
    {
        $this->checkers->add($checker);
        $this->checkers->sortWith($this->checkersSorter());
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainerBuilder()
    {
        if (is_null($this->containerBuilder)) {
            if (!is_file($cachedFile = $this->container->getParameter('debug.container.dump'))) {
                throw new \LogicException(
                    'Debug information about the container could not be found. Please clear the cache and retry.'
                );
            }
            $this->containerBuilder = new ContainerBuilder();
            $loader = new XmlFileLoader($this->containerBuilder, new FileLocator());
            $loader->load($cachedFile);
        }
        return $this->containerBuilder;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    public function getDefinition()
    {
        return $this->getContainerBuilder()->getDefinition($this->serviceName);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->container->has($this->serviceName);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return bool
     */
    public function isAlias()
    {
        return $this->getContainerBuilder()->hasAlias($this->getServiceName());
    }

    /**
     * @return bool
     */
    public function isSynthetic()
    {
        return $this->getDefinition()->isSynthetic();
    }

    /**
     * do the check
     */
    public function check()
    {
        /** @var Checker $checker */
        foreach ($this->checkers as $checker) {
            $checker->setServiceDescriptor($this);
            if (BaseChecker::BLOCK_CHECKS === $checker->check()) {
                break;
            }
        }
    }

    /**
     * @return \Closure
     */
    public function checkersSorter()
    {
        return function (Checker $a, Checker $b) {
            return $a->getOrder() < $b->getOrder() ? -1 : 1;
        };
    }
}
