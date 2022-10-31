<?php

namespace LSN\ipmitool;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Client
{
    protected $process;
    protected $config;
    protected $cwd;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function run(array $command, callable $callback = null)
    {
        $config = $this->getConfig();
        $env = $config->getEnvironmentVariables();
        $command = array_merge($config->generateBaseCommand(), $command);
        $cwd = null;
        if ($config->getCwd() !== '') {
            $cwd = $config->getCwd();
        }

        $timeout = $config->getTimeout();
        $this->setProcess(new Process($command, $cwd, null, null, $timeout));
        $this->process->run($callback, $env);
        if (!$this->process->isSuccessful()) {
            throw new ProcessFailedException($this->process);
        }

        return $this->process->getOutput();
    }

    /**
     * @codeCoverageIgnore
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @param $process
     * @codeCoverageIgnore
     * @return Client
     */
    public function setProcess($process): Client
    {
        $this->process = $process;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @codeCoverageIgnore
     * @return Client
     */
    public function setConfig(Config $config): Client
    {
        $this->config = $config;
        return $this;
    }
}
