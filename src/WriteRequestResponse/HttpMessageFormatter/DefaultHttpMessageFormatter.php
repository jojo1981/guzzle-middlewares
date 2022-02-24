<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2022 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatter;

use Jojo1981\GuzzleMiddlewares\Helper\JsonPrettifier;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatterInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use function explode;
use function implode;
use function sprintf;
use function str_repeat;
use function str_replace;
use function stripos;
use function strtoupper;
use function trim;
use function uniqid;

/**
 * @package Jojo1981\GuzzleMiddlewares\WriteRequestResponse\HttpMessageFormatter
 */
final class DefaultHttpMessageFormatter implements HttpMessageFormatterInterface
{
    /**
     * @param MessageInterface|null $message
     * @param int $indent
     * @return string|null
     * @throws RuntimeException
     */
    public function convertMessageToString(?MessageInterface $message = null, int $indent = 4): ?string
    {
        if (!$message instanceof RequestInterface && !$message instanceof ResponseInterface) {
            return null;
        }

        if ($message instanceof RequestInterface) {
            $resultString = sprintf(
                '%s %s HTTP/%s',
                strtoupper(trim($message->getMethod())),
                trim($message->getRequestTarget()),
                trim($message->getProtocolVersion())
            );
            if (!$message->hasHeader('Host')) {
                $resultString .= PHP_EOL . 'Host: ' . trim($message->getUri()->getHost());
            }
        } else {
            $resultString = sprintf(
                'HTTP/%s %s %s',
                trim($message->getProtocolVersion()),
                trim((string) $message->getStatusCode()),
                trim($message->getReasonPhrase())
            );
        }

        if (null !== $headerString = $this->convertHeadersToString($message->getHeaders())) {
            $resultString .= PHP_EOL . $headerString;
        }

        $message->getBody()->rewind();
        $bodyString = $message->getBody()->getContents();
        $message->getBody()->rewind();

        if ($this->hasContentTypeHeaderWithApplicationJson($message)) {
            $bodyString = JsonPrettifier::prettyPrint($bodyString);
        }

        if (!empty($bodyString)) {
            $resultString .= PHP_EOL . PHP_EOL . $bodyString;
        }

        if ($indent > 0) {
            $resultString = $this->indentText($resultString, $indent);
        }

        return $resultString;
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    private function hasContentTypeHeaderWithApplicationJson(MessageInterface $message): bool
    {
        foreach ($message->getHeader('Content-Type') as $value) {
            if (false !== stripos($value, 'application/json')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[][] $headers
     * @return string|null
     */
    private function convertHeadersToString(array $headers): ?string
    {
        $lines = [];
        foreach ($headers as $name => $values) {
            $lines[] = $name . ': ' . implode(', ', $values);
        }

        return !empty($lines) ? implode(PHP_EOL, $lines) : null;
    }

    /**
     * @param string $text
     * @param int $size
     * @return string
     */
    private function indentText(string $text, int $size): string
    {
        $indentString = str_repeat(' ', $size);
        $randomString = uniqid('random', true);
        $text = str_replace(["\r\n", "\r", "\n"], $randomString, $text);
        $lines = explode($randomString, $text);
        foreach ($lines as &$line) {
            $line = $indentString . $line;
        }

        return implode(PHP_EOL, $lines);
    }
}
