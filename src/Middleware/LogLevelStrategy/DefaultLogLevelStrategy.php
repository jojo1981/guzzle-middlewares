<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategy;

use Jojo1981\GuzzleMiddlewares\Exception\InvalidValueException;
use Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategyInterface;
use Jojo1981\GuzzleMiddlewares\Value\LogLevel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategy
 */
final class DefaultLogLevelStrategy implements LogLevelStrategyInterface
{
    /**
     * @param ResponseInterface $response
     * @throws InvalidValueException
     * @return LogLevel
     */
    public function getLogLevelBasedOnResponse(ResponseInterface $response): LogLevel
    {
        $statusCode = $response->getStatusCode();
        if ($response->getStatusCode() >= 500) {
            return LogLevel::error();
        }

        if ($response->getStatusCode() >= 400) {
            return LogLevel::warning();
        }

        if ($statusCode >= 200 && $statusCode <= 299) {
            return LogLevel::info();
        }

        return LogLevel::notice();
    }

    /**
     * @param Throwable $reason
     * @throws InvalidValueException
     * @return LogLevel
     */
    public function getLogLevelForFailedRequest(Throwable $reason): LogLevel
    {
        return LogLevel::warning();
    }
}
