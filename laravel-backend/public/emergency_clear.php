<?php
/**
 * Emergency Cache Clear Script for Public Folder
 * Upload to public/ folder and run directly
 */

// Security
$password = 'emergency2025';
if (!isset($_GET['pass']) || $_GET['pass'] !== $password) {
    die('Access denied. Use: ?pass=emergency2025');
}

header('Content-Type: text/plain');
echo "=== EMERGENCY CACHE CLEAR (from public folder) ===\n\n";

// Get Laravel root directory (one level up from public)
$rootPath = dirname(__DIR__);
echo "Laravel root directory: $rootPath\n";
echo "Current directory (public): " . __DIR__ . "\n\n";

// Function to delete directory contents
function clearDirectory($path) {
    if (!is_dir($path)) {
        echo "Directory not found: $path\n";
        return false;
    }
    
    $files = scandir($path);
    $deleted = 0;
    
    foreach ($files as $file) {
        if ($file == '.' || $file == '..' || $file == '.gitignore') {
            continue;
        }
        
        $fullPath = $path . '/' . $file;
        
        if (is_file($fullPath)) {
            if (unlink($fullPath)) {
                echo "Deleted: $file\n";
                $deleted++;
            } else {
                echo "Failed to delete: $file\n";
            }
        }
    }
    
    echo "Cleared $deleted files from " . basename($path) . "\n\n";
    return true;
}

// Function to create directory if not exists
function ensureDirectory($path) {
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "Created directory: $path\n";
        } else {
            echo "Failed to create: $path\n";
        }
    }
    
    chmod($path, 0755);
}

// Clear cache directories (relative to Laravel root)
$cacheDirs = [
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/logs',
    'bootstrap/cache'
];

echo "=== CLEARING CACHE DIRECTORIES ===\n";
foreach ($cacheDirs as $dir) {
    $fullPath = $rootPath . '/' . $dir;
    echo "Clearing: $fullPath\n";
    clearDirectory($fullPath);
}

// Delete specific cache files
echo "=== DELETING SPECIFIC CACHE FILES ===\n";
$cacheFiles = [
    'bootstrap/cache/packages.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes-v7.php'
];

foreach ($cacheFiles as $file) {
    $fullPath = $rootPath . '/' . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "Deleted: $file\n";
        } else {
            echo "Failed to delete: $file\n";
        }
    }
}

// Ensure required directories exist
echo "\n=== CREATING REQUIRED DIRECTORIES ===\n";
$requiredDirs = [
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($requiredDirs as $dir) {
    $fullPath = $rootPath . '/' . $dir;
    ensureDirectory($fullPath);
}

// Set permissions
echo "\n=== SETTING PERMISSIONS ===\n";
$dirsToChmod = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($dirsToChmod as $dir) {
    $fullPath = $rootPath . '/' . $dir;
    if (is_dir($fullPath)) {
        if (chmod($fullPath, 0755)) {
            echo "Set 755 for: $dir\n";
        } else {
            echo "Failed chmod: $dir\n";
        }
    }
}

// Try to run artisan commands
echo "\n=== TRYING ARTISAN COMMANDS ===\n";
$artisanCommands = [
    'php artisan optimize:clear',
    'php artisan view:clear',
    'php artisan config:clear',
    'php artisan cache:clear'
];

foreach ($artisanCommands as $command) {
    echo "Attempting: $command\n";
    
    $output = [];
    $returnVar = 0;
    
    // Execute from Laravel root
    $fullCommand = "cd $rootPath && $command 2>&1";
    exec($fullCommand, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "  ✓ Success\n";
    } else {
        echo "  ✗ Failed (this is normal if Laravel is broken)\n";
    }
}

echo "\n=== CHECKING STORAGE SYMLINK ===\n";
$symlinkPath = __DIR__ . '/storage';
$targetPath = $rootPath . '/storage/app/public';

if (is_link($symlinkPath)) {
    echo "Storage symlink exists\n";
} else {
    echo "Creating storage symlink...\n";
    if (symlink($targetPath, $symlinkPath)) {
        echo "Storage symlink created successfully\n";
    } else {
        echo "Failed to create storage symlink\n";
    }
}

echo "\n=== COMPLETED ===\n";
echo "Emergency cache clear completed!\n";
echo "Try accessing your application now.\n\n";
echo "If you still have issues:\n";
echo "1. Check if .env file exists in: $rootPath/.env\n";
echo "2. Make sure database file exists\n";
echo "3. Check file permissions\n\n";
echo "DELETE THIS FILE AFTER USE!\n";

// Self-delete option
if (isset($_GET['delete'])) {
    if (unlink(__FILE__)) {
        echo "\nFile deleted successfully!";
    } else {
        echo "\nFailed to delete file!";
    }
}

echo "\n\nTo delete this file: " . $_SERVER['REQUEST_URI'] . "&delete=1\n";
?>