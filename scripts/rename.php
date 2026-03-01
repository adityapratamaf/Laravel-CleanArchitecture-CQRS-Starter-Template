<?php

// scripts/rename.php
// Usage: composer run-script rename -- namaProject

$projectName = $argv[1] ?? null;

if (!$projectName) {
    echo "No project name provided.\n";
    exit(0);
}

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath) && file_exists(__DIR__ . '/../.env.example')) {
    copy(__DIR__ . '/../.env.example', $envPath);
}

if (file_exists($envPath)) {
    $env = file_get_contents($envPath);
    $env = preg_replace('/^APP_NAME=.*/m', 'APP_NAME="'.$projectName.'"', $env);
    file_put_contents($envPath, $env);
    echo "APP_NAME set to {$projectName}\n";
}