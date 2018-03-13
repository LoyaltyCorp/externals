<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Filesystem\Exceptions\FileNotFoundException;
use EoneoPay\External\Filesystem\Interfaces\CloudFilesystemInterface;
use EoneoPay\External\Filesystem\Interfaces\DiskFilesystemInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException as ContractedFileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class Filesystem implements CloudFilesystemInterface, DiskFilesystemInterface
{
    /**
     * Contracted filesystem instance
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Create new filesystem instance
     *
     * @param \Illuminate\Contracts\Filesystem\Filesystem $contract Contracted filesystem isntance
     */
    public function __construct(FilesystemContract $contract)
    {
        $this->filesystem = $contract;
    }

    /**
     * Check whether a file exists
     *
     * @param string $filename The file to check
     *
     * @return bool
     */
    public function exists(string $filename): bool
    {
        return $this->filesystem->exists($filename);
    }

    /**
     * Get the full path to a file
     *
     * @param string|null $filename The filename to append to the path
     *
     * @return string
     */
    public function path(?string $filename = null): string
    {
        /** @noinspection PhpUndefinedMethodInspection */
        // Method is in \Illuminate\Filesystem\FilesystemAdapter
        // @see: https://github.com/illuminate/contracts/pull/6
        return $this->filesystem->path((string)$filename);
    }

    /**
     * Get contents of a file
     *
     * @param string $filename The filename to read from
     *
     * @return string
     *
     * @throws \EoneoPay\External\Filesystem\Exceptions\FileNotFoundException If file is not found
     */
    public function read(string $filename): string
    {
        try {
            return $this->filesystem->get($filename);
        } catch (ContractedFileNotFoundException $exception) {
            // Wrap exception
            throw new FileNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Write a file to the filesystem
     *
     * @param string $filename The filename to write to
     * @param string $contents The contents to write to the file
     *
     * @return bool
     */
    public function write(string $filename, string $contents): bool
    {
        return $this->filesystem->put($filename, $contents);
    }
}
