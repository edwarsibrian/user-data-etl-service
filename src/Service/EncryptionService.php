<?php

namespace App\Service;

class EncryptionService
{
    public function encryptFile(string $sourcePath, string $targetPath): void
    {
        $key = $_ENV['ENCRYPTION_KEY'] ?? 'default-local-key';
        $method = $_ENV['ENCRYPTION_METHOD'] ?? 'AES-256-CBC';

        if (!file_exists($sourcePath)) {
            throw new \RuntimeException('Source file not found: ' . $sourcePath);
        }

        $plainContent = file_get_contents($sourcePath);

        if ($plainContent === false) {
            throw new \RuntimeException('Unable to read source file: ' . $sourcePath);
        }

        $ivLength = openssl_cipher_iv_length($method);

        if ($ivLength === false) {
            throw new \RuntimeException('Invalid encryption method: ' . $method);
        }

        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedContent = openssl_encrypt(
            $plainContent,
            $method,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encryptedContent === false) {
            throw new \RuntimeException('Unable to encrypt file: ' . $sourcePath);
        }

        $payload = base64_encode($iv . $encryptedContent);

        $targetDirectory = dirname($targetPath);

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        file_put_contents($targetPath, $payload);
    }
}