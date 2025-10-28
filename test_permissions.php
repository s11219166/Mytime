<?php
$dir = __DIR__ . '/bootstrap/cache';
echo "Directory exists: " . (is_dir($dir) ? 'Yes' : 'No') . "\n";
echo "Is writable: " . (is_writable($dir) ? 'Yes' : 'No') . "\n";
echo "Current user: " . get_current_user() . "\n";
echo "Full path: " . realpath($dir) . "\n";

// Try to create a test file
$testFile = $dir . '/test_file.txt';
$result = file_put_contents($testFile, 'test');
echo "Could write file: " . ($result !== false ? 'Yes' : 'No') . "\n";

// Try to read directory contents
echo "Directory contents:\n";
$files = scandir($dir);
print_r($files);
