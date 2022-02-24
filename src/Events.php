<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares;

use Jojo1981\GuzzleMiddlewares\Event\AfterSendRequestFailedEvent;
use Jojo1981\GuzzleMiddlewares\Event\AfterSendRequestSuccessEvent;
use Jojo1981\GuzzleMiddlewares\Event\BeforeSendRequestEvent;

/**
 * @package Jojo1981\GuzzleMiddlewares
 */
class Events
{
    /** @var string */
    public const EVENT_BEFORE_SEND_REQUEST = BeforeSendRequestEvent::NAME;

    /** @var string  */
    public const EVENT_AFTER_SEND_REQUEST_SUCCESS = AfterSendRequestSuccessEvent::NAME;

    /** @var string */
    public const EVENT_AFTER_SEND_REQUEST_FAILED = AfterSendRequestFailedEvent::NAME;

    /**
     * Private constructor, prevent getting an instance of this class
     */
    private function __construct()
    {
        // Nothing to do here
    }
}
