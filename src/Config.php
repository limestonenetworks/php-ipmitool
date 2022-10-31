<?php

namespace LSN\ipmitool;

class Config implements \ArrayAccess
{
    protected $flags = [
        'interface'     => 'I',
        'username'      => 'U',
        'password'      => 'P',
        'hostname'      => 'H',
        'port'          => 'p',
        'password_env'  => 'E',
        'password_file' => 'f',
        'authtype'      => 'A',
        'level'         => 'L'
    ];
    private $config;
    protected $fieldWhitelist = ['binary','cwd','timeout'];

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function generateBaseCommand(): array
    {
        $flags = $this->getFlags();
        $command = [$this->getBinary()];
        $skipValue = ['password_env'];
        foreach ($this->getConfig() as $item => $value) {
            if (in_array($item, $this->fieldWhitelist)) {
                continue;
            }
            $command[] = "-{$flags[$item]}";
            if ($value !== '' && !in_array($item, $skipValue)) {
                $command[] = $value;
            }
        }
        return $command;
    }

    public function getEnvironmentVariables()
    {
        $env = [];
        if (isset($this->config['password_env']) && $this->config['password_env'] !== true) {
            $env['IPMI_PASSWORD'] = $this->config['password_env'];
        }
        return $env;
    }
    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getUsername(): string
    {
        return $this->config['username'];
    }

    /**
     * @param string $username
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setUsername($username): Config
    {
        $this->config['username'] = $username;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getPassword(): string
    {
        return $this->config['password'];
    }

    /**
     * @param string $password
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setPassword($password): Config
    {
        $this->config['password'] = $password;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getHostname(): string
    {
        return $this->config['hostname'];
    }

    /**
     * @param string $hostname
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setHostname($hostname): Config
    {
        $this->config['hostname'] = $hostname;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getInterface(): string
    {
        return $this->config['interface'];
    }

    /**
     * @param string $interface
     *
     * @codeCoverageIgnoreds
     * @return Config
     */
    public function setInterface($interface): Config
    {
        $this->config['interface'] = $interface;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getBinary(): string
    {
        return $this->config['binary'];
    }

    /**
     * @param string $binary
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setBinary($binary): Config
    {
        $this->config['binary'] = $binary;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getCwd(): string
    {
        return $this->config['cwd'];
    }

    /**
     * @param string $cwd
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setCwd($cwd): Config
    {
        $this->config['cwd'] = $cwd;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->config['timeout'];
    }

    /**
     * @param int $timeout
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setTimeout(int $timeout): Config
    {
        $this->config['timeout'] = $timeout;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return Config
     */
    public function setConfig(array $config): Config
    {
        $this->config = $this->validateConfig($this->setDefaults($config));
        return $this;
    }

    private function setDefaults(array $config): array
    {
        $defaults = [
            'username'  => 'ADMIN',
            'interface' => 'lanplus',
            'binary'    => 'ipmitool',
            'cwd'       => '',
            'timeout'   => 15
        ];

        return array_merge($defaults, $config);
    }

    /**
     * Validate that the config array only contains valid options
     *
     * @param array $config
     *
     * @return array
     */
    private function validateConfig(array $config): array
    {
        $flags = $this->getFlags();
        foreach ($config as $item => $value) {
            if (!isset($flags[$item]) && !in_array($item, $this->fieldWhitelist)) {
                unset($config[$item]);
            }
            if ($item === 'password') {
                unset($config['password_env']);
                unset($config['password_file']);
            } elseif ($item === 'password_file' && !isset($config['password'])) {
                unset($config['password']);
                unset($config['password_env']);
            } elseif ($item == 'password_env' && !isset($config['password']) && !isset($config['password_file'])) {
                unset($config['password']);
                unset($config['password_file']);
            }
        }
        return $config;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @param array $flags
     *
     * @codeCoverageIgnore
     * @return Config
     */
    public function setFlags(array $flags): Config
    {
        $this->flags = $flags;
        return $this;
    }

    /**
     * @param mixed $offset
     *
     * @codeCoverageIgnore
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @codeCoverageIgnore
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function offsetSet(mixed $offset, $value): void
    {
        $this->config[$offset] = $value;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->config[$offset]);
    }
}
