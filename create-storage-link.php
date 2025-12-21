<?php

$targetFolder = __DIR__.'/storage/app/public';
$linkFolder = __DIR__.'/public/storage';

// Hapus link lama jika ada
if (file_exists($linkFolder)) {
    if (is_link($linkFolder)) {
        unlink($linkFolder);
    } else {
        rmdir($linkFolder);
    }
}

// Buat symbolic link
if (symlink($targetFolder, $linkFolder)) {
    echo "✅ Storage link created successfully!<br>";
    echo "Target: {$targetFolder}<br>";
    echo "Link: {$linkFolder}<br>";
} else {
    echo "❌ Failed to create storage link<br>";
    echo "Please check folder permissions<br>";
}
