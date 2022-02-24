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

use Jojo1981\GuzzleMiddlewares\Value\LogLevel;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware
 */
interface LogLevelStrategyInterface
{
    /**
     * @param ResponseInterface $response
     * @return LogLevel
     */
    public function getLogLevelBasedOnResponse(ResponseInterface $response): LogLevel;

    /**
     * @param Throwable $reason
     * @return LogLevel
     */
    public function getLogLevelForFailedRequest(Throwable $reason): LogLevel;
}
