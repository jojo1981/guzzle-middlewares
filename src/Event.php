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

use function class_exists;

if (class_exists('Behat\Testwork\Event\Event')) {

    /**
     * @package Jojo1981\GuzzleMiddlewares
     */
    abstract class Event extends \Behat\Testwork\Event\Event
    {
    }

} elseif (class_exists('Symfony\Component\EventDispatcher\Event')) {

    /**
     * @package Jojo1981\GuzzleMiddlewares
     */
    abstract class Event extends \Symfony\Component\EventDispatcher\Event
    {
    }

} else {

    /**
     * @package Jojo1981\GuzzleMiddlewares
     */
    abstract class Event extends \Symfony\Contracts\EventDispatcher\Event
    {
    }

}
