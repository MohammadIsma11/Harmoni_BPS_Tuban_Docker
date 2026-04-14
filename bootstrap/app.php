<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Mendaftarkan alias middleware agar bisa dipanggil di routes/web.php
        $middleware->alias([
            'cek.cuti' => \App\Http\Middleware\CheckStatusCuti::class,
        ]);

        // Trust all proxies (penting saat menggunakan Nginx/Docker/Cloudflare)
        $middleware->trustProxies(at: '*');

        // Opsional: Jika ingin mengarahkan user yang belum login ke halaman tertentu
        $middleware->redirectTo(
            guests: '/',
            users: function () {
                // Logika redirect cerdas jika user SUDAH login tapi coba akses /login lagi
                if (auth()->user()->role === 'Admin') {
                    return route('manajemen.anggota');
                }
                return route('dashboard');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();