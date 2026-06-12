<?php

namespace App\Tests\Service;

use App\Service\EncryptionService;
use PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function testEncryptFileCreatesEncryptedOutputFile(): void
    {
        $_ENV['ENCRYPTION_KEY'] = 'test-key';
        $_ENV['ENCRYPTION_METHOD'] = 'AES-256-CBC';

        $service = new EncryptionService();

        $sourcePath = sys_get_temp_dir() . '/source_test_file.txt';
        $targetPath = sys_get_temp_dir() . '/source_test_file.txt.enc';

        file_put_contents($sourcePath, 'Sensitive test content');

        if (file_exists($targetPath)) {
            unlink($targetPath);
        }

        $service->encryptFile($sourcePath, $targetPath);

        $this->assertFileExists($targetPath);
        $this->assertNotEmpty(file_get_contents($targetPath));
        $this->assertNotEquals(
            file_get_contents($sourcePath),
            file_get_contents($targetPath)
        );

        unlink($sourcePath);
        unlink($targetPath);
    }
}