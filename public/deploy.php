<?php
// Script sekali-pakai untuk menjalankan perintah artisan lewat browser
// di shared hosting tanpa SSH/Terminal.
//
// CARA PAKAI:
// 1. Ganti $secret di bawah dengan string acak yang hanya kamu tahu.
// 2. Upload file ini ke public_html/deploy.php
// 3. Akses: https://domainkamu.com/deploy.php?key=SECRET_KAMU&cmd=storage:link
//    Command yang diizinkan: storage:link, migrate, migrate:status,
//    config:cache, config:clear, route:cache, route:clear, view:cache, view:clear
// 4. SETELAH SELESAI DIPAKAI, HAPUS FILE INI DARI SERVER. Jangan dibiarkan.

$secret = 'f75f132dfd4efb0e0030b505f56dd2ba4d4eb3b0225314ab7636ab70e6435df2';

$key = $_GET['key'] ?? '';
if (!hash_equals($secret, $key)) {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__.'/../../toya-amerta/vendor/autoload.php';
$app = require_once __DIR__.'/../../toya-amerta/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$allowed = [
    'storage:link',
    'migrate',
    'migrate:status',
    'migrate:fresh',
    'db:seed',
    'config:cache',
    'config:clear',
    'route:cache',
    'route:clear',
    'view:cache',
    'view:clear',
    'optimize',
    'optimize:clear',
    'key:generate',
];

$cmd = $_GET['cmd'] ?? '';

if ($cmd === 'fix-storage') {
    $base = __DIR__.'/../../toya-amerta';
    $dirs = [
        '/storage/app/public',
        '/storage/fonts',
        '/storage/framework/cache/data',
        '/storage/framework/sessions',
        '/storage/framework/testing',
        '/storage/framework/views',
        '/storage/logs',
        '/bootstrap/cache',
    ];
    echo '<pre>';
    foreach ($dirs as $dir) {
        $path = $base.$dir;
        if (!is_dir($path)) {
            mkdir($path, 0775, true);
            echo "Created: {$dir}\n";
        } else {
            echo "Exists:  {$dir}\n";
        }
        chmod($path, 0775);
    }
    echo '</pre>';
    exit;
}

if ($cmd === 'log') {
    $logFile = __DIR__.'/../../toya-amerta/storage/logs/laravel.log';
    if (!file_exists($logFile)) {
        exit('Log file belum ada.');
    }
    $lines = file($logFile);
    $tail = array_slice($lines, -200);
    echo '<pre>'.htmlspecialchars(implode('', $tail)).'</pre>';
    exit;
}

if (!in_array($cmd, $allowed, true)) {
    exit('Command tidak diizinkan. Pilihan: '.implode(', ', $allowed).', log');
}

$args = [];
if ($cmd === 'migrate' || $cmd === 'migrate:fresh' || $cmd === 'db:seed') {
    $args['--force'] = true;
}
if ($cmd === 'migrate:fresh' && isset($_GET['seed'])) {
    $args['--seed'] = true;
}

$exitCode = Illuminate\Support\Facades\Artisan::call($cmd, $args);

echo '<pre>';
echo "Command: {$cmd}\n";
echo "Exit code: {$exitCode}\n\n";
echo htmlspecialchars(Illuminate\Support\Facades\Artisan::output());
echo '</pre>';
