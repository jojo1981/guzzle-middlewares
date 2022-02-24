<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Storage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Storage
 */
interface WritableHttpDataStorageInterface
{
    /**
     * @param RequestInterface $lastRequest
     * @return void
     */
    public function setLastRequest(RequestInterface $lastRequest): void;

    /**
     * @param ResponseInterface $lastResponse
     * @return void
     */
    public function setLastResponse(ResponseInterface $lastResponse): void;

    /**
     * @param Throwable $lastReason
     * @return void
     */
    public function setLastReason(Throwable $lastReason): void;

    /**
     * @return void
     */
    public function clear(): void;
}
