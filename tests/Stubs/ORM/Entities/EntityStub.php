<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs\ORM\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;
use EoneoPay\Externals\ORM\Traits\HasTransformers;
use Gedmo\Mapping\Annotation as Gedmo;
use LaravelDoctrine\Extensions\SoftDeletes\SoftDeletes;

/**
 * @method int|null getInteger()
 * @method string|null getEntityId()
 * @method string|null getString()
 * @method bool hasString()
 * @method bool isString()
 * @method self setInteger(int $integer)
 * @method self setEntityId(string $entityId)
 * @method self setString(string $string)
 *
 * The following methods are only used for testing validity of __call
 * @method string|null getAnnotationName()
 * @method null|null getInvalid()
 * @method self setAnnotationName(string $name)
 * @method null whenString()
 *
 * @ORM\Entity()
 *
 * @Gedmo\SoftDeleteable()
 */
class EntityStub extends Entity
{
    use HasTransformers;
    use SoftDeletes;

    /**
     * Primary id.
     *
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $entityId;

    /**
     * Integer test.
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, options={"unsigned" = true})
     */
    protected $integer;

    /**
     * String test.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, name="annotation_name", nullable=true)
     */
    protected $string;

    /**
     * Function exclusively for test purposes to test uniqueRuleAsString.
     *
     * @param string[]|null $wheres
     *
     * @return string
     */
    public function getEmailUniqueRuleForTest(?array $wheres = null): string
    {
        return $this->uniqueRuleAsString('email', $wheres ?? []);
    }

    /**
     * Function exclusively for test purposes to test instanceOfRuleAsString.
     *
     * @param string $class
     *
     * @return string
     */
    public function getInstanceOfRuleForTest(string $class): string
    {
        return $this->instanceOfRuleAsString($class);
    }

    /**
     * Setter which sets value on another property.
     * This setter is part of a test which checks if setters can be called
     * without needing to have a corresponding property on the entity.
     * In this example $nonExistent does not need to be a property in this class,
     * but the setter would still be called when we do a new EntityStub(['nonExistent' => 'value'])
     *
     * @param string $value
     *
     * @return $this
     */
    public function setNonExistent(string $value): self
    {
        $this->string = $value;

        return $this;
    }

    /**
     * Get the contents of the entity as an array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    /**
     * Make sure that string is a string.
     *
     * @return void
     */
    public function transformString(): void
    {
        $this->transformToString('string');
    }

    /**
     * {@inheritdoc}
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }
}
