<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Stubs;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\External\ORM\Entity;

/**
 * @ORM\Entity(repositoryClass="Tests\EoneoPay\External\ORM\Stubs\NotFoundRepository")
 */
class EntityStubWithNotFoundRepository extends Entity
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->entityId
        ];
    }
}