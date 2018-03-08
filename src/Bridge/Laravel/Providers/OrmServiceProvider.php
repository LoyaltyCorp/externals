<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel\Providers;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use EoneoPay\External\ORM\EntityManager;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Support\ServiceProvider;

class OrmServiceProvider extends ServiceProvider
{
    /**
     * Register ORM services.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function register(): void
    {
        // Extend Doctrine EntityManager with our EntityManager
        $this->app->extend('em', function (DoctrineEntityManager $entityManager) {
            return new EntityManager($entityManager);
        });

        // Create alias to the package interface for DI purposes
        $this->app->alias('em', EntityManagerInterface::class);
    }
}
