<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Factory;

use Jojo1981\GuzzleMiddlewares\Exception\FactoryIsFrozenException;
use Jojo1981\GuzzleMiddlewares\Formatter\MessageFormatterFactory;
use Jojo1981\GuzzleMiddlewares\Formatter\MessageFormatterInterface;
use Jojo1981\GuzzleMiddlewares\Middleware\LoggerMiddleware;
use Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategy\DefaultLogLevelStrategy;
use Jojo1981\GuzzleMiddlewares\Middleware\LogLevelStrategyInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @package Jojo1981\GuzzleMiddlewares\Factory
 */
final class LoggerMiddlewareFactory
{
    /** @var LoggerInterface|null */
    private ?LoggerInterface $logger = null;

    /** @var MessageFormatterInterface|null */
    private ?MessageFormatterInterface $formatter = null;

    /** @var LogLevelStrategyInterface|null */
    private ?LogLevelStrategyInterface $logLevelStrategy = null;

    /** @var LoggerMiddleware|null */
    private ?LoggerMiddleware $loggerMiddleware = null;

    /** @var bool */
    private bool $frozen = false;

    /***
     * @param LoggerInterface $logger
     * @throws FactoryIsFrozenException
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->assertIsNotFrozen();
        $this->logger = $logger;
    }

    /**
     * @param MessageFormatterInterface $formatter
     * @throws FactoryIsFrozenException
     * @return void
     */
    public function setFormatter(MessageFormatterInterface $formatter): void
    {
        $this->assertIsNotFrozen();
        $this->formatter = $formatter;
    }

    /**
     * @param LogLevelStrategyInterface $logLevelStrategy
     * @throws FactoryIsFrozenException
     * @return void
     */
    public function setLogLevelStrategy(LogLevelStrategyInterface $logLevelStrategy): void
    {
        $this->assertIsNotFrozen();
        $this->logLevelStrategy = $logLevelStrategy;
    }

    /**
     * @return LoggerMiddleware
     */
    public function getLoggerMiddleware(): LoggerMiddleware
    {
        if (null === $this->loggerMiddleware) {
            $this->loggerMiddleware = new LoggerMiddleware(
                $this->getLogger(),
                $this->getFormatter(),
                $this->getLogLevelStrategy()
            );
            $this->frozen = true;
        }

        return $this->loggerMiddleware;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @return MessageFormatterInterface
     */
    private function getFormatter(): MessageFormatterInterface
    {
        if (null === $this->formatter) {
            $this->formatter = (new MessageFormatterFactory())->getMessageFormatter();
        }

        return $this->formatter;
    }

    /**
     * @return LogLevelStrategyInterface
     */
    private function getLogLevelStrategy(): LogLevelStrategyInterface
    {
        if (null === $this->logLevelStrategy) {
            $this->logLevelStrategy = new DefaultLogLevelStrategy();
        }

        return $this->logLevelStrategy;
    }

    /**
     * @throws FactoryIsFrozenException
     * @return void
     */
    private function assertIsNotFrozen(): void
    {
        if ($this->frozen) {
            throw new FactoryIsFrozenException('LoggerMiddlewareFactory is frozen.');
        }
    }
}
