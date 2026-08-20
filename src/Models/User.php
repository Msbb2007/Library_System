<?php

namespace App\Models;

abstract class User{
    protected string $id;
    protected string $name;
    protected string $phone;
    protected string $role;

    public function __construct(string $name, string $phone, string $role, string $id = ''){
        $this->id = $id !== '' ? $id : uniqid('usr_');
        $this->name = $name;
        $this->phone = $phone;
        $this->role = $role;
    }

    public function getId(): string{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getPhone(): string{
        return $this->phone;
    }

    public function getRole(): string{
        return $this->role;
    }

    abstract public function getPermissions(): array;

    public function toArray(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'role' => $this->role
        ];
    }
}