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

use Jojo1981\GuzzleMiddlewares\Exception\FactoryIsFrozenException;
use Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategy\DefaultFormatStrategy;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter
 */
class MessageFormatterFactory
{
    /** @var FormatStrategyInterface */
    private $formatStrategy;

    /** @var ProcessorInterface */
    private $processor;

    /** @var MessageFormatterInterface */
    private $messageFormatter;

    /** @var bool */
    private $frozen = false;

    /**
     * @param FormatStrategyInterface $formatStrategy
     * @throws FactoryIsFrozenException
     * @return void
     */
    public function setFormatStrategy(FormatStrategyInterface $formatStrategy): void
    {
        $this->assertIsNotFrozen();
        $this->formatStrategy = $formatStrategy;
    }

    /**
     * @param ProcessorInterface $processor
     * @throws FactoryIsFrozenException
     * @return void
     */
    public function setProcessor(ProcessorInterface $processor): void
    {
        $this->assertIsNotFrozen();
        $this->processor = $processor;
    }

    /**
     * @return MessageFormatterInterface
     */
    public function getMessageFormatter(): MessageFormatterInterface
    {
        if (null === $this->messageFormatter) {
            $this->messageFormatter = new DefaultMessageFormatter($this->getFormatStrategy(), $this->getProcessor());
            $this->frozen = true;
        }

        return $this->messageFormatter;
    }

    /**
     * @return FormatStrategyInterface
     */
    private function getFormatStrategy(): FormatStrategyInterface
    {
        if (null === $this->formatStrategy) {
            $this->formatStrategy = new DefaultFormatStrategy();
        }

        return $this->formatStrategy;
    }

    /**
     * @return ProcessorInterface
     */
    private function getProcessor(): ProcessorInterface
    {
        if (null === $this->processor) {
            $this->processor = new ProcessorRegistry();
            $this->processor->addDefaultProcessors($this->getFormatStrategy());
        }

        return $this->processor;
    }

    /**
     * @throws FactoryIsFrozenException
     * @return void
     */
    private function assertIsNotFrozen(): void
    {
        if ($this->frozen) {
            throw new FactoryIsFrozenException('MessageFormatterFactory is frozen.');
        }
    }
}