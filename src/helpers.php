<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function flash(string $type, string $text): void
{
    $_SESSION['flash'][] = ['type' => $type, 'text' => $text];
}

function take_flash(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}