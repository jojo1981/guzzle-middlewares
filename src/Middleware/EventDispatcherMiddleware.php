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
use Jojo1981\GuzzleMiddlewares\Event\AfterSendRequestFailedEvent;
use Jojo1981\GuzzleMiddlewares\Event\AfterSendRequestSuccessEvent;
use Jojo1981\GuzzleMiddlewares\Event\BeforeSendRequestEvent;
use Jojo1981\GuzzleMiddlewares\Events;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;
use function GuzzleHttp\Promise\rejection_for;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware
 */
class EventDispatcherMiddleware
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $this->eventDispatcher->dispatch(
                Events::EVENT_BEFORE_SEND_REQUEST,
                new BeforeSendRequestEvent($request, $options)
            );

            return $handler($request, $options)->then(
                $this->onSuccess($request, $options),
                $this->onFailure($request, $options)
            );
        };
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return Closure
     */
    private function onSuccess(RequestInterface $request, array $options): Closure
    {
        return function (ResponseInterface $response) use ($request, $options): ResponseInterface {
            $this->eventDispatcher->dispatch(
                Events::EVENT_AFTER_SEND_REQUEST_SUCCESS,
                new AfterSendRequestSuccessEvent($request, $options, $response)
            );

            return $response;
        };
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return Closure
     */
    private function onFailure(RequestInterface $request, array $options): Closure
    {
        return function (Throwable $reason) use ($request, $options): PromiseInterface {
            $this->eventDispatcher->dispatch(
                Events::EVENT_AFTER_SEND_REQUEST_FAILED,
                new AfterSendRequestFailedEvent($request, $options, $reason)
            );

            return rejection_for($reason);
        };
    }
}