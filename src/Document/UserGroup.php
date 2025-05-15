<?php

namespace App\Document;

use App\Repository\UserGroupRepository;
use Nucleos\UserBundle\Model\Group as BaseGroup;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: 'user_group', repositoryClass: UserGroupRepository::class)]
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