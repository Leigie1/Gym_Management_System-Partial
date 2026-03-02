<?php
/**
 * PHP Configuration Checker
 * Checks if PHP is configured correctly for QR generation
 */

echo "<!DOCTYPE html><html><head><title>PHP Config Check</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;}";
echo ".success{color:#c8f53a;}.error{color:#ff4444;}.warning{color:#ffa500;}.info{color:#888;}";
echo "table{border-collapse:collapse;margin:20px 0;}td,th{border:1px solid #444;padding:10px;text-align:left;}</style></head><body>";

echo "<h1>PHP Configuration Check</h1>";
echo "<p>Checking if your PHP setup supports QR code generation...</p><hr>";

// Check allow_url_fopen
echo "<h2>1. URL File Access (allow_url_fopen)</h2>";
$allow_url_fopen = ini_get('allow_url_fopen');
if ($allow_url_fopen) {
    echo "<p class='success'>✓ allow_url_fopen is ENABLED</p>";
    echo "<p class='info'>PHP can fetch content from external URLs</p>";
} else {
    echo "<p class='error'>✗ allow_url_fopen is DISABLED</p>";
    echo "<p class='warning'>⚠️ This is the problem! QR generation requires this setting.</p>";
    echo "<p><strong>How to fix:</strong></p>";
    echo "<ol>";
    echo "<li>Open your php.ini file (usually in C:\\xampp\\php\\php.ini)</li>";
    echo "<li>Find the line: <code>;allow_url_fopen = Off</code></li>";
    echo "<li>Change it to: <code>allow_url_fopen = On</code> (remove the semicolon)</li>";
    echo "<li>Restart Apache in XAMPP Control Panel</li>";
    echo "</ol>";
}

echo "<hr>";

// Check file_get_contents
echo "<h2>2. file_get_contents() Function</h2>";
if (function_exists('file_get_contents')) {
    echo "<p class='success'>✓ file_get_contents() is available</p>";
} else {
    echo "<p class='error'>✗ file_get_contents() is not available</p>";
}

echo "<hr>";

// Check file_put_contents
echo "<h2>3. file_put_contents() Function</h2>";
if (function_exists('file_put_contents')) {
    echo "<p class='success'>✓ file_put_contents() is available</p>";
} else {
    echo "<p class='error'>✗ file_put_contents() is not available</p>";
}

echo "<hr>";

// Check folder permissions
echo "<h2>4. QR Codes Folder</h2>";
if (is_dir('assets/qrcodes')) {
    echo "<p class='success'>✓ Folder exists: assets/qrcodes/</p>";
    if (is_writable('assets/qrcodes')) {
        echo "<p class='success'>✓ Folder is writable</p>";
    } else {
        echo "<p class='error'>✗ Folder is not writable</p>";
        echo "<p>Fix: Right-click folder → Properties → Security → Edit → Allow 'Full control'</p>";
    }
} else {
    echo "<p class='error'>✗ Folder does not exist: assets/qrcodes/</p>";
    echo "<p>Creating folder...</p>";
    if (mkdir('assets/qrcodes', 0777, true)) {
        echo "<p class='success'>✓ Folder created successfully</p>";
    } else {
        echo "<p class='error'>✗ Failed to create folder</p>";
    }
}

echo "<hr>";

// Test internet connection
echo "<h2>5. Internet Connection</h2>";
$test_url = "https://www.google.com";
$context = stream_context_create(['http' => ['timeout' => 5]]);
$connected = @file_get_contents($test_url, false, $context);

if ($connected !== false) {
    echo "<p class='success'>✓ Internet connection OK</p>";
    echo "<p class='info'>Successfully connected to: {$test_url}</p>";
} else {
    echo "<p class='error'>✗ Cannot connect to internet</p>";
    echo "<p class='warning'>Check your internet connection or firewall settings</p>";
}

echo "<hr>";

// Test QR API
echo "<h2>6. QR Code API Test</h2>";
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TEST";
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);
$qr_test = @file_get_contents($qr_api, false, $context);

if ($qr_test !== false && strlen($qr_test) > 100) {
    echo "<p class='success'>✓ QR API is accessible</p>";
    echo "<p class='info'>Successfully fetched QR code from API</p>";
    echo "<div style='background:#2a2a2a;padding:20px;border-radius:8px;display:inline-block;'>";
    echo "<p>Test QR Code:</p>";
    echo "<img src='data:image/png;base64," . base64_encode($qr_test) . "' alt='Test QR'>";
    echo "</div>";
} else {
    echo "<p class='error'>✗ Cannot access QR API</p>";
    echo "<p class='warning'>API might be blocked by firewall or temporarily down</p>";
}

echo "<hr>";

// Summary
echo "<h2>Summary</h2>";
$issues = [];
if (!$allow_url_fopen) $issues[] = "allow_url_fopen is disabled";
if (!is_writable('assets/qrcodes')) $issues[] = "QR folder not writable";
if ($connected === false) $issues[] = "No internet connection";
if ($qr_test === false) $issues[] = "QR API not accessible";

if (empty($issues)) {
    echo "<p class='success' style='font-size:18px;'>✓ All checks passed! QR generation should work.</p>";
    echo "<p><a href='test-qr.php' style='color:#c8f53a;'>→ Test QR Generation</a></p>";
} else {
    echo "<p class='error' style='font-size:18px;'>✗ Found " . count($issues) . " issue(s):</p>";
    echo "<ul class='error'>";
    foreach ($issues as $issue) {
        echo "<li>{$issue}</li>";
    }
    echo "</ul>";
    echo "<p class='warning'>Fix these issues and refresh this page</p>";
}

echo "<hr>";
echo "<h2>PHP Information</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>allow_url_fopen</td><td>" . ($allow_url_fopen ? 'On' : 'Off') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . " seconds</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "</table>";

echo "<hr>";
echo "<p><a href='dashboard.php' style='color:#c8f53a;'>← Back to Dashboard</a></p>";
echo "</body></html>";
?>
