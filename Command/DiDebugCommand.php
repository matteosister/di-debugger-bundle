<?php

namespace Cypress\DiDebuggerBundle\Command;

use Cypress\DiDebuggerBundle\Checker\Checker\ArgumentsCountChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\ClassChecker;
use Cypress\DiDebuggerBundle\Checker\Checker\UnusedArgumentChecker;
use Cypress\DiDebuggerBundle\Checker\ServiceCollection;
use Cypress\DiDebuggerBundle\Exception\Data;
use Cypress\DiDebuggerBundle\Exception\DiDebuggerException;
use Cypress\DiDebuggerBundle\Exception\NonExistentClassException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryClassException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryMethodException;
use Cypress\DiDebuggerBundle\Exception\NonExistentFactoryServiceMethodException;
use Cypress\DiDebuggerBundle\Exception\TooFewParametersException;
use Cypress\DiDebuggerBundle\Exception\TooManyParametersException;
use Cypress\DiDebuggerBundle\Exception\UnusedArgumentException;
use Cypress\DiDebuggerBundle\Exception\WrongParametersCountException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DiDebugCommand extends ContainerAwareCommand
{
    const EMPTY_PARAM = '<error>no parameters</error>';
    const EMPTY_ARGUMENT = '<fg=black>empty string</fg=black>';

    protected function configure()
    {
        $this
            ->setName('cypress:di:debug')
            ->setDescription('Debug the service container')
            ->addArgument('service_name', InputArgument::OPTIONAL, 'check only for the given service')
            ->addOption(
                'pattern',
                'p',
                InputOption::VALUE_OPTIONAL,
                'pattern to match for selecting the services to check',
                '.*'
            );
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
        $serviceCollection = new ServiceCollection($this->getContainer(), $serviceIds, $input->getOption('pattern'));
        $serviceCollection->addChecker(new ClassChecker());
        $serviceCollection->addChecker(new ArgumentsCountChecker());
        //$serviceCollection->addChecker(new UnusedArgumentChecker());
        $errors = $serviceCollection->check();
        /** @var DiDebuggerException $error */
        foreach ($errors as $i => $error) {
            $data = $error->getData();
            $output->writeln(sprintf(
                '<fg=white>SERVICE:</fg=white> <fg=yellow;bg=black>%s</fg=yellow;bg=black>',
                $data->getServiceName()
            ));
            switch (true) {
                case $error instanceof WrongParametersCountException:
                    $solution = $this->handleWrongParametersCount($output, $error, $data);
                    break;
                case $error instanceof NonExistentClassException:
                    $solution = $this->handleNonExistentClassException($output, $data);
                    break;
                case $error instanceof NonExistentFactoryClassException:
                    $solution = $this->handleNonExistentFactoryClassException($output, $data);
                    break;
                case $error instanceof NonExistentFactoryMethodException:
                    $solution = $this->handleNonExistentFactoryMethodException($output, $data);
                    break;
                case $error instanceof NonExistentFactoryServiceMethodException:
                    $solution = $this->handleNonExistentFactoryServiceMethodException($output, $data);
                    break;
                case $error instanceof UnusedArgumentException:
                    $solution = $this->handleUnusedArgumentException($output, $error, $data);
                    break;
                default:
                    $solution = null;
                    break;
            }
            if (!is_null($solution)) {
                $output->writeln(sprintf('<comment>Solution</comment>: <info>%s</info>', $solution));
            }
            $output->writeln('');
            $output->writeln('');
        }
        $output->writeln(sprintf('<info>%s services analyzed</info>', count($serviceCollection)));
        if (count($errors)) {
            $tpl = count($errors) === 1 ? '<error>%s</error> problem found' : '<error>%s</error> problems found';
            $output->writeln(sprintf($tpl, count($errors)));
        } else {
            $output->writeln(sprintf('<error>%s</error> problems found. <info>Well done!</info>', count($errors)));
        }
    }

    /**
     * @param OutputInterface $output
     * @param WrongParametersCountException $error
     * @param Data $data
     * @return string
     */
    protected function handleWrongParametersCount(
        OutputInterface $output,
        WrongParametersCountException $error,
        Data $data
    ) {
        $solution = null;
        /** @var TableHelper $table */
        $table = $this->getHelper('table');
        $table->setCrossingChar('<fg=white>.</fg=white>');
        $table->setHorizontalBorderChar('<fg=black>.</fg=black>');
        $table->setVerticalBorderChar('');
        $table->setCellHeaderFormat('<fg=blue>%s</fg=blue>');
        $table->setHeaders(array('container defined arguments', 'service parameters'));
        $containerDefined = array_map(function ($name) {
            if (is_array($name)) {
                return 'array';
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
        if ($error instanceof TooManyParametersException) {
            $solution = sprintf('Change the service definition, remove %s arguments', $data->getDifference());
        }
        if ($error instanceof TooFewParametersException) {
            $solution = sprintf('Change the service definition, add %s arguments', $data->getDifference());
            return $solution;
        }
        return $solution;
    }

    /**
     * @param OutputInterface $output
     * @param Data $data
     * @return string
     */
    protected function handleNonExistentClassException(OutputInterface $output, Data $data)
    {
        $solution = 'The class do not exists. Create it!';
        $output->writeln(sprintf('Class: <comment>%s</comment>', $data->getClass()));
        return $solution;
    }

    /**
     * @param OutputInterface $output
     * @param Data $data
     * @return string
     */
    protected function handleNonExistentFactoryClassException(OutputInterface $output, Data $data)
    {
        $solution = 'The factory class do not exists. Create it!';
        $output->writeln(sprintf('Factory Class: <comment>%s</comment>', $data->getFactoryClass()));
        return $solution;
    }

    /**
     * @param OutputInterface $output
     * @param Data $data
     * @return string
     */
    protected function handleNonExistentFactoryMethodException(OutputInterface $output, Data $data)
    {
        $solution = 'The method defined for the factory class do not exists. Implement it!';
        $output->writeln(sprintf(
            'Factory Method: <comment>%s::%s</comment>',
            $data->getFactoryClass(),
            $data->getFactoryMethod()
        ));
        return $solution;
    }

    /**
     * @param OutputInterface $output
     * @param Data $data
     * @return string
     */
    protected function handleNonExistentFactoryServiceMethodException(OutputInterface $output, Data $data)
    {
        $solution = 'The method defined for the factory service do not exists. Implement it!';
        $output->writeln(sprintf('Factory Service: <comment>%s</comment>', $data->getFactoryService()));
        $output->writeln(sprintf('Factory Method: <comment>%s</comment>', $data->getFactoryMethod()));
        return $solution;
    }

    /**
     * @param OutputInterface $output
     * @param UnusedArgumentException $error
     * @param Data $data
     * @return string
     */
    protected function handleUnusedArgumentException(
        OutputInterface $output,
        UnusedArgumentException $error,
        Data $data
    ) {
        $solution = sprintf(
            'The service is correctly configured, but the <comment>%s</comment> parameter seems not used inside the class. Check it out!',
            $error->getUnusedParameterName()
        );

        return $solution;
    }
} 