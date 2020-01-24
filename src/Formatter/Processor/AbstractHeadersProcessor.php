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
use Psr\Http\Message\MessageInterface as Message;
use function implode;
use function trim;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\Processor
 */
abstract class AbstractHeadersProcessor implements ProcessorInterface
{
    /**
     * @param Message $message
     * @return string
     */
    final protected function parseHeaders(Message $message): string
    {
        $result = '';
        foreach ($message->getHeaders() as $name => $values) {
            $result .= $name . ': ' . implode(', ', $values) . "\r\n";
        }

        return trim($result);
    }
}