<?php

namespace App\Document;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Nucleos\UserBundle\Model\User as BaseUser;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(repositoryClass: UserRepository::class)]
#[MongoDBUnique(fields: ['username'])]
class User extends BaseUser implements Serializable
{
    #[MongoDB\Id(strategy: 'auto')]
    protected string $id;

    #[Assert\NotBlank(message: 'La contraseña es obligatoria')]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@~`!#$%^&*=_+:;'<>?,.|])[A-Za-z\d@~`!#$%^&*=_+:;'<>?,.|]{1,}$/",
        message: "La contraseña debe contener números, al menos un carácter especial, letras mayúsculas y minúsculas"
    )]
    #[Assert\Length(
        min: 8,
        max: 15,
        minMessage: "La contraseña debe contener mínimo 8 carácteres",
        maxMessage: "La contraseña debe contener máximo 15 carácteres"
    )]
    protected ?string $plainPassword;

    #[MongoDB\Field(type: 'string')]
    protected ?string $nombre = null;

    #[MongoDB\Field(type: 'string')]
    protected ?string $email;

    #[MongoDB\Field(type: 'int')]
    protected int $loginFail = 0;

    #[MongoDB\ReferenceMany(targetDocument: UserGroup::class)]
    protected Collection $groups;

    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLoginFail(): int
    {
        return $this->loginFail ?? 0;
    }

    public function setLoginFail(int $loginFail): void
    {
        $this->loginFail = $loginFail;
    }

    public function incLoginFail(): void
    {
        $this->loginFail++;
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data, ['allowed_classes' => false]));
    }

    public function __serialize(): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
            'enabled'  => $this->enabled,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id       = $data['id'];
        $this->username = $data['username'];
        $this->email    = $data['email'];
        $this->password = $data['password'];
        $this->enabled  = $data['enabled'];
    }
}
