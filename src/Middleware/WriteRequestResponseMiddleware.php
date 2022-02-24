<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2022 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Middleware;

use Closure;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatterInterface;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\RequestResponseWriterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function implode;
use function str_pad;

/**
 * @package Jojo1981\GuzzleMiddlewares\Middleware
 */
final class WriteRequestResponseMiddleware
{
    /** @var int */
    private int $counter = 0;

    /** @var HttpMessageFormatterInterface */
    private HttpMessageFormatterInterface $httpMessageFormatter;

    /** @var RequestResponseWriterInterface */
    private RequestResponseWriterInterface $requestResponseWriter;

    /**
     * @param HttpMessageFormatterInterface $httpMessageFormatter
     * @param RequestResponseWriterInterface $requestResponseWriter
     */
    public function __construct(HttpMessageFormatterInterface $httpMessageFormatter, RequestResponseWriterInterface $requestResponseWriter)
    {
        $this->httpMessageFormatter = $httpMessageFormatter;
        $this->requestResponseWriter = $requestResponseWriter;
    }

    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(
                $this->onSuccess($request),
                $this->onFailure($request)
            );
        };
    }

    /**
     * @param RequestInterface $request
     * @return Closure
     */
    private function onSuccess(RequestInterface $request): Closure
    {
        return function (ResponseInterface $response) use ($request): ResponseInterface {
            $this->counter++;
            $content = implode(
                PHP_EOL,
                [
                    'REQUEST:',
                    $this->httpMessageFormatter->convertMessageToString($request, 0),
                    '',
                    'RESPONSE:',
                    $this->httpMessageFormatter->convertMessageToString($response, 0)
                ]
            );
            $this->writeContent($content);

            return $response;
        };
    }

    /**
     * @param RequestInterface $request
     * @return Closure
     */
    private function onFailure(RequestInterface $request): Closure
    {
        return function (Throwable $reason) use ($request): PromiseInterface {
            $this->counter++;
            $content = implode(
                PHP_EOL,
                [
                    'REQUEST:',
                    $this->httpMessageFormatter->convertMessageToString($request, 0),
                    '',
                    'ERROR:',
                    $reason->getMessage()
                ]
            );
            $this->writeContent($content);

            return Create::rejectionFor($reason);
        };
    }

    /**
     * @param string $content
     * @return void
     */
    private function writeContent(string $content): void
    {
        $filename = str_pad((string) $this->counter, 5, '0', STR_PAD_LEFT);
        $this->requestResponseWriter->write($filename, $content);
    }
}
