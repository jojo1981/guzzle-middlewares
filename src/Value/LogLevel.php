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
    private string $value;

    /**
     * @param string $value
     * @throws InvalidValueException
     */
    public function __construct(string $value)
    {
        $value = self::normalizeValue($value);
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
        if (!self::isValidValue($value)) {
            throw new InvalidValueException(sprintf(
                'Invalid log level value given: `%s`. Expect one of [%s]',
                $value,
                implode(', ', self::getValidValues())
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
        return array_flip(self::getValidValues())[$this->value];
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
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function emergency(): LogLevel
    {
        return new self('emergency');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function alert(): LogLevel
    {
        return new self('alert');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function critical(): LogLevel
    {
        return new self('critical');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function error(): LogLevel
    {
        return new self('error');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function warning(): LogLevel
    {
        return new self('warning');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function notice(): LogLevel
    {
        return new self('notice');
    }

    /**
     * @return LogLevel
     * @throws InvalidValueException
     */
    public static function info(): LogLevel
    {
        return new self('info');
    }

    /**
     * @throws InvalidValueException
     * @return LogLevel
     */
    public static function debug(): LogLevel
    {
        return new self('debug');
    }

    /**
     * @return string[]
     */
    public static function getValidValues(): array
    {
        return [
            100 => 'debug',
            200 => 'info',
            250 => 'notice',
            300 => 'warning',
            400 => 'error',
            500 => 'critical',
            550 => 'alert',
            600 => 'emergency'
        ];
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValidValue(string $value): bool
    {
        return in_array(self::normalizeValue($value), self::getValidValues(), true);
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
