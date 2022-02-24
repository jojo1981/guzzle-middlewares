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
 * This decorator can be used to decorate a http data storage which is writable and prevent write access and
 * only allow read access.
 *
 * @package Jojo1981\GuzzleMiddlewares\Storage
 */
class ReadOnlyHttpStorageDecorator implements ReadOnlyHttpDataStorageInterface
{
    /** @var HttpDataStorageInterface */
    private HttpDataStorageInterface $httpDataStorage;

    /**
     * @param HttpDataStorageInterface $httpDataStorage
     */
    public function __construct(HttpDataStorageInterface $httpDataStorage)
    {
        $this->httpDataStorage = $httpDataStorage;
    }

    /**
     * @return null|RequestInterface
     */
    public function getLastRequest(): ?RequestInterface
    {
        return $this->httpDataStorage->getLastRequest();
    }

    /**
     * @return null|ResponseInterface
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->httpDataStorage->getLastResponse();
    }

    /**
     * @return null|Throwable
     */
    public function getLastReason(): ?Throwable
    {
        return $this->httpDataStorage->getLastReason();
    }
}
