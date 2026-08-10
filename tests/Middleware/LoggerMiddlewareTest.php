<?php
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2026 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Tests\Jojo1981\GuzzleMiddlewares\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Exception\MalformedUriException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Jojo1981\GuzzleMiddlewares\Formatter\MessageFormatterInterface;
use Jojo1981\GuzzleMiddlewares\Middleware\LoggerMiddleware;
use Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategy\DefaultLogLevelStrategy;
use Jojo1981\GuzzleMiddlewares\Value\LogLevel;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException as PHPUnitInvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * The response is exposed on a different exception class per guzzle major version, so these tests
 * guard the failure path against all supported versions. Exceptions are built with
 * RequestException::create() because that factory has a compatible signature in guzzle 6, 7 and 8.
 *
 * @package Tests\Jojo1981\GuzzleMiddlewares\Middleware
 */
final class LoggerMiddlewareTest extends TestCase
{
    /** @var bool */
    private bool $formatterCalled = false;

    /** @var null|ResponseInterface */
    private ?ResponseInterface $capturedResponse = null;

    /** @var null|LogLevel */
    private ?LogLevel $capturedLogLevel = null;

    /** @var null|string */
    private ?string $capturedReason = null;

    /** @var array<int, array{level: string, message: string}> */
    private array $logRecords = [];

    /**
     * A failed request that carries a response must log that exact response.
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    public function testFailedRequestWithResponseLogsThatResponse(): void
    {
        $response = new Response(500, [], 'server exploded');
        $reason = RequestException::create($this->createRequest(), $response);

        $this->assertRejectedWithSameReason($reason);

        self::assertTrue($this->formatterCalled, 'The formatter should have been called');
        self::assertSame($response, $this->capturedResponse);
        self::assertSame($reason->getMessage(), $this->capturedReason);
    }

    /**
     * A failed request without a response must log a null response instead of blowing up. On guzzle
     * 8 a plain RequestException has no getResponse() method at all, which is what this covers.
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    public function testFailedRequestWithoutResponseLogsWithoutResponse(): void
    {
        $reason = RequestException::create($this->createRequest());

        $this->assertRejectedWithSameReason($reason);

        self::assertTrue($this->formatterCalled, 'The formatter should have been called');
        self::assertNull($this->capturedResponse);
    }

    /**
     * A reason that is not a guzzle exception has no response to resolve at all.
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    public function testFailedRequestWithNonGuzzleThrowableLogsWithoutResponse(): void
    {
        $reason = new RuntimeException('connection refused');

        $this->assertRejectedWithSameReason($reason);

        self::assertTrue($this->formatterCalled, 'The formatter should have been called');
        self::assertNull($this->capturedResponse);
    }

    /**
     * The formatter consumes the body while logging, so the middleware has to rewind it to keep the
     * content available to whoever handles the rejection.
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws RuntimeException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    public function testResponseBodyIsRewoundAfterLogging(): void
    {
        $response = new Response(500, [], 'server exploded');

        $this->handleRejection(RequestException::create($this->createRequest(), $response));

        self::assertSame(0, $response->getBody()->tell(), 'The body should have been rewound');
        self::assertSame('server exploded', $response->getBody()->getContents());
    }

    /**
     * A failed request is logged as a warning by the default log level strategy.
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    public function testFailedRequestIsLoggedAtWarningLevel(): void
    {
        $this->handleRejection(RequestException::create($this->createRequest(), new Response(500)));

        self::assertNotNull($this->capturedLogLevel);
        self::assertTrue($this->capturedLogLevel->isWarning());
        self::assertSame([['level' => 'warning', 'message' => 'formatted message']], $this->logRecords);
    }

    /**
     * Asserts the middleware rejects with the untouched reason. Reporting the throwable that came
     * back keeps a failure readable, because a broken response lookup surfaces here as whatever
     * error the rejection handler threw instead of as the original reason.
     *
     * @param Throwable $reason
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws AssertionFailedError
     */
    private function assertRejectedWithSameReason(Throwable $reason): void
    {
        $caught = $this->handleRejection($reason);

        self::assertSame(
            $reason,
            $caught,
            'The original reason must be passed through, got ' . $caught::class . ': ' . $caught->getMessage()
        );
    }

    /**
     * Runs the middleware against a handler that rejects with the given reason and returns the
     * throwable the resulting promise rejected with.
     *
     * @param Throwable $reason
     * @return Throwable
     * @throws MalformedUriException
     * @throws MockObjectException
     * @throws NoPreviousThrowableException
     * @throws AssertionFailedError
     * @throws PHPUnitInvalidArgumentException
     * @throws InvalidArgumentException
     */
    private function handleRejection(Throwable $reason): Throwable
    {
        $handler = static function () use ($reason): PromiseInterface {
            return new RejectedPromise($reason);
        };

        $promise = ($this->createMiddleware())($handler)($this->createRequest(), []);

        try {
            $promise->wait();
        } catch (Throwable $caught) {
            return $caught;
        }

        self::fail('Expected the returned promise to be rejected');
    }

    /**
     * @return LoggerMiddleware
     * @throws NoPreviousThrowableException
     * @throws PHPUnitInvalidArgumentException
     * @throws MockObjectException
     */
    private function createMiddleware(): LoggerMiddleware
    {
        $formatter = $this->createStub(MessageFormatterInterface::class);
        $formatter->method('format')->willReturnCallback(
            function (
                LogLevel $logLevel,
                RequestInterface $request,
                ?ResponseInterface $response = null,
                ?string $reason = null
            ): string {
                $this->formatterCalled = true;
                $this->capturedLogLevel = $logLevel;
                $this->capturedResponse = $response;
                $this->capturedReason = $reason;
                // Consume the body, so there is something for the middleware to rewind.
                $response?->getBody()->getContents();

                return 'formatted message';
            }
        );

        $logger = $this->createStub(LoggerInterface::class);
        $logger->method('log')->willReturnCallback(
            function ($level, $message): void {
                $this->logRecords[] = ['level' => (string) $level, 'message' => (string) $message];
            }
        );

        return new LoggerMiddleware($logger, $formatter, new DefaultLogLevelStrategy());
    }

    /**
     * @return RequestInterface
     * @throws MalformedUriException
     * @throws InvalidArgumentException
     */
    private function createRequest(): RequestInterface
    {
        return new Request('GET', 'http://example.com/resource');
    }
}
