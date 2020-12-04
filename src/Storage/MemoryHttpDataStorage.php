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
class MemoryHttpDataStorage implements HttpDataStorageInterface
{
    /** @var null|RequestInterface */
    private $lastRequest;

    /** @var null|ResponseInterface */
    private $lastResponse;

    /** @var null|Throwable */
    private $lastReason;

    /**
     * @return null|RequestInterface
     */
    public function getLastRequest(): ?RequestInterface
    {
        return $this->lastRequest;
    }

    /**
     * @param RequestInterface $lastRequest
     * @return void
     */
    public function setLastRequest(RequestInterface $lastRequest): void
    {
        $this->lastRequest = $lastRequest;
        $this->lastResponse = null;
        $this->lastReason = null;
    }

    /**
     * @return null|ResponseInterface
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * @param ResponseInterface $lastResponse
     * @return void
     */
    public function setLastResponse(ResponseInterface $lastResponse): void
    {
        $this->lastResponse = $lastResponse;
        $this->lastReason = null;
    }

    /**
     * @return null|Throwable
     */
    public function getLastReason(): ?Throwable
    {
        return $this->lastReason;
    }

    /**
     * @param Throwable $lastReason
     * @return void
     */
    public function setLastReason(Throwable $lastReason): void
    {
        $this->lastReason = $lastReason;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->lastRequest = null;
        $this->lastResponse = null;
        $this->lastReason = null;
    }
}