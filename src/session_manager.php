<?php

if (session_status() === PHP_SESSION_NONE) {

    $sessionName = 'SESS_' . md5(__DIR__);

    $currentHost = preg_replace('/^www\./', '', strtolower($_SERVER['HTTP_HOST'] ?? 'localhost'));
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $currentHost,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    
    session_name($sessionName);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
