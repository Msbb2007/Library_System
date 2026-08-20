<?php

namespace App\Models;

use App\Interfaces\BorrowableInterface;

class Book implements BorrowableInterface
{
    private string $id;
    private string $title;
    private string $author;
    private bool $isBorrowed;
    private ?string $borrowedBy; 

    public function __construct(string $title, string $author, string $id = '', bool $isBorrowed = false, ?string $borrowedBy = null){
       $this->id = !empty($id) ? $id : uniqid('bk_');
       $this->title = $title;
       $this->author = $author;
       $this->isBorrowed = $isBorrowed;
       $this->borrowedBy = $borrowedBy;
    }  

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function isBorrowed(): bool { return $this->isBorrowed; }
    public function getBorrowedBy(): ?string { return $this->borrowedBy; }

    public function borrow(?string $userId = null): bool
    {
        if ($this->isBorrowed) {
            return false;
        }
        $this->isBorrowed = true;
        $this->borrowedBy = $userId;
        return true;
    }

    public function returnItem(): bool
    {
        if (!$this->isBorrowed) {
            return false;
        }
        $this->isBorrowed = false;
        $this->borrowedBy = null;
        return true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isBorrowed' => $this->isBorrowed,
            'borrowedBy' => $this->borrowedBy
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['author'],
            $data['id'] ?? '',
            $data['isBorrowed'] ?? false,
            $data['borrowedBy'] ?? null
        );
    }
}