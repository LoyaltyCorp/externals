<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Request\Interfaces;

interface RequestInterface
{
    /**
     * Get a header by name
     *
     * @param string $key The key to find
     * @param mixed $default The default to return if key isn't found
     *
     * @return mixed
     */
    public function getHeader(string $key, $default = null);

    /**
     * Determine if the request contains a given input item key
     *
     * @param string $key The key to find
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Retrieve an input item from the request
     *
     * @param string|null $key The key to retrieve from the input
     * @param mixed $default The default value to use if key isn't set
     *
     * @return mixed
     */
    public function input(?string $key = null, $default = null);

    /**
     * Set a header on the request
     *
     * @param string $key The key to set
     * @param mixed $value The value to set against the header
     *
     * @return static
     */
    public function setHeader(string $key, $value);

    /**
     * Retrieve the entire request as an array
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
