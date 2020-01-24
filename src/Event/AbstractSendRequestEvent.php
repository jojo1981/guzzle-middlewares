<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2020 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Event;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @package Jojo1981\GuzzleMiddlewares\Event
 */
abstract class AbstractSendRequestEvent extends Event
{
    /** @var RequestInterface */
    private $request;

    /** @var array */
    private $options;

    /**
     * @param RequestInterface $request
     * @param array $options
     */
    public function __construct(RequestInterface $request, array $options)
    {
        $this->request = $request;
        $this->options = $options;
    }

    /**
     * @return RequestInterface
     */
    final public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return array
     */
    final public function getOptions(): array
    {
        return $this->options;
    }
}