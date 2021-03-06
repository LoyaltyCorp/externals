<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces;

/**
 * Interface to be implemented by any entity
 * that is acting as an user.
 */
interface UserInterface
{
    /**
     * Get unique user id.
     *
     * @return int|string|null
     */
    public function getUniqueId();
}
