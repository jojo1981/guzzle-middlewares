<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Middleware;

use Closure;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Jojo1981\GuzzleMiddlewares\Formatter\MessageFormatterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware;
 */
final class LoggerMiddleware
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var MessageFormatterInterface */
    private MessageFormatterInterface $formatter;

    /** @var LogLevelStrategyInterface */
    private LogLevelStrategyInterface $logLevelStrategy;

    /**
     * @param LoggerInterface $logger
     * @param MessageFormatterInterface $formatter
     * @param LogLevelStrategyInterface $logLevelStrategy
     */
    public function __construct(
        LoggerInterface $logger,
        MessageFormatterInterface $formatter,
        LogLevelStrategyInterface $logLevelStrategy
    ) {
        $this->logger = $logger;
        $this->formatter = $formatter;
        $this->logLevelStrategy = $logLevelStrategy;
    }

    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                $this->onSuccess($request),
                $this->onFailure($request)
            );
        };
    }

    /**
     * @param RequestInterface $request
     * @return Closure
     */
    private function onSuccess(RequestInterface $request): Closure
    {
        return function (ResponseInterface $response) use ($request): ResponseInterface {
            $this->logSuccess($request, $response);
            // Make sure that the content of the body is available again.
            $response->getBody()->seek(0);

            return $response;
        };
    }

    /**
     * @param RequestInterface $request
     * @return Closure
     */
    private function onFailure(RequestInterface $request): Closure
    {
        return function (Throwable $reason) use ($request): PromiseInterface {
            $response = $reason instanceof RequestException ? $reason->getResponse() : null;
            $this->logFailed($request, $reason, $response);

            // Make sure that the content of the body is available again.
            if (null !== $response) {
                $response->getBody()->seek(0);
            }

            return Create::rejectionFor($reason);
        };
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws InvalidArgumentException
     * @return void
     */
    private function logSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $level = $this->logLevelStrategy->getLogLevelBasedOnResponse($response);
        $this->logger->log($level->getValue(), $this->formatter->format($level, $request, $response));
    }

    /**
     * @param RequestInterface $request
     * @param Throwable $reason
     * @param null|ResponseInterface $response
     * @throws InvalidArgumentException
     * @return void
     */
    private function logFailed(RequestInterface $request, Throwable $reason, ?ResponseInterface $response = null): void
    {
        $logLevel = $this->logLevelStrategy->getLogLevelForFailedRequest($reason);
        $this->logger->log(
            $logLevel->getValue(),
            $this->formatter->format($logLevel, $request, $response, $reason->getMessage())
        );
    }
}
