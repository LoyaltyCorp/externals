<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @method null|ParentEntityStub getParent()
 */
class ChildEntityStub extends EntityStub
{
    /**
     * @ORM\ManyToOne(targetEntity="Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub", inversedBy="children")
     *
     * @var \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub
     */
    protected $parent;

    /**
     * ChildEntityStub constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct($data);

        $this->parent = new ParentEntityStub();
        $this->parent->getChildren()->add($this);
    }

    /**
     * Return an array of annotation/attribute pairs to search for properties in
     *
     * Note: Changing this array will cause the test testPropertyAnnotationsContainsInvalidClassAndAttribute() to fail
     *
     * @return array
     *
     * @see \Tests\EoneoPay\Externals\ORM\EntityTest::testPropertyAnnotationsContainsInvalidClassAndAttribute
     */
    public function getPropertyAnnotations(): array
    {
        parent::getPropertyAnnotations();

        return [];
    }

    /**
     * Set parent.
     *
     * @param \Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub $parent
     *
     * @return \Tests\EoneoPay\Externals\ORM\Stubs\ChildEntityStub
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\InvalidMethodCallException If the method doesn't exist on an entity
     */
    public function setParent(ParentEntityStub $parent): self
    {
        return $this->associate('parent', $parent, 'children');
    }
}
