<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategy;

use Jojo1981\GuzzleMiddlewares\Formatter\Format\DebugFormat;
use Jojo1981\GuzzleMiddlewares\Formatter\FormatInterface;
use Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategyInterface;
use Jojo1981\GuzzleMiddlewares\Value\LogLevel;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategy
 */
class DebugFormatStrategy implements FormatStrategyInterface
{
    /**
     * @param LogLevel $logLevel
     * @return FormatInterface
     */
    public function getFormatBasedOnLogLevel(LogLevel $logLevel): FormatInterface
    {
        return new DebugFormat();
    }

    /**
     * @return bool
     */
    public function prettifyJsonBody(): bool
    {
        return true;
    }
}