<?php

namespace App\Models;

class Member extends User{
    public function __construct(string $name, string $phone, string $id = ''){
        parent::__construct($name, $phone, 'member', $id);
    }

    public function getPermissions(): array{
        return ['borrow_book', 'return_book', 'view_books'];
    }

    public static function fromArray(array $data): self{
        return new self($data['name'], $data['phone'], $data['id'] ?? '');
    }
}