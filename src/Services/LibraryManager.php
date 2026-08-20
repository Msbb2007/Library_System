<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Models\Admin;
use App\Models\Member;
use App\Traits\LoggerTrait;

class LibraryManager{
    use LoggerTrait;

    private FileStorage $bookStorage;
    private FileStorage $userStorage;

    public function __construct(){
        $baseDir = __DIR__ . '/../../data/';
        $this->bookStorage = new FileStorage($baseDir . 'books.json');
        $this->userStorage = new FileStorage($baseDir . 'users.json');
    }


    public function addBook(string $title, string $author, string $id = ''): Book{
        $books = $this->getAllBooks();
    
        $newBook = new Book($title, $author, $id);
        $books[] = $newBook;

        $this->saveBooks($books);
        $this->log("کتاب جدید اضافه شد: {$title} (شناسه: {$newBook->getId()})");

        return $newBook;
    }

    public function getAllBooks(): array{
        $data = $this->bookStorage->load();
        // استفاده از Anonymous Function برای تبدیل داده‌های خام به اشیاء Book
        return array_map(fn($item) => Book::fromArray($item), $data);
    }

    public function findBookById(string $id): ?Book{
        $books = $this->getAllBooks();
        foreach ($books as $book) {
            if ($book->getId() === $id) {
                return $book;
            }
        }
        return null;
    }

    public function searchBooks(string $keyword): array{
    $books = $this->getAllBooks();
    return array_filter($books, function (Book $book) use ($keyword) {
        $search = mb_strtolower($keyword);
        return mb_strpos(mb_strtolower($book->getTitle()), $search) !== false ||
               mb_strpos(mb_strtolower($book->getAuthor()), $search) !== false ||
               mb_strpos(mb_strtolower($book->getId()), $search) !== false;
    });
    }

    // --- عملیات امانت و بازگشت ---

    public function borrowBook(string $bookId, string $userId): bool{
    $books = $this->getAllBooks();
    foreach ($books as $book) {
        if ($book->getId() === $bookId) {
            if ($book->borrow($userId)) {
                $this->saveBooks($books);
                $this->log("کتاب با شناسه {$bookId} توسط کاربر {$userId} امانت گرفته شد.");
                return true;
            }
            return false;
        }
    }
    return false;
    }

    public function returnBook(string $bookId): bool{
        $books = $this->getAllBooks();
        foreach ($books as $book) {
            if ($book->getId() === $bookId) {
                if ($book->returnItem()) {
                    $this->saveBooks($books);
                    $this->log("کتاب بازگردانده شد: {$book->getTitle()} (شناسه: {$bookId})");
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    public function updateBook(string $id, string $newTitle, string $newAuthor, bool $isBorrowed): bool{
    $books = $this->getAllBooks();
    foreach ($books as $book) {
        if ($book->getId() === $id) {
            $borrowedBy = $isBorrowed ? $book->getBorrowedBy() : null;

            $updatedBook = new Book(
                $newTitle,
                $newAuthor,
                $id,
                $isBorrowed,
                $borrowedBy
            );

            $index = array_search($book, $books);
            $books[$index] = $updatedBook;

            $this->saveBooks($books);
            $this->log("اطلاعات و وضعیت کتاب با شناسه {$id} ویرایش شد.");
            return true;
        }
    }
    return false;
    }

    public function deleteBook(string $id): bool{
    $books = $this->getAllBooks();
    $initialCount = count($books);

    // حذف کتاب با شناسه مشخص شده
    $books = array_filter($books, function (Book $book) use ($id) {
        return $book->getId() !== $id;
    });

    if (count($books) < $initialCount) {
        $this->saveBooks(array_values($books));
        $this->log("کتاب با شناسه {$id} حذف شد.");
        return true;
    }

    return false;
    }

    // --- مدیریت کاربران ---

    public function addUser(string $name, string $phone, string $role = 'member'): User{
        $users = $this->getAllUsers();
        
        $user = ($role === 'admin') 
            ? new Admin($name, $phone) 
            : new Member($name, $phone);

        $users[] = $user;
        $this->saveUsers($users);
        $this->log("کاربر جدید ثبت شد: {$name} - نقش: {$role}");

        return $user;
    }

    public function getAllUsers(): array{
        $data = $this->userStorage->load();
        return array_map(function ($item) {
            return ($item['role'] === 'admin') 
                ? Admin::fromArray($item) 
                : Member::fromArray($item);
        }, $data);
    }

    public function searchUsers(string $keyword): array{
    $users = $this->getAllUsers();
    return array_filter($users, function (User $user) use ($keyword) {
        $search = mb_strtolower($keyword);
        return mb_strpos(mb_strtolower($user->getName()), $search) !== false ||
               mb_strpos(mb_strtolower($user->getPhone()), $search) !== false ||
               mb_strpos(mb_strtolower($user->getId()), $search) !== false ||
               mb_strpos(mb_strtolower($user->getRole()), $search) !== false;
    });
    }

    public function findUserByPhone(string $phone): ?User{
    $users = $this->getAllUsers();
    foreach ($users as $user) {
        if ($user->getPhone() === $phone) {
            return $user;
        }
    }
    return null;
    }

    // --- متدهای کمکی ذخیره‌سازی ---

    private function saveBooks(array $books): void{
        $data = array_map(fn(Book $book) => $book->toArray(), $books);
        $this->bookStorage->save($data);
    }

    private function saveUsers(array $users): void{
        $data = array_map(fn(User $user) => $user->toArray(), $users);
        $this->userStorage->save($data);
    }
}