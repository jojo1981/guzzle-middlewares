<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Value;

use Jojo1981\GuzzleMiddlewares\Exception\InvalidValueException;
use function array_flip;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * This value class represents the values which are valid according the PSR-3.
 * @see \Psr\Log\LogLevel
 *
 * @package Jojo1981\GuzzleMiddlewares\Value
 */
final class LogLevel
{
    /** @var string */
    private $value;

    /**
     * @param string $value
     * @throws InvalidValueException
     */
    public function __construct(string $value)
    {
        $value = static::normalizeValue($value);
        $this->assertValue($value);
        $this->value = $value;
    }

    /**
     * @param string $value
     * @throws InvalidValueException
     * @return void
     */
    private function assertValue(string $value): void
    {
        if (!static::isValidValue($value)) {
            throw new InvalidValueException(sprintf(
                'Invalid log level value given: `%s`. Expect one of [%s]',
                $value,
                implode(', ', static::getValidValues())
            ));
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param LogLevel $other
     * @return bool
     */
    public function isEqual(LogLevel $other): bool
    {
        return $other->getValue() === $this->value;
    }

    /**
     * @return int
     */
    public function getNumericValue(): int
    {
        return array_flip(static::getValidValues())[$this->value];
    }

    /**
     * @return bool
     */
    public function isEmergency(): bool
    {
        return 'emergency' === $this->value;
    }

    /**
     * @return bool
     */
    public function isAlert(): bool
    {
        return 'alert' === $this->value;
    }

    /**
     * @return bool
     */
    public function isCritical(): bool
    {
        return 'critical' === $this->value;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return 'error' === $this->value;
    }

    /**
     * @return bool
     */
    public function isWarning(): bool
    {
        return 'warning' === $this->value;
    }

    /**
     * @return bool
     */
    public function isNotice(): bool
    {
        return 'notice' === $this->value;
    }

    /**
     * @return bool
     */
    public function isInfo(): bool
    {
        return 'info' === $this->value;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return 'debug' === $this->value;
    }


    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function emergency(): LogLevel
    {
        return new static('emergency');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function alert(): LogLevel
    {
        return new static('alert');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function critical(): LogLevel
    {
        return new static('critical');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function error(): LogLevel
    {
        return new static('error');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function warning(): LogLevel
    {
        return new static('warning');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function notice(): LogLevel
    {
        return new static('notice');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function info(): LogLevel
    {
        return new static('info');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function debug(): LogLevel
    {
        return new static('debug');
    }

    /**
     * @return string[]
     */
    public static function getValidValues(): array
    {
        return [
            600 => 'emergency',
            550 => 'alert',
            500 => 'critical',
            400 => 'error',
            300 => 'warning',
            250 => 'notice',
            200 => 'info',
            100 => 'debug'
        ];
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValidValue(string $value): bool
    {
        return in_array(static::normalizeValue($value), static::getValidValues(), true);
    }

    /**
     * @param string $value
     * @return string
     */
    private static function normalizeValue(string $value): string
    {
        return strtolower($value);
    }
}