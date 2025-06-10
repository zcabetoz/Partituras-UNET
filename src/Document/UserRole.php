<?php

namespace App\Document;

use App\Repository\UserRoleRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: 'UserRole', repositoryClass: UserRoleRepository::class)]
class UserRole
{
    #[MongoDB\Id(strategy: 'auto')]
    protected string $id;

    #[MongoDB\Field(type: 'string')]
    protected ?string $role = null;

    #[MongoDB\Field(type: 'string')]
    protected ?string $description = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'role' => $this->getRole(),
            'description' => $this->getDescription(),
        ];
    }
}