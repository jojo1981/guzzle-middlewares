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

use Jojo1981\GuzzleMiddlewares\Value\LogLevel;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;
use function preg_replace_callback;
use function strpos;
use function substr;

/**
 * Formats log messages using variable substitutions for requests, responses, and other transactional data.
 *
 * The following variable substitutions are supported:
 *
 * - {request}:        Full HTTP request message
 * - {response}:       Full HTTP response message
 * - {ts}:             ISO 8601 date in GMT
 * - {date_iso_8601}   ISO 8601 date in GMT
 * - {date_common_log} Apache common log date using the configured timezone.
 * - {host}:           Host of the request
 * - {method}:         Method of the request
 * - {uri}:            URI of the request
 * - {version}:        Protocol version
 * - {target}:         Request target of the request (path + query + fragment)
 * - {hostname}:       Hostname of the machine that sent the request
 * - {code}:           Status code of the response (if available)
 * - {phrase}:         Reason phrase of the response  (if available)
 * - {error}:          Any error messages (if available)
 * - {req_header_*}:   Replace `*` with the lowercased name of a request header to add to the message
 * - {res_header_*}:   Replace `*` with the lowercased name of a response header to add to the message
 * - {req_headers}:    Request headers
 * - {res_headers}:    Response headers
 * - {req_body}:       Request body
 * - {res_body}:       Response body
 *
 * @package Jojo1981\GuzzleMiddlewares\Formatter
 */
final class DefaultMessageFormatter implements MessageFormatterInterface
{
    /** @var FormatStrategyInterface */
    private FormatStrategyInterface $formatStrategy;

    /** @var ProcessorInterface */
    private ProcessorInterface $processor;

    /**
     * @param FormatStrategyInterface $formatStrategy
     * @param ProcessorInterface $processor
     */
    public function __construct(FormatStrategyInterface $formatStrategy, ProcessorInterface $processor)
    {
        $this->formatStrategy = $formatStrategy;
        $this->processor = $processor;
    }

    /**
     * @param LogLevel $logLevel
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @throws RuntimeException
     * @return string
     */
    public function format(
        LogLevel $logLevel,
        Request $request,
        ?Response $response = null,
        ?string $reason = null
    ): string
    {
        $this->rewindRequestAndResponse($request, $response);
        $format = $this->formatStrategy->getFormatBasedOnLogLevel($logLevel);

        $cache = [];
        return preg_replace_callback(
            '/{\s*([A-Za-z_\-.0-9]+)\s*}/',
            function (array $matches) use ($request, $response, $reason, &$cache): string {
                if (isset($cache[$matches[1]])) {
                    return $cache[$matches[1]];
                }

                $result = '';
                $key = $matches[1];
                if ($this->processor->supports($key)) {
                    $result = $this->processor->process($key, $request, $response, $reason);
                } else if (0 === strpos($matches[1], 'req_header_')) {
                    // handle prefixed dynamic headers
                    $result = $request->getHeaderLine(substr($matches[1], 11));
                } elseif (0 === strpos($matches[1], 'res_header_')) {
                    $result = null !== $response ? $response->getHeaderLine(substr($matches[1], 11)) : '';
                }

                return $cache[$matches[1]] = $result;
            },
            $format->getPatternString()
        );
    }

    /**
     * @param Request $request
     * @param null|Response $response
     * @throws RuntimeException
     * @return void
     */
    private function rewindRequestAndResponse(Request $request, ?Response $response = null): void
    {
        $request->getBody()->seek(0);
        if ($response) {
            $response->getBody()->seek(0);
        }
    }
}
