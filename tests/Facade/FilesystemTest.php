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

namespace Tests\Jojo1981\GuzzleMiddlewares\Facade;

use Jojo1981\GuzzleMiddlewares\Exception\IOException;
use Jojo1981\GuzzleMiddlewares\Facade\Filesystem;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function chmod;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function rmdir;
use function unlink;

final class FilesystemTest extends TestCase
{
    /** @var string */
    private string $cacheDir = __DIR__ . '/../../var/cache';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (is_dir($this->cacheDir)) {
            rmdir($this->cacheDir);
        }
    }


    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws IOException
     */
    public function testDumpFileSuccess(): void
    {
        $file = $this->cacheDir . '/success.txt';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($file, 'test content');
        self::assertFileExists($file);
        self::assertSame('test content', file_get_contents($file));
        unlink($file);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testDumpFileFailure(): void
    {
        $this->expectException(IOException::class);
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile('/invalid/path/file.txt', 'fail');
        } catch (IOException $caughtException) {
            self::assertEquals('Failed to dump file "/invalid/path/file.txt".', $caughtException->getMessage());
            self::assertEquals(0, $caughtException->getCode());
            self::assertNotNull($caughtException->getPrevious());
            self::assertEquals('/invalid/path', $caughtException->getPath());
            throw $caughtException;
        }
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testExistsReturnsTrueForExistingFile(): void
    {
        $file = $this->cacheDir . '/exists.txt';
        file_put_contents($file, 'exists');
        $filesystem = new Filesystem();
        self::assertTrue($filesystem->exists($file));
        unlink($file);
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testExistsReturnsFalseForNonExistingFile(): void
    {
        $file = $this->cacheDir . '/does_not_exist.txt';
        $filesystem = new Filesystem();
        self::assertFalse($filesystem->exists($file));
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     */
    public function testExistsReturnsFalseOnThrowable(): void
    {
        $filesystem = new Filesystem();
        self::assertFalse($filesystem->exists($this->cacheDir . '/' . str_repeat('a', 4100)));
    }

    /**
     * @return void
     * @throws IOException
     * @throws ExpectationFailedException
     */
    public function testMkdirSuccess(): void
    {
        $dir = $this->cacheDir . '/new_dir';
        $filesystem = new Filesystem();
        $filesystem->mkdir($dir);
        self::assertDirectoryExists($dir);
        rmdir($dir);
    }

    /**
     * @return void
     * @throws IOException
     */
    public function testMkdirFailure(): void
    {
        $this->expectException(IOException::class);
        $filesystem = new Filesystem();
        try {
            // Attempt to create a directory in an invalid location
            $filesystem->mkdir('/invalid/path/new_dir');
        } catch (IOException $caughtException) {
            self::assertEquals('Failed to create directory.', $caughtException->getMessage());
            self::assertEquals(0, $caughtException->getCode());
            self::assertNotNull($caughtException->getPrevious());
            self::assertEquals('/invalid/path/new_dir', $caughtException->getPath());
            throw $caughtException;
        }
    }

    /**
     * @return void
     * @throws IOException
     * @throws ExpectationFailedException
     */
    public function testRemoveSuccess(): void
    {
        $file = $this->cacheDir . '/remove.txt';
        file_put_contents($file, 'to be removed');
        $filesystem = new Filesystem();
        $filesystem->remove($file);
        self::assertFileDoesNotExist($file);
    }

    /**
     * @return void
     * @throws IOException
     * @throws ExpectationFailedException
     */
    public function testRemoveFailure(): void
    {
        $dir = $this->cacheDir . '/protected_dir';
        $file = $dir . '/protected_file.txt';
        mkdir($dir);
        file_put_contents($file, 'content');
        // Remove write permission from the directory
        chmod($dir, 0555); // Read and execute only, no write

        $this->expectException(IOException::class);
        $filesystem = new Filesystem();
        try {
            $filesystem->remove($file);
        } catch (IOException $caughtException) {
            self::assertEquals('Failed to remove files or directories.', $caughtException->getMessage());
            self::assertEquals(0, $caughtException->getCode());
            self::assertNotNull($caughtException->getPrevious());
            self::assertNull($caughtException->getPath());
            throw $caughtException;
        } finally {
            chmod($dir, 0755); // Restore permissions
            if (file_exists($file)) {
                unlink($file);
            }
            rmdir($dir);
        }
    }
}
