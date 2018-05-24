<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs\Factories;

use EoneoPay\Externals\ORM\EntityFactory;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;

class EntityStubEntityFactory extends EntityFactory
{
    /**
     * Create an entity.
     *
     * @param mixed[]|null $data
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     */
    public function create(?array $data = null): EntityInterface
    {
        return new EntityStub($data ?? $this->getDefaultData());
    }

    /**
     * Get default date used for test.
     *
     * @return mixed[]
     */
    public function getDefaultData(): array
    {
        return [
            'integer' => 1,
            'string' => 'string'
        ];
    }
}
