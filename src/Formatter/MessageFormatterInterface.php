<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter;

use Jojo1981\GuzzleMiddlewares\Value\LogLevel;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter
 */
interface MessageFormatterInterface
{
    /**
     * @param LogLevel $logLevel
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @return string
     */
    public function format(
        LogLevel $logLevel,
        Request $request,
        ?Response $response = null,
        ?string $reason = null
    ): string;
}
