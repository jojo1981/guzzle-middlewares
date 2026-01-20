<?php
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2026 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
declare(strict_types=1);

namespace Jojo1981\GuzzleMiddlewares;

use Jojo1981\GuzzleMiddlewares\Exception\IOException;

/**
 * @package Jojo1981\GuzzleMiddlewares
 */
interface FilesystemInterface
{
    /**
     * Atomically dumps content into a file.
     *
     * @param string $filename The file to write to
     * @param string $content The data to write into the file
     * @return void
     * @throws IOException if the file cannot be written to
     */
    public function dumpFile(string $filename, string $content): void;

    /**
     * Checks the existence of files or directories.
     *
     * @param iterable|string $files A filename, an array of files, or a \Traversable instance to check
     * @return bool
     */
    public function exists(iterable|string $files): bool;

    /**
     * Creates a directory recursively.
     *
     * @param iterable|string $dirs The directory path
     * @param int $mode The permissions mode
     * @return void
     * @throws IOException On any directory creation failure
     */
    public function mkdir(iterable|string $dirs, int $mode = 0777): void;

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     * @return void
     * @throws IOException When removal fails
     */
    public function remove(string|iterable $files): void;
}
