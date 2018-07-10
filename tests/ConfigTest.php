<?php

namespace LSN\ipmitool;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testConfigDefault()
    {
        $config = new Config(['password'=>'foobar']);
        $conf = $config->getConfig();
        $this->assertEquals('ADMIN', $conf['username']);
        $this->assertEquals('lanplus', $conf['interface']);
        $this->assertEquals(0, $conf['timeout']);
    }

    public function testConfigValidate()
    {
        $config = new Config(['foo'=>'bar']);
        $conf = $config->getConfig();
        $this->assertArrayNotHasKey('foo', $conf);
    }

    public function testConfigValidateRetainsCWD()
    {
        $config = new Config([]);
        $conf = $config->getConfig();
        $this->assertArrayHasKey('cwd', $conf);
        $this->assertEmpty($conf['cwd']);
    }

    public function testConfigValidateMutuallyExclusivePassword()
    {
        $config = new Config(['password'=>'foobar','password_env'=>true]);
        $conf = $config->getConfig();
        $this->assertArrayNotHasKey('password_env', $conf);
    }

    public function testConfigValidateMutuallyExclusivePasswordFile()
    {
        $config = new Config(['password_file'=>'foobar','password_env'=>true]);
        $conf = $config->getConfig();
        $this->assertArrayNotHasKey('password_env', $conf);
        $this->assertArrayNotHasKey('password', $conf);
        $this->assertArrayHasKey('password_file', $conf);
    }

    public function testConfigValidateMutuallyExclusivePasswordEnv()
    {
        $config = new Config(['password_env'=>true]);
        $conf = $config->getConfig();
        $this->assertArrayNotHasKey('password_file', $conf);
        $this->assertArrayNotHasKey('password', $conf);
        $this->assertArrayHasKey('password_env', $conf);
        $this->assertTrue($conf['password_env']);
    }

    public function testGetEnvironmentVariablesDoesNotReturnsPasswordEnv()
    {
        $config = new Config(['password'=>'foo']);
        $env = $config->getEnvironmentVariables();
        $this->assertArrayNotHasKey('IPMI_PASSWORD', $env);
    }

    public function testGetEnvironmentVariablesReturnsPasswordEnvWhenSet()
    {
        $config = new Config(['password_env'=>'foobar']);
        $env = $config->getEnvironmentVariables();
        $this->assertArrayHasKey('IPMI_PASSWORD', $env);
        $this->assertEquals('foobar', $env['IPMI_PASSWORD']);
    }

    public function testGetEnvironmentVariablesDoesNotReturnWhenTrue()
    {
        $config = new Config(['password_env'=>true]);
        $env = $config->getEnvironmentVariables();
        $conf = $config->getConfig();
        $this->assertArrayNotHasKey('IPMI_PASSWORD', $env);
        $this->assertArrayHasKey('password_env', $conf);
    }

    public function testGenerateBaseCommand()
    {
        $config = new Config(['password'=>'foobar']);
        $base = $config->generateBaseCommand();
        $this->assertTrue(is_array($base));
        $this->assertEquals('ipmitool', $base[0]);
        $this->assertContains(implode(' ', ['-U','ADMIN']), implode(' ', $base));
        $this->assertContains(implode(' ', ['-I','lanplus']), implode(' ', $base));
        $this->assertContains(implode(' ', ['-P','foobar']), implode(' ', $base));
    }

    public function testGenerateBaseCommandWithPasswordEnv()
    {
        $config = new Config(['password_env'=>'foobar']);
        $base = $config->generateBaseCommand();
        $this->assertTrue(is_array($base));
        $this->assertEquals('ipmitool', $base[0]);
        $this->assertContains(implode(' ', ['-U','ADMIN']), implode(' ', $base));
        $this->assertContains(implode(' ', ['-I','lanplus']), implode(' ', $base));
        $this->assertContains(implode(' ', ['-E']), implode(' ', $base));
        $this->assertNotContains(implode(' ', ['-P']), implode(' ', $base));
    }
}
