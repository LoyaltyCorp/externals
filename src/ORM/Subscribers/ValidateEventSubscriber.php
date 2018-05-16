<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use EoneoPay\Externals\Logger\Interfaces\LoggerInterface;
use EoneoPay\Externals\Logger\Logger;
use EoneoPay\Externals\ORM\Exceptions\DefaultEntityValidationFailedException;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Utils\AnnotationReader;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\ValidationException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) High coupling to cover decoupling between subscriber and application
 */
class ValidateEventSubscriber implements EventSubscriber
{
    /**
     * @var \EoneoPay\Externals\Logger\Interfaces\LoggerInterface
     */
    private $logger;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    private $validationFactory;

    /**
     * ValidateEventSubscriber constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validationFactory
     * @param null|\EoneoPay\Externals\Logger\Interfaces\LoggerInterface $logger
     */
    public function __construct(ValidationFactory $validationFactory, ?LoggerInterface $logger = null)
    {
        $this->validationFactory = $validationFactory;
        $this->logger = $logger ?? new Logger();
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    /**
     * Validate entity against rule set on insert
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /**
     * Validate entity against rule set on update
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException Inherited, if validation fails
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->callValidator($eventArgs);
    }

    /** @noinspection PhpDocRedundantThrowsInspection Exception thrown dynamically */
    /**
     * Call validator on an object
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    private function callValidator(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (($entity instanceof EntityInterface) === false
            || \method_exists($entity, 'getRules') === false
            || \is_array($entity->getRules()) === false) {
            return;
        }

        try {
            /** @var \Illuminate\Validation\Validator $validator */
            $validator = $this->validationFactory->make($this->getEntityContents($entity), $entity->getRules());
            $validator->validate();
        } catch (ValidationException $exception) {
            $exceptionClass = \method_exists($entity, 'getValidationFailedException')
                ? $entity->getValidationFailedException()
                : DefaultEntityValidationFailedException::class;

            throw new $exceptionClass(
                $exception->getMessage(),
                $exception->getCode(),
                $exception,
                $exception->errors()
            );
        }
    }

    /**
     * Get list of doctrine annotations classes we are looking for to get entity contents.
     *
     * @return string[]
     */
    private function getDoctrineAnnotations(): array
    {
        return [
            Column::class,
            OneToOne::class,
            OneToMany::class,
            ManyToOne::class,
            ManyToMany::class
        ];
    }

    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray().
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return mixed[]
     */
    private function getEntityContents(EntityInterface $entity): array
    {
        try {
            $mapping = (new AnnotationReader())->getClassPropertyAnnotations(
                \get_class($entity),
                $this->getDoctrineAnnotations()
            );
            // Can't test exception since opcache config can only be set in php.ini
            // @codeCoverageIgnoreStart
        } catch (\Exception $exception) {
            $this->logger->exception($exception);

            return [];
        }
        // @codeCoverageIgnoreEnd

        $contents = [];
        foreach ($mapping as $property => $annotations) {
            $getter = \sprintf('get%s', \ucfirst($property));
            $annotation = \reset($annotations);

            $contents[$annotation->name ?? $property] = $entity->$getter();
        }

        return $contents;
    }
}
