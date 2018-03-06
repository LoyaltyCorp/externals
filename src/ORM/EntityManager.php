<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use EoneoPay\External\ORM\Exceptions\EntityValidationFailedException;
use EoneoPay\External\ORM\Exceptions\ORMException;
use EoneoPay\External\ORM\Interfaces\EntityInterface;
use EoneoPay\External\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\External\ORM\Interfaces\Query\FilterCollectionInterface;
use EoneoPay\External\ORM\Interfaces\RepositoryInterface;
use EoneoPay\External\ORM\Query\FilterCollection;
use Exception;

class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(DoctrineEntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Flush unit of work to the database
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     */
    public function flush(): void
    {
        $this->callMethod('flush');
    }

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \EoneoPay\External\ORM\Interfaces\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollection($this->entityManager->getFilters());
    }

    /**
     * Gets the repository from an entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return \EoneoPay\External\ORM\Interfaces\RepositoryInterface
     */
    public function getRepository(string $class): RepositoryInterface
    {
        return new Repository($this->entityManager->getRepository($class));
    }

    /**
     * Merge entity to the database, similar to REPLACE in SQL
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to merge to the database
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     */
    public function merge(EntityInterface $entity): void
    {
        $this->callMethod('merge', $entity);
    }

    /**
     * Persist entity to the database
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to persist to the database
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     */
    public function persist(EntityInterface $entity): void
    {
        $this->callMethod('persist', $entity);
    }

    /**
     * Remove entity from the database.
     *
     * @param \EoneoPay\External\ORM\Interfaces\EntityInterface $entity The entity to remove from the database
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    public function remove(EntityInterface $entity): void
    {
        $this->callMethod('remove', $entity);
    }

    /**
     * Call a method on the entity manager and catch any exception
     *
     * @param string $method The method to call
     * @param mixed $parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If entity validation fails
     * @throws \EoneoPay\External\ORM\Exceptions\ORMException If database returns an error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            return \call_user_func_array([$this->entityManager, $method], $parameters ?? []);
        } catch (Exception $exception) {
            // Throw directly exceptions from this package
            if ($exception instanceof EntityValidationFailedException) {
                throw $exception;
            }

            // Wrap others in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, $exception);
        }
    }
}
