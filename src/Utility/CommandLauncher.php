<?php

namespace App\Utility;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Process\Process;

class CommandLauncher
{
    private $options;
    private $parameters;
    private $command;
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $parameters
     *
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    private function replacePlaceholders()
    {
        $params = array_merge($this->getOptions(), $this->getParameters());

        foreach ($params as $key => $value) {
            if (in_array($key, ['username', 'password', 'database'])) {
                $key = ':' . $key;
            }
            $this->command = str_replace($key, $value, $this->command);
        }
    }

    /**
     * @param $command
     *
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @param $command
     *
     * @return $this
     */
    public function setMysqlCommand($command)
    {
        $this->command = "mysql --user=:username --password=:password -e \"$command\";";
        $this->parameters = array_merge($this->options);

        return $this;
    }

    /**
     * @param bool $replace
     *
     * @return Process
     */
    public function exec($replace = true)
    {
        if ($replace) {
            $this->replacePlaceholders();
        }

        $process = new Process(trim($this->getCommand()));
        $process->setTimeout(5000000);
        $process->run();

        if ('' !== trim($process->getErrorOutput())) {
            $this->dispatcher->dispatch('command.error.event', new GenericEvent(trim($process->getErrorOutput())));
        }

        return $process;
    }
}
