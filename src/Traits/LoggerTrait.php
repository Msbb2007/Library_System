<?php

namespace App\Traits;

trait LoggerTrait{
    protected string $logFile = __DIR__ . '/../../data/activity.log';

    public function log(string $message): void{
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }
}