<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs\Factories;

use EoneoPay\Externals\ORM\EntityFactory;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Tests\EoneoPay\Externals\ORM\Stubs\ParentEntityStub;

class ParentEntityStubEntityFactory extends EntityFactory
{
    /**
     * Create an entity.
     *
     * @param mixed[] $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     */
    public function create(array $data): EntityInterface
    {
        return new ParentEntityStub($data);
    }

    /**
     * Get default date used for test.
     *
     * @return mixed[]
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
