<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter;

use Jojo1981\GuzzleMiddlewares\Value\LogLevel;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter
 */
interface FormatStrategyInterface
{
    /**
     * @param LogLevel $logLevel
     * @return FormatInterface
     */
    public function getFormatBasedOnLogLevel(LogLevel $logLevel): FormatInterface;

    /**
     * @return bool
     */
    public function prettifyJsonBody(): bool;
}
