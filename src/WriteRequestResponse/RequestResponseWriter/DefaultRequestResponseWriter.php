<?php declare(strict_types=1);
/*
 * This file is part of the jojo1981/guzzle-middlewares package
 *
 * Copyright (c) 2022 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\GuzzleMiddlewares\WriteRequestResponse\RequestResponseWriter;

use FilesystemIterator;
use Jojo1981\GuzzleMiddlewares\WriteRequestResponse\RequestResponseWriterInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use UnexpectedValueException;
use function rtrim;

/**
 * @package Jojo1981\GuzzleMiddlewares\WriteRequestResponse
 */
final class DefaultRequestResponseWriter implements RequestResponseWriterInterface
{
    /** @var string */
    private string $path;

    /** @var Filesystem */
    private Filesystem $filesystem;

    /** @var bool */
    private bool $prepared = false;

    /**
     * @param string $path
     * @param Filesystem $filesystem
     */
    public function __construct(string $path, Filesystem $filesystem)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filename
     * @param string $content
     * @return void
     * @throws UnexpectedValueException
     * @throws IOException
     */
    public function write(string $filename, string $content): void
    {
        $this->prepareWhenNotPreparedAlready();
        $this->filesystem->dumpFile($this->path . DIRECTORY_SEPARATOR . $filename, $content);
    }

    /**
     * @return void
     * @throws UnexpectedValueException
     * @throws IOException
     */
    private function prepareWhenNotPreparedAlready(): void
    {
        if (false === $this->prepared) {
            if ($this->filesystem->exists($this->path)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                $this->filesystem->remove($files);
            } else {
                $this->filesystem->mkdir($this->path);
            }

            $this->prepared = true;
        }
    }
}
