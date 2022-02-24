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
use Psr\Http\Message\ResponseInterface;

/**
 * @package Jojo1981\GuzzleMiddlewares\Event
 */
final class AfterSendRequestSuccessEvent extends AbstractSendRequestEvent
{
    /** @var string */
    public const NAME = 'event.http_client.after_send_request.success';

    /** @var ResponseInterface */
    private ResponseInterface $response;

    /**
     * @param RequestInterface $request
     * @param array $options
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, array $options, ResponseInterface $response)
    {
        parent::__construct($request, $options);
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
