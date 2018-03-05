<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\External\ORM\Entity;

/**
 * @method int getInteger()
 * @method string getEntityId()
 * @method string getString()
 * @method self setInteger(int $integer)
 * @method self setEntityId(string $entityId)
 * @method self setString(string $string)
 *
 * The following methods are only used for testing validity of __call
 * @method string|null getAnnotationName()
 * @method null getInvalid()
 * @method self setAnnotationName(string $name)
 * @method null whenString()
 *
 * @ORM\Entity
 */
class EntityStub extends Entity
{
    /**
     * Primary id
     *
     * @var string
     *
     * @ORM\Column(type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;

    /**
     * Integer test
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true})
     */
    protected $integer;

    /**
     * String test
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, name="annotation_name", nullable=true)
     */
    protected $string;

    /**
     * Return an array of annotation/attribute pairs to search for properties in
     *
     * Note: Changing this array will cause the test testPropertyAnnotationsContainsInvalidClassAndAttribute() to fail
     *
     * @return array
     *
     * @see \Tests\EoneoPay\External\ORM\EntityTest::testPropertyAnnotationsContainsInvalidClassAndAttribute
     */
    public function getPropertyAnnotations(): array
    {
        /** @noinspection PhpUndefinedClassInspection InvalidClass is intentionally invalid */
        return [
            InvalidClass::class => 'name',
            ORM\Column::class => 'name',
            ORM\Id::class => 'invalid'
        ];
    }

    /**
     * Get the contents of the entity as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
