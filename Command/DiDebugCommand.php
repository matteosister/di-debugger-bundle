<?php

namespace Cypress\DiDebuggerBundle\Command;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Service;
use Cypress\DiDebuggerBundle\Checker\ServiceCollection;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
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
        $output->writeln('<info>Debugging container</info>');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceIds = null;
        if ($serviceName = $input->getArgument('service_name')) {
            $serviceIds = [$serviceName];
        }
        $serviceCollection = new ServiceCollection($this->getContainer(), $serviceIds);
        $serviceCollection->addChecker(new ClassChecker());
        $serviceCollection->addChecker(new ArgumentsCountChecker());
        $errors = $serviceCollection->check();
        /** @var DiDebuggerException $error */
        foreach ($errors as $i => $error) {
            $output->writeln(
                str_replace(
                    DiDebuggerException::SEPARATOR,
                    DiDebuggerException::SEPARATOR.' error <comment>'.($i + 1).'</comment>',
                    $error->getMessage()
                )
            );
        }
        $output->writeln('');
        $output->writeln(DiDebuggerException::SEPARATOR);
        $output->writeln(sprintf('<info>%s</info> errors found', count($errors)));
    }
} 