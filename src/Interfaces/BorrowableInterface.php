<?php

namespace App\Interfaces;

interface BorrowableInterface{
    public function borrow(): bool;
    public function returnItem(): bool;
    public function isBorrowed(): bool;
}