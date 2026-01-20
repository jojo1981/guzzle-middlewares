<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2026 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\Exception;

use RuntimeException;
use Throwable;

/**
 * @package Jojo1981\GuzzleMiddlewares\Exception
 */
final class IOException extends RuntimeException
{
    /** @var string|null */
    private ?string $path;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param string|null $path
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null, ?string $path = null)
    {
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
