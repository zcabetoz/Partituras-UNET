<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Nucleos\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
#[MongoDBUnique(fields: ['username'])]
class User extends BaseUser
{
    #[ODM\Id(strategy: 'auto')]
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

    #[ODM\Field(type: 'string')]
    protected string $nombre;

    #[ODM\Field(type: 'string')]
    protected string $shema_version;

    #[ODM\Field(type: 'string')]
    protected ?string $email;

    #[ODM\Field(type: 'int')]
    protected int $loginFail = 0;

    public function __construct()
    {
        parent::__construct();
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

    public function getShemaVersion(): ?string
    {
        return $this->shema_version;
    }

    public function setShemaVersion(?string $shema_version): void
    {
        $this->shema_version = $shema_version;
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
}
