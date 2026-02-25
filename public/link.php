<?php
$target = dirname(__DIR__) . '/storage/app/public';
$link   = __DIR__ . '/storage';

if (is_link($link)) {
    echo 'Symlink already exists: ' . $link . ' → ' . readlink($link);
} elseif (file_exists($link)) {
    echo 'ERROR: ' . $link . ' already exists and is not a symlink. Remove it first.';
} else {
    if (symlink($target, $link)) {
        echo 'Symlink created successfully: ' . $link . ' → ' . $target;
    } else {
        echo 'ERROR: Failed to create symlink. Your host may not allow symlinks. Try the copy fallback below.';
    }
}
