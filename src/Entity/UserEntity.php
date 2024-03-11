<?php

namespace App\Entity;

class UserEntity{
    private ?int $id;
    private \DateTimeImmutable $createdAt;
    private string $email;
    private string $password;

    public function __construct() {
        $this->createdAt = new \DateTimeImmutable();
    }

    function getCreatedAt(): \DateTimeImmutable{
        return $this->createdAt;
    }

    function setCreatedAt(\DateTimeImmutable $createdAt): void{
        $this->createdAt = $createdAt;
    }

    function getEmail(): string{
        return $this->email;
    }

    function setEmail(string $email): void{
        $this->email = $email;
    }

    function getPassword(): string{
        return $this->password;
    }
 
}