<?php

namespace Cypress\DiDebuggerBundle\Command;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Service;
use Cypress\DiDebuggerBundle\Checker\ServiceCollection;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
use Cypress\DiDebuggerBundle\Exception\TooFewParameters;
use Cypress\DiDebuggerBundle\Exception\TooManyParameters;
use Cypress\DiDebuggerBundle\Exception\WrongParametersCount;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiDebugCommand extends ContainerAwareCommand
{
    const EMPTY_PARAM = '<fg=black>----</fg=black>';
    const EMPTY_ARGUMENT = '<fg=black>empty string</fg=black>';

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

//    protected function initialize(InputInterface $input, OutputInterface $output)
//    {
//        $output->writeln('<info>Debugging container</info>');
//    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceIds = null;
        if ($serviceName = $input->getArgument('service_name')) {
            $serviceIds = array($serviceName);
        }
        $serviceCollection = new ServiceCollection($this->getContainer(), $serviceIds);
        $serviceCollection->addChecker(new ClassChecker());
        $serviceCollection->addChecker(new ArgumentsCountChecker());
        $errors = $serviceCollection->check();
        /** @var TableHelper $table */
        $table = $this->getHelper('table');
        /** @var DiDebuggerException $error */
        foreach ($errors as $i => $error) {
            $data = $error->getData();
            $output->writeln(sprintf('<error>%s</error>', $data->getServiceName()));
            $output->writeln(sprintf('Class: <comment>%s</comment>', $data->getClass()));
            if ($error instanceof WrongParametersCount) {
                $table->setHeaders(array('container definition arguments', 'method parameters'));
                $containerDefined = array_map(function ($name) {
                    if (is_array($name)) {
                        return 'arr';
                    }
                    return "" === $name ? self::EMPTY_ARGUMENT : $name;
                }, $data->getContainerDefinedArguments());
                $classDefined = array_map(function (\ReflectionParameter $parameter) {
                    return $parameter->getName();
                }, $data->getClassDefinedArguments());
                if (count($containerDefined) > count($classDefined)) {
                    $classDefined = array_pad($classDefined, count($containerDefined), self::EMPTY_PARAM);
                } else {
                    $containerDefined = array_pad($containerDefined, count($classDefined), self::EMPTY_PARAM);
                }
                $elementsCount = count($classDefined);
                $rows = array();
                for ($i = 0; $i < $elementsCount; $i++) {
                    $rows[] = [$containerDefined[$i], $classDefined[$i]];
                }
                $table->setRows($rows);
                $table->render($output);
            }
            $output->writeln('');
        }
        $output->writeln(sprintf('<info>%s</info> errors found', count($errors)));
    }
} 