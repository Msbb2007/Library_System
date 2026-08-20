<?php

namespace App\Models;

class Admin extends User{
    public function __construct(string $name, string $phone, string $id = ''){
        parent::__construct($name, $phone, 'admin', $id);
    }

    public function getPermissions(): array{
        return ['add_book', 'delete_book', 'view_books', 'manage_users'];
    }

    public static function fromArray(array $data): self{
        return new self($data['name'], $data['phone'], $data['id'] ?? '');
    }
}