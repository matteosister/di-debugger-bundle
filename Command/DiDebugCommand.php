<?php

namespace Cypress\DiDebuggerBundle\Command;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ExistenceChecker;
use Cypress\DiDebuggerBundle\Checker\Service;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiDebugCommand extends ContainerAwareCommand
{
    /**
     * @var Service
     */
    private $serviceChecker;

    protected function configure()
    {
        $this
            ->setName('cypress:di:debug')
            ->addArgument('service_name', InputArgument::OPTIONAL, 'check only for the given service');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->serviceChecker = new Service();
        $this->serviceChecker->addChecker(new ClassChecker());
        $this->serviceChecker->addChecker(new ArgumentsCountChecker());
        $this->serviceChecker->setContainer($this->getContainer());
        $output->writeln('<info>Debugging container...</info>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($serviceName = $input->getArgument('service_name')) {
            $this->serviceChecker->setServiceName($serviceName);
            $this->doCheck($output);
            return;
        }
        foreach ($this->serviceChecker->getContainerBuilder()->getServiceIds() as $serviceId) {
            //$output->writeln(sprintf('%s', $serviceId));
            $this->serviceChecker->setServiceName($serviceId);
            $this->doCheck($output);
        }
    }

    protected function doCheck(OutputInterface $output)
    {
        try {
            $this->serviceChecker->check();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
} 