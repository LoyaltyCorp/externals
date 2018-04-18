<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Logger;

use EoneoPay\Externals\Logger\Logger;
use Monolog\Logger as Monolog;
use Tests\EoneoPay\Externals\Logger\Stubs\ArrayHandlerStub;
use Tests\EoneoPay\Externals\Logger\Stubs\ThrowExceptionHandlerStub;
use Tests\EoneoPay\Externals\TestCase;

class LoggerTest extends TestCase
{
    /**
     * Test logger create right logs for all bool methods.
     *
     * @return void
     */
    public function testBooleanMethods(): void
    {
        $handler = new ArrayHandlerStub(Monolog::DEBUG, true);
        $logger = new Logger(null, $handler);
        $message = 'my message';
        $context = ['attr' => 'value'];

        foreach (['debug', 'error', 'info', 'notice', 'warning'] as $method) {
            self::assertTrue($logger->$method($message, $context));
        }

        foreach ($handler->getLogs() as $log) {
            self::assertArrayHasKey('message', $log);
            self::assertArrayHasKey('context', $log);
            self::assertEquals($message, $log['message']);
            self::assertEquals($context, $log['context']);
        }
    }

    /**
     * Test logger create right log for exception.
     *
     * @return void
     */
    public function testLogException(): void
    {
        $handler = new ArrayHandlerStub(Monolog::DEBUG, true);
        $logger = new Logger(null, $handler);
        $message = 'my message';

        $logger->exception(new \Exception($message));
        $logs = $handler->getLogs();

        self::assertCount(1, $logs);

        $log = \reset($logs);

        self::assertArrayHasKey('message', $log);
        self::assertEquals(\sprintf('Exception caught: %s', $message), $log['message']);
    }

    /**
     * Test logger returns false if Monolog throws an exception
     *
     * @return void
     */
    public function testLoggerReturnsFalseWhenMonologExceptionThrown(): void
    {
        self::assertFalse(
            (new Logger(null, new ThrowExceptionHandlerStub(Monolog::DEBUG, true)))->error('message')
        );
    }
}
