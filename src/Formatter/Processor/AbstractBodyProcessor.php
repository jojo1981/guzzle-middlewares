<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Formatter\Processor;

use Jojo1981\GuzzleMiddlewares\Formatter\FormatStrategyInterface as FormatStrategy;
use Jojo1981\GuzzleMiddlewares\Formatter\ProcessorInterface;
use Jojo1981\GuzzleMiddlewares\Helper\JsonPrettifier;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamInterface as Stream;
use RuntimeException;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\Processor
 */
abstract class AbstractBodyProcessor implements ProcessorInterface
{
    /**
     * @param string $key
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @return string
     * @throws RuntimeException
     */
    final public function process(string $key, Request $request, ?Response $response = null, ?string $reason = null): string
    {
        $httpMessage = $this->getStream($request, $response, $reason);

        return null !== $httpMessage ? 'Body:' . PHP_EOL . $this->parseStream($httpMessage) : '';
    }

    /**
     * @return FormatStrategy
     */
    abstract protected function getFormatStrategy(): FormatStrategy;

    /**
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @return null|Stream
     */
    abstract protected function getStream(
        Request $request,
        ?Response $response = null,
        ?string $reason = null
    ): ?Stream;

    /**
     * @param Stream $httpMessage
     * @throws RuntimeException
     * @return string
     */
    private function parseStream(Stream $httpMessage): string
    {
        if ($this->getFormatStrategy()->prettifyJsonBody()) {
            $result = JsonPrettifier::prettyPrint((string) $httpMessage);
            $httpMessage->seek(0);

            return $result;
        }

        return (string) $httpMessage;
    }
}
