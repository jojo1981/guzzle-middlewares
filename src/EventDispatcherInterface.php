<?php
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2026 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Jojo1981\GuzzleMiddlewares;

/**
 * @package Jojo1981\GuzzleMiddlewares
 */
interface EventDispatcherInterface
{
    /**
     * Dispatches an event to all registered listeners.
     *
     * @template T of object
     * @param T $event The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied, the class of $event should be used instead.
     * @return T The passed $event MUST be returned
     */
    public function dispatch(object $event, ?string $eventName = null): object;
}
