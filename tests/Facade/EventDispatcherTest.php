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

namespace Tests\Jojo1981\GuzzleMiddlewares\Facade;

use Jojo1981\GuzzleMiddlewares\Facade\EventDispatcher;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

/**
 * @package Tests\Jojo1981\GuzzleMiddlewares\Facade
 */
class EventDispatcherTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testDispatch(): void
    {
        $baseDispatcher = new BaseEventDispatcher();
        $event = new stdClass();
        $eventName = 'test.event';
        $handlerCalled = false;

        $baseDispatcher->addListener($eventName, function ($passedEvent) use ($event, &$handlerCalled) {
            $handlerCalled = true;
            self::assertSame($event, $passedEvent);
        });

        $dispatcher = new EventDispatcher($baseDispatcher);
        $result = $dispatcher->dispatch($event, $eventName);
        self::assertSame($event, $result);
        self::assertTrue($handlerCalled, 'Handler should have been called');
    }
}
