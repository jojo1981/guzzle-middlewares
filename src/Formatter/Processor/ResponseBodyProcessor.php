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
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\StreamInterface as Stream;

/**
 * @package Jojo1981\GuzzleMiddlewares\Formatter\SegmentFormatter
 */
final class ResponseBodyProcessor extends AbstractBodyProcessor
{
    /** @var FormatStrategy */
    private FormatStrategy $formatStrategy;

    /**
     * @param FormatStrategy $formatStrategy
     */
    public function __construct(FormatStrategy $formatStrategy)
    {
        $this->formatStrategy = $formatStrategy;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function supports(string $key): bool
    {
        return 'res_body' === $key;
    }

    /**
     * @param Request $request
     * @param Response|null $response
     * @param string|null $reason
     * @return null|Stream
     */
    protected function getStream(Request $request, ?Response $response = null, ?string $reason = null): ?Stream
    {
        return null !== $response ? $response->getBody() : null;
    }

    /**
     * @return FormatStrategy
     */
    protected function getFormatStrategy(): FormatStrategy
    {
        return $this->formatStrategy;
    }
}
