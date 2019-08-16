<?php

namespace LoxBerry\Tests\Utility;

use LoxBerry\Utility\LowLevelExecutor;
use PHPUnit\Framework\TestCase;

/**
 * Class LowLevelTest.
 */
class LowLevelExecutorTest extends TestCase
{
    public function testEnvironmentVariablesAreFetchedFromEnv()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel
            ->expects($this->once())
            ->method('execLowLevelFunction')
            ->with('getenv', 'test')
            ->willReturn('something');

        $this->assertEquals('something', $lowLevel->getEnvironmentVariable('test'));
    }

    public function testUserInfoIsProperlyRead()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel
            ->expects($this->at(0))
            ->method('execLowLevelFunction')
            ->with('posix_getpwnam', 'testfile')
            ->willReturn('bar');
        $lowLevel
            ->expects($this->at(1))
            ->method('execLowLevelFunction')
            ->with('posix_getpwuid', 'bar')
            ->willReturn('foo');

        $this->assertEquals('foo', $lowLevel->getUserInfo('testfile'));
    }

    public function testErrorLogIsPassed()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel
            ->expects($this->once())
            ->method('execLowLevelFunction')
            ->with('error_log', 'test')
            ->willReturn(false);

        $this->assertEquals(false, $lowLevel->errorLog('test'));
    }

    public function testFileOwnerIsProperlyRead()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel
            ->expects($this->at(0))
            ->method('execLowLevelFunction')
            ->with('fileowner', 'testfile')
            ->willReturn('bar');
        $lowLevel
            ->expects($this->at(1))
            ->method('execLowLevelFunction')
            ->with('posix_getpwuid', 'bar')
            ->willReturn(['name' => 'foo']);

        $this->assertEquals('foo', $lowLevel->getFileOwner('testfile'));
    }

    public function testFileOwnerCanBeSet()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel
            ->expects($this->once())
            ->method('execLowLevelFunction')
            ->with('chown', ['test', 'owner'])
            ->willReturn(true);

        $this->assertEquals(true, $lowLevel->setFileOwner('test', 'owner'));
    }

    public function testFwriteIsCalledProperly()
    {
        $lowLevel = $this->createPartialMock(LowLevelExecutor::class, ['execLowLevelFunction']);
        $lowLevel->expects($this->once())
            ->method('execLowLevelFunction')
            ->with('fwrite', [STDERR, 'testmessage', null])
            ->willReturn(true);

        $lowLevel->fwrite(STDERR, 'testmessage');
    }

    public function testLowLevelExecutionRunsNativeFunctions()
    {
        $lowLevel = new LowLevelExecutor();
        $this->assertEquals('TEST', $lowLevel->execLowLevelFunction('strtoupper', 'test'));
        $this->assertEquals(36, $lowLevel->execLowLevelFunction('pow', [6, 2]));
        $this->assertEquals('foo,bar', $lowLevel->execLowLevelFunction('implode', [',', ['foo', 'bar']]));
    }
}
