<?php

namespace Cypress\DiDebuggerBundle\Command;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Service;
use Cypress\DiDebuggerBundle\Checker\ServiceCollection;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryClassException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryMethodException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryServiceMethodException;
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
    const EMPTY_PARAM = '<error>no parameters</error>';
    const EMPTY_ARGUMENT = '<fg=black>empty string</fg=black>';

    protected function configure()
    {
        $this
            ->setName('cypress:di:debug')
            ->addArgument('service_name', InputArgument::OPTIONAL, 'check only for the given service');
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
            $serviceIds = array($serviceName);
        }
        $serviceCollection = new ServiceCollection($this->getContainer(), $serviceIds);
        $serviceCollection->addChecker(new ClassChecker());
        $serviceCollection->addChecker(new ArgumentsCountChecker());
        $errors = $serviceCollection->check();
        /** @var TableHelper $table */
        $table = $this->getHelper('table');
        $table->setCrossingChar('<fg=red>.</fg=red>');
        $table->setHorizontalBorderChar('<fg=black>.</fg=black>');
        $table->setVerticalBorderChar('');
        $table->setCellHeaderFormat('<fg=blue>%s</fg=blue>');
        $solution = null;
        /** @var DiDebuggerException $error */
        foreach ($errors as $i => $error) {
            $data = $error->getData();
            $output->writeln(sprintf('<fg=white>SERVICE: <error>%s</error></fg=white>', $data->getServiceName()));
            if ($error instanceof WrongParametersCount) {
                $table->setHeaders(array('container defined arguments', 'service parameters'));
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
                if ($error instanceof TooManyParameters) {
                    $solution = sprintf('Change the service definition, remove %s arguments', $data->getDifference());
                }
                if ($error instanceof TooFewParameters) {
                    $solution = sprintf('Change the service definition, add %s arguments', $data->getDifference());
                }
            }
            if ($error instanceof NonExistentClassException) {
                $solution = 'The class do not exists. Create it!';
                $output->writeln(sprintf('Class: <comment>%s</comment>', $data->getClass()));
            }
            if ($error instanceof NonExistentFactoryClassException) {
                $solution = 'The factory class do not exists. Create it!';
                $output->writeln(sprintf('Factory Class: <comment>%s</comment>', $data->getFactoryClass()));
            }
            if ($error instanceof NonExistentFactoryMethodException) {
                $solution = 'The method defined for the factory class do not exists. Implement it!';
                $output->writeln(sprintf(
                    'Factory Method: <comment>%s::%s</comment>',
                    $data->getFactoryClass(),
                    $data->getFactoryMethod()
                ));
            }
            if ($error instanceof NonExistentFactoryServiceMethodException) {
                $solution = 'The method defined for the factory service do not exists. Implement it!';
                $output->writeln(sprintf('Factory Service: <comment>%s</comment>', $data->getFactoryService()));
                $output->writeln(sprintf('Factory Method: <comment>%s</comment>', $data->getFactoryMethod()));
            }

            if (!is_null($solution)) {
                $output->writeln(sprintf('<comment>Solution</comment>: <info>%s</info>', $solution));
            }
            $output->writeln('');
            $output->writeln('');
        }
        if (count($errors)) {
            $output->writeln(sprintf('<info>%s</info> problems found', count($errors)));
        } else {
            $output->writeln(sprintf('<info>%s</info> problems found. <info>Well done!</info>', count($errors)));
        }
    }
} 