<?php

namespace LSN\ipmitool;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testCommandIsExecuted()
    {
        $resulta = shell_exec('ls');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('getTimeout')->andReturn(0);
        $config->shouldReceive('generateBaseCommand')->andReturn([]);
        $client = new Client($config);
        $resultb = $client->run(['ls']);
        $this->assertEquals($resulta, $resultb);
    }

    public function testCommandEnvIsInjected()
    {
        $resulta = shell_exec('env');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn(['FOO'=>'bar']);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('getTimeout')->andReturn(0);
        $config->shouldReceive('generateBaseCommand')->andReturn([]);
        $client = new Client($config);
        $resultb = $client->run(['env']);
        $this->assertNotEquals($resulta, $resultb);
        $this->assertStringNotContainsString('FOO=bar', $resulta);
        $this->assertStringContainsString('FOO=bar', $resultb);
    }

    public function testBaseCommandIsInjected()
    {
        $resulta = shell_exec('ls -lh');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('getTimeout')->andReturn(0);
        $config->shouldReceive('generateBaseCommand')->andReturn(['ls']);
        $client = new Client($config);
        $resultb = $client->run(['-lh']);
        $this->assertEquals($resulta, $resultb);
    }

    public function testProcessFailedExceptionIsThrownOnFail()
    {
        $this->expectException(ProcessFailedException::class);
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('getTimeout')->andReturn(0);
        $config->shouldReceive('generateBaseCommand')->andReturn(['exit']);
        $client = new Client($config);
        $client->run(['1']);
    }

    public function testCWDisRespected()
    {
        $resulta = shell_exec('cd ..; ls -lh');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('generateBaseCommand')->andReturn(['ls']);
        $config->shouldReceive('getCwd')->andReturn('..');
        $config->shouldReceive('getTimeout')->andReturn(0);
        $client = new Client($config);
        $resultb = $client->run(['-lh']);
        $this->assertEquals($resulta, $resultb);
    }

    public function testTimeoutisRespected()
    {
        $resulta = shell_exec('cd ..; ls -lh');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('generateBaseCommand')->andReturn(['ls']);
        $config->shouldReceive('getCwd')->andReturn('..');
        $config->shouldReceive('getTimeout')->andReturn(1);
        $client = new Client($config);
        $resultb = $client->run(['-lh']);
        $this->assertEquals($resulta, $resultb);
        $process = $client->getProcess();
        $this->assertEquals(1, $process->getTimeout());
    }
}
