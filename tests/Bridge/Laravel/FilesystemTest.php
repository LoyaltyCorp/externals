<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Filesystem;
use EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter as ContractedFilesystem;
use League\Flysystem\Filesystem as Flysystem;
use Tests\EoneoPay\Externals\Stubs\VirtualFilesystemAdapterStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Filesystem
 */
class FilesystemTest extends TestCase
{
    /**
     * Test filesystem can retrieve files from disk
     *
     * @return void
     *
     * @throws \org\bovigo\vfs\vfsStreamException
     */
    public function testFilesystemCanListFilesInDirectoryRecursively(): void
    {
        $filesystem = $this->createFilesystem();

        $filenames = [
            'test/test.txt',
            'test/sub-directory/test.txt'
        ];

        foreach ($filenames as $filename) {
            $filesystem->write($filename, 'contents');
        }

        $files = $filesystem->files('test', true);

        self::assertCount(2, $files);

        foreach ($filenames as $filename) {
            self::assertContains($filename, $files);
        }
    }

    /**
     * Test filesystem can write files to disk
     *
     * @return void
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testFilesystemCanReadAndWritesFilesToDisk(): void
    {
        $filesystem = $this->createFilesystem();

        $filename = 'test/test.txt';
        $contents = 'contents';

        self::assertTrue($filesystem->write($filename, $contents));
        self::assertSame(\sprintf('vfs://root/%s', $filename), $filesystem->path($filename));
        self::assertTrue($filesystem->exists($filename));
        self::assertSame($contents, $filesystem->read($filename));
        self::assertTrue($filesystem->remove($filename));
        self::assertFalse($filesystem->exists($filename));
    }

    /**
     * Test reading a file which doesn't exists throws an exception
     *
     * @return void
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    public function testFilesystemThrowsExceptionIfReadiningNonExistentFile(): void
    {
        $filesystem = $this->createFilesystem();

        $this->expectException(FileNotFoundException::class);

        $filesystem->read('non-existent.file');
    }

    /**
     * Create a filesystem instance for testing
     *
     * @return \EoneoPay\Externals\Bridge\Laravel\Filesystem
     *
     * @throws \org\bovigo\vfs\vfsStreamException If stream can't be created
     */
    private function createFilesystem(): Filesystem
    {
        return new Filesystem(new ContractedFilesystem(new Flysystem(new VirtualFilesystemAdapterStub())));
    }
}
