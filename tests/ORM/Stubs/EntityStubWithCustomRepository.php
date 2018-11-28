<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;

/**
 * @ORM\Entity(repositoryClass="Tests\EoneoPay\Externals\ORM\Stubs\EntityCustomRepository")
 */
class EntityStubWithCustomRepository extends Entity
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
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'id' => $this->entityId
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getIdProperty(): string
    {
        return 'entityId';
    }
}
