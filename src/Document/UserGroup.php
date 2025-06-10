<?php

namespace App\Document;

use Nucleos\UserBundle\Model\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: 'UserGroup')]
class UserGroup extends BaseGroup
{
    #[MongoDB\Id(strategy: 'auto')]
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nombre' => $this->getName(),
        ];
    }
}