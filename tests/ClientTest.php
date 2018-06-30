<?php

namespace LSN\ipmitool;

use Symfony\Component\Process\Process;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    public function testCommandIsExecuted()
    {
        $resulta = shell_exec('ls');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('generateBaseCommand')->andReturn([]);
        $client = new Client(new Process(''), $config);
        $resultb = $client->run(['ls']);
        $this->assertEquals($resulta, $resultb);
    }

    public function testCommandEnvIsInjected()
    {
        $resulta = shell_exec('env');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn(['foo'=>'bar']);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('generateBaseCommand')->andReturn([]);
        $client = new Client(new Process(''), $config);
        $resultb = $client->run(['env']);
        $this->assertNotEquals($resulta, $resultb);
        $this->assertNotContains('foo=bar', $resulta);
        $this->assertContains('foo=bar', $resultb);
    }

    public function testBaseCommandIsInjected()
    {
        $resulta = shell_exec('ls -lh');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('generateBaseCommand')->andReturn(['ls']);
        $client = new Client(new Process(''), $config);
        $resultb = $client->run(['-lh']);
        $this->assertEquals($resulta, $resultb);
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testProcessFailedExceptionIsThrownOnFail()
    {
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('getCwd')->andReturn('');
        $config->shouldReceive('generateBaseCommand')->andReturn(['exit']);
        $client = new Client(new Process(''), $config);
        $client->run(['1']);
    }

    public function testCWDisRespected()
    {
        $resulta = shell_exec('cd ..; ls -lh');
        $config = \Mockery::mock(Config::class);
        $config->shouldReceive('getEnvironmentVariables')->andReturn([]);
        $config->shouldReceive('generateBaseCommand')->andReturn(['ls']);
        $config->shouldReceive('getCwd')->andReturn('..');
        $client = new Client(new Process(''), $config);
        $resultb = $client->run(['-lh']);
        $this->assertEquals($resulta, $resultb);
    }
}
