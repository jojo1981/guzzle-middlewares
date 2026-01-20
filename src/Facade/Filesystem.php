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

namespace Jojo1981\GuzzleMiddlewares\Facade;

use Jojo1981\GuzzleMiddlewares\Exception\IOException;
use Jojo1981\GuzzleMiddlewares\FilesystemInterface;
use Symfony\Component\Filesystem\Exception\IOException as BaseIOException;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Throwable;
use function sprintf;

/**
 * The Filesystem class acts as a facade for the Symfony Filesystem component, providing a simplified and unified interface for common filesystem
 * operations such as writing files, checking existence, creating directories, and removing files or directories. It handles exceptions from the
 * underlying Symfony implementation and rethrows them as custom exceptions, ensuring consistent error handling across your application.
 *
 * @package Jojo1981\GuzzleMiddlewares\Facade
 */
final class Filesystem implements FilesystemInterface
{
    /** @var BaseFilesystem */
    private BaseFilesystem $filesystem;

    /**
     * @param BaseFilesystem|null $filesystem
     */
    public function __construct(?BaseFilesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?? new BaseFilesystem();
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string $filename The file to write to
     * @param string $content The data to write into the file
     * @return void
     * @throws IOException if the file cannot be written to
     */
    public function dumpFile(string $filename, string $content): void
    {
        try {
            $this->filesystem->dumpFile($filename, $content);
        } catch (BaseIOException $exception) {
            throw new IOException(sprintf('Failed to dump file "%s".', $filename), 0, $exception, $exception->getPath());
        } catch (Throwable $exception) {
            throw new IOException(sprintf('Failed to dump file "%s".', $filename), 0, $exception);
        }
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param iterable|string $files A filename, an array of files, or a \Traversable instance to check
     * @return bool
     */
    public function exists(iterable|string $files): bool
    {
        try {
            return $this->filesystem->exists($files);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Creates a directory recursively.
     *
     * @param iterable|string $dirs The directory path
     * @param int $mode The permissions mode
     * @return void
     * @throws IOException On any directory creation failure
     */
    public function mkdir(iterable|string $dirs, int $mode = 0777): void
    {
        try {
            $this->filesystem->mkdir($dirs, $mode);
        } catch (BaseIOException $exception) {
            throw new IOException('Failed to create directory.', 0, $exception, $exception->getPath());
        } catch (Throwable $exception) {
            throw new IOException('Failed to create directory.', 0, $exception);
        }
    }

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     * @return void
     * @throws IOException When removal fails
     */
    public function remove(iterable|string $files): void
    {
        try {
            $this->filesystem->remove($files);
        } catch (BaseIOException $exception) {
            throw new IOException('Failed to remove files or directories.', 0, $exception, $exception->getPath());
        } catch (Throwable $exception) {
            throw new IOException('Failed to remove files or directories.', 0, $exception);
        }
    }
}
