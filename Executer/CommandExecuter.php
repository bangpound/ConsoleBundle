<?php

/*
 * This file is part of the CoreSphereConsoleBundle.
 *
 * (c) Laszlo Korte <me@laszlokorte.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreSphere\ConsoleBundle\Executer;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\HttpKernel\KernelInterface;

use Symfony\Bundle\FrameworkBundle\Console\Application;

use Symfony\Component\Console\Output\BufferedOutput;
use CoreSphere\ConsoleBundle\Formatter\HtmlOutputFormatterDecorator;

/**
 * CommandExecuter
 *
 * Takes a string to execute as console command.
 */
class CommandExecuter
{
    protected $baseKernel;

    public function __construct(KernelInterface $baseKernel)
    {
        $this->baseKernel = $baseKernel;
    }

    public function execute($commandString)
    {
        $input = new StringInput($commandString);
        $output = new BufferedOutput();

        $application = $this->getApplication($input);
        $formatter = $output->getFormatter();
        $kernel = $application->getKernel();

        chdir($kernel->getRootDir().'/..');

        $input->setInteractive(false);
        $formatter->setDecorated(true);
        $output->setFormatter(new HtmlOutputFormatterDecorator($formatter));
        $application->setAutoExit(false);

        ob_start();
        $errorCode = $application->run($input, $output);
        $result = $output->fetch();
        if (empty($result)) {
            $result = ob_get_contents();
        }
        ob_end_clean();

        return array(
            'input'       => $commandString,
            'output'      => $result,
            'environment' => $kernel->getEnvironment(),
            'error_code'  => $errorCode
        );
    }

    protected function getApplication($input = null)
    {
        $kernel = $this->getKernel($input);

        return new Application($kernel);
    }

    protected function getKernel($input = null)
    {
        if($input === null) {
            return $this->baseKernel;
        }

        $env = $input->getParameterOption(array('--env', '-e'), $this->baseKernel->getEnvironment());
        $debug = !$input->hasParameterOption(array('--no-debug', ''));

        if($this->baseKernel->getEnvironment() === $env && $this->baseKernel->isDebug() === $debug) {
            return $this->baseKernel;
        }

        $kernelClass = new \ReflectionClass($this->baseKernel);

        return $kernelClass->newInstance($env, $debug);
    }

}
