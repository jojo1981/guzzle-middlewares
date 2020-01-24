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

/**
 * @package Jojo1981\GuzzleMiddlewares\Event
 */
class BeforeSendRequestEvent extends AbstractSendRequestEvent
{
    /** @var string */
    public const NAME = 'event.http_client.before_send_request';
}