<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Event;

use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Event
 */
final class AfterSendRequestFailedEvent extends AbstractSendRequestEvent
{
    /** @var string */
    public const NAME = 'event.http_client.after_send_request.failed';

    /** @var Throwable */
    private Throwable $exception;

    /**
     * @param RequestInterface $request
     * @param array $options
     * @param Throwable $exception
     */
    public function __construct(RequestInterface $request, array $options, Throwable $exception)
    {
        parent::__construct($request, $options);
        $this->exception = $exception;
    }

    /**
     * @return Throwable
     */
    public function getException(): Throwable
    {
        return $this->exception;
    }
}
