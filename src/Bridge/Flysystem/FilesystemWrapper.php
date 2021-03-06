<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Flysystem;

use EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException;
use EoneoPay\Externals\Filesystem\Interfaces\FilesystemInterface;
use League\Flysystem\FileNotFoundException as FlysystemFileNotFoundException;
use League\Flysystem\FilesystemInterface as FlysystemInterface;

class FilesystemWrapper implements FilesystemInterface
{
    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $flysystem;

    /**
     * FilesystemWrapper constructor.
     *
     * @param \League\Flysystem\FilesystemInterface $flysystem
     */
    public function __construct(FlysystemInterface $flysystem)
    {
        $this->flysystem = $flysystem;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function append(string $filename, string $contents): bool
    {
        /** @var resource $existing */
        $existing = $this->readStream($filename);

        /** @var resource $writeStream */
        $writeStream = \fopen('php://memory', 'ab+');

        \stream_copy_to_stream($existing, $writeStream);
        \fwrite($writeStream, $contents);

        return $this->flysystem->updateStream($filename, $writeStream);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $filename): bool
    {
        return $this->flysystem->has($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function files(?string $directory = null, ?bool $recursive = null): array
    {
        return $this->flysystem->listContents($directory ?? '', $recursive ?? false);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated See PYMT-1581.
     */
    public function path(?string $filename = null): string
    {
        return $filename ?? '';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Externals\Filesystem\Exceptions\FileNotFoundException
     */
    public function read(string $filename): string
    {
        try {
            $response = $this->flysystem->read($filename);
        } catch (FlysystemFileNotFoundException $exception) {
            throw new FileNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        }
        if ($response === false) {
            throw new FileNotFoundException(\sprintf('File not found at path: %s', $filename));
        }
        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function readStream(string $filename)
    {
        $stream = $this->flysystem->readStream($filename);
        if (\is_resource($stream) !== true) {
            throw new FileNotFoundException(\sprintf('File not found at path: %s', $filename));
        }
        return $stream;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function remove(string $filename): bool
    {
        $metadata = $this->flysystem->getMetadata($filename);
        if (\is_array($metadata) && \array_key_exists('type', $metadata) && $metadata['type'] === 'file') {
            return $this->flysystem->delete($filename);
        }
        return $this->flysystem->deleteDir($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function write(string $filename, string $contents): bool
    {
        return $this->flysystem->write($filename, $contents);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function writeStream(string $path, $resource, ?array $options = null): bool
    {
        return $this->flysystem->writeStream($path, $resource);
    }
}
