<?php

namespace Cypress\DiDebuggerBundle\Checker;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;

interface ServiceDescriptor
{
    /**
     * @return string
     */
    public function getServiceName();

    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * @return ContainerBuilder
     */
    public function getContainerBuilder();

    /**
     * @return Definition
     */
    public function getDefinition();

    /**
     * @return bool
     */
    public function exists();
}
