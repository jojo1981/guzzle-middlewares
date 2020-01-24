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

use Jojo1981\GuzzleMiddlewares\Formatter\FormatInterface;
use Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategyInterface;
use Jojo1981\GuzzleMiddlewares\Value\LogLevel;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategy
 */
class ConfigurableFormatStrategy implements FormatStrategyInterface
{
    /** @var FormatInterface */
    private $format;

    /** @var bool */
    private $prettifyJsonBody;

    /**
     * @param FormatInterface $format
     * @param bool $prettifyJsonBody
     */
    public function __construct(FormatInterface $format, bool $prettifyJsonBody)
    {
        $this->format = $format;
        $this->prettifyJsonBody = $prettifyJsonBody;
    }

    /**
     * @param LogLevel $logLevel
     * @return FormatInterface
     */
    public function getFormatBasedOnLogLevel(LogLevel $logLevel): FormatInterface
    {
        return $this->format;
    }

    /**
     * @return bool
     */
    public function prettifyJsonBody(): bool
    {
        return $this->prettifyJsonBody;
    }
}