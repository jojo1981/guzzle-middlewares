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

use Jojo1981\GuzzleMiddlewares\Formatter\ProcessorInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use function date;
use function gmdate;
use function in_array;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\SegmentFormatter
 */
class DateProcessor implements ProcessorInterface
{
    /**
     * @param string $key
     * @return bool
     */
    public function supports(string $key): bool
    {
        return in_array($key, ['date_common_log', 'date_iso_8601', 'ts'], true);
    }

    /**
     * @param string $key
     * @param Request $request
     * @param null|Response $response
     * @param null|string $reason
     * @return string
     */
    public function process(string $key, Request $request, ?Response $response = null, ?string $reason = null): string
    {
        if ('date_common_log' === $key) {
            return date('d/M/Y:H:i:s O');
        }

        return gmdate('c');
    }
}
