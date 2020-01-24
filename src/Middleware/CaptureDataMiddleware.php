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
use GuzzleHttp\Promise\PromiseInterface;
use Jojo1981\GuzzleMiddlewares\Storage\WritableHttpDataStorageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function GuzzleHttp\Promise\rejection_for;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware
 */
class CaptureDataMiddleware
{
    /** @var WritableHttpDataStorageInterface */
    private $httpDataStorage;

    /**
     * @param WritableHttpDataStorageInterface $httpDataStorage
     */
    public function __construct(WritableHttpDataStorageInterface $httpDataStorage)
    {
        $this->httpDataStorage = $httpDataStorage;
    }

    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $this->httpDataStorage->clear();
            $this->httpDataStorage->setLastRequest($request);

            return $handler($request, $options)->then(
                $this->onSuccess(),
                $this->onFailure()
            );
        };
    }

    /**
     * @return Closure
     */
    private function onSuccess(): Closure
    {
        return function (ResponseInterface $response): ResponseInterface {
            $this->httpDataStorage->setLastResponse($response);

            return $response;
        };
    }

    /**
     * @return Closure
     */
    private function onFailure(): Closure
    {
        return function (Throwable $reason): PromiseInterface {
            $this->httpDataStorage->setLastReason($reason);

            return rejection_for($reason);
        };
    }
}