<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Shield\Filters\SessionAuth;
use App\Filters\AdminFilter;
use App\Filters\LicenseCheck;

class Filters extends BaseFilters
{
    /**
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'forcehttps'    => ForceHTTPS::class,
        'session'       => SessionAuth::class,
        'auth'          => SessionAuth::class,
        'admin'         => AdminFilter::class,
        'license'       => \App\Filters\LicenseCheck::class,
    ];

    /**
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
public array $globals = [
    'before' => [
        'honeypot',
        'csrf' => ['except' => ['install/testdb']],
        'license' => [
    'except' => [
        '/install',
        '/install/process',
        '/install/testdb'
    ]],
        'session' => ['except' => [
            '/', '/login', '/logout', '/register', '/forgot', '/activate', '/activate/process', '/invalid', '/install', '/install/process', '/install/testdb'
        ]],
    ],
    'after' => [
        'toolbar',
        'honeypot',
        'secureheaders',
    ],
];

    /**
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];

    /**
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [],
        'after' => [],
    ];
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Logika untuk mengaktifkan filter tertentu hanya di lingkungan 'production'
        if (ENVIRONMENT === 'production') {
            // Aktifkan 'forcehttps' hanya saat live di server dengan SSL
            $this->globals['before'][] = 'forcehttps';
        }
    }
}
