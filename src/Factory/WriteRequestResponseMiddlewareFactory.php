<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2022 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Factory;

use Jojo1981\GuzzleMiddlewares\Exception\FactoryIsFrozenException;
use Jojo1981\GuzzleMiddlewares\Middleware\WriteRequestResponseMiddleware;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatter\DefaultHttpMessageFormatter;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatterInterface;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\RequestResponseWriter\DefaultRequestResponseWriter;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\RequestResponseWriterInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Jojo1981\GuzzleMiddlewares\Factory
 */
final class WriteRequestResponseMiddlewareFactory
{
    /** @var HttpMessageFormatterInterface|null */
    private ?HttpMessageFormatterInterface $httpMessageFormatter = null;

    /** @var RequestResponseWriterInterface|null */
    private ?RequestResponseWriterInterface $requestResponseWriter = null;

    /** @var bool */
    private bool $frozen = false;

    /** @var string */
    private string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param HttpMessageFormatterInterface $httpMessageFormatter
     * @return $this
     * @throws FactoryIsFrozenException
     */
    public function setHttpMessageFormatter(HttpMessageFormatterInterface $httpMessageFormatter): self
    {
        $this->assertIsNotFrozen();
        $this->httpMessageFormatter = $httpMessageFormatter;

        return $this;
    }

    /**
     * @param RequestResponseWriterInterface $requestResponseWriter
     * @return $this
     * @throws FactoryIsFrozenException
     */
    public function setRequestResponseWriter(RequestResponseWriterInterface $requestResponseWriter): self
    {
        $this->assertIsNotFrozen();
        $this->requestResponseWriter = $requestResponseWriter;

        return $this;
    }

    /**
     * @return WriteRequestResponseMiddleware
     */
    public function getWriteRequestResponseMiddleware(): WriteRequestResponseMiddleware
    {
        $this->frozen = true;

        return new WriteRequestResponseMiddleware($this->getHttpMessageFormatter(), $this->getRequestResponseWriter());
    }

    /**
     * @return HttpMessageFormatterInterface
     */
    private function getHttpMessageFormatter(): ?HttpMessageFormatterInterface
    {
        if (null === $this->httpMessageFormatter) {
            return new DefaultHttpMessageFormatter();
        }

        return $this->httpMessageFormatter;
    }

    /**
     * @return RequestResponseWriterInterface
     */
    private function getRequestResponseWriter(): ?RequestResponseWriterInterface
    {
        if (null === $this->requestResponseWriter) {
            return new DefaultRequestResponseWriter($this->path, new Filesystem());
        }

        return $this->requestResponseWriter;
    }

    /**
     * @throws FactoryIsFrozenException
     * @return void
     */
    private function assertIsNotFrozen(): void
    {
        if ($this->frozen) {
            throw new FactoryIsFrozenException('WriteRequestResponseMiddlewareFactory is frozen.');
        }
    }
}
