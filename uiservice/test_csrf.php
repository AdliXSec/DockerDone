<?php
// Test CSRF token generation and session
require '/var/www/vendor/autoload.php';
$app = require_once '/var/www/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// GET /register
$request = \Illuminate\Http\Request::create('/register', 'GET');
$response = $kernel->handle($request);
echo 'GET Status: ' . $response->getStatusCode() . PHP_EOL;

// Check for CSRF token
preg_match('/name="_token"\s+value="([^"]+)"/', $response->getContent(), $matches);
$token = $matches[1] ?? 'NOT_FOUND';
echo 'CSRF Token found: ' . ($token !== 'NOT_FOUND' ? 'YES' : 'NO') . PHP_EOL;

// Check cookies
$cookies = $response->headers->getCookies();
echo 'Cookies count: ' . count($cookies) . PHP_EOL;
foreach ($cookies as $c) {
    echo '  ' . $c->getName() . PHP_EOL;
}

// Check for XSRF cookie
$xsrfCookie = null;
foreach ($cookies as $c) {
    if ($c->getName() === 'XSRF-TOKEN') {
        $xsrfCookie = $c->getValue();
    }
}
echo 'XSRF-TOKEN cookie: ' . ($xsrfCookie ? 'YES' : 'NO') . PHP_EOL;

// Check session driver
echo 'Session driver: ' . config('session.driver') . PHP_EOL;
echo 'Session path: ' . config('session.files') . PHP_EOL;

// Check if session directory is writable
$sessionPath = config('session.files');
echo 'Session dir exists: ' . (is_dir($sessionPath) ? 'YES' : 'NO') . PHP_EOL;
echo 'Session dir writable: ' . (is_writable($sessionPath) ? 'YES' : 'NO') . PHP_EOL;

// List session files
$files = glob($sessionPath . '/*');
echo 'Session files: ' . count($files) . PHP_EOL;

$kernel->terminate($request, $response);
