<?php

namespace App\Services;

class FileStorage{
    private string $filePath;

    public function __construct(string $filePath){
        $this->filePath = $filePath;
        $this->ensureFileExists();
    }

    // اطمینان از وجود فایل و پوشه data
    private function ensureFileExists(): void{
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // خواندن تمام داده‌ها از فایل JSON
    public function load(): array{
        $content = file_get_contents($this->filePath);
        return json_decode($content, true) ?? [];
    }

    // ذخیره داده‌ها در فایل JSON
    public function save(array $data): bool{
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($this->filePath, $json) !== false;
    }
}