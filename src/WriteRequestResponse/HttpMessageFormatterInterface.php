<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2022 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\WriteRequestResponse;

use Psr\Http\Message\MessageInterface;

/**
 * @package Jojo1981\GuzzleMiddlewares\WriteRequestResponse
 */
interface HttpMessageFormatterInterface
{
    /**
     * @param MessageInterface|null $message
     * @param int $indent
     * @return string|null
     */
    public function convertMessageToString(?MessageInterface $message = null, int $indent = 4): ?string;
}
