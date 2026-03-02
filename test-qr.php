<?php
/**
 * QR Code Test Page
 * Quick test to verify QR generation is working
 */

require_once 'includes/qr-generator.php';

echo "<!DOCTYPE html><html><head><title>QR Test</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;}";
echo ".success{color:#c8f53a;}.error{color:#ff4444;}.qr-box{background:#2a2a2a;padding:20px;border-radius:8px;margin:20px 0;display:inline-block;}.warning{color:#ffa500;}</style></head><body>";
echo "<h1>QR Code Generation Test</h1>";

// Test internet connection first
echo "<h2>1. Testing Internet Connection...</h2>";
$connected = @file_get_contents("https://www.google.com", false, stream_context_create(['http' => ['timeout' => 5]]));
if ($connected !== false) {
    echo "<p class='success'>✓ Internet connection OK</p>";
} else {
    echo "<p class='error'>✗ No internet connection</p>";
    echo "<p class='warning'>QR generation requires internet access to external APIs</p>";
}

echo "<hr>";

// Test member ID
$test_member_id = "TEST-00001";
$test_qr_path = "assets/qrcodes/" . $test_member_id . ".png";

echo "<h2>2. Testing QR Generation...</h2>";
echo "<p>Member ID: <strong>{$test_member_id}</strong></p>";

// Generate test QR
$result = generate_member_qr($test_member_id, $test_qr_path);

if ($result) {
    echo "<p class='success'>✓ QR Code generated successfully!</p>";
    echo "<div class='qr-box'>";
    echo "<p>Generated QR Code:</p>";
    echo "<img src='{$test_qr_path}' alt='Test QR' style='border:2px solid #c8f53a;border-radius:4px;'>";
    echo "<p style='font-size:12px;color:#888;margin-top:10px;'>File: {$test_qr_path}</p>";
    echo "</div>";
    
    // Test if file exists
    if (file_exists($test_qr_path)) {
        $filesize = filesize($test_qr_path);
        echo "<p class='success'>✓ File exists ({$filesize} bytes)</p>";
    }
    
    // Test qr_exists function
    if (qr_exists($test_member_id)) {
        echo "<p class='success'>✓ qr_exists() function working</p>";
    }
    
    // Test get_qr_path function
    $path_test = get_qr_path($test_member_id);
    echo "<p class='success'>✓ get_qr_path() returns: {$path_test}</p>";
    
} else {
    echo "<p class='error'>✗ QR Code generation failed!</p>";
    echo "<p class='warning'>Possible issues:</p>";
    echo "<ul>";
    echo "<li>No internet connection (required for QR API)</li>";
    echo "<li>Firewall blocking API requests</li>";
    echo "<li>Check folder permissions: assets/qrcodes/</li>";
    echo "<li>Verify PHP file_get_contents() is enabled</li>";
    echo "<li>All QR APIs might be down (try again later)</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h2>3. System Check</h2>";

// Check folder
if (is_dir('assets/qrcodes')) {
    echo "<p class='success'>✓ QR codes folder exists</p>";
    if (is_writable('assets/qrcodes')) {
        echo "<p class='success'>✓ Folder is writable</p>";
    } else {
        echo "<p class='error'>✗ Folder is not writable</p>";
    }
} else {
    echo "<p class='error'>✗ QR codes folder missing</p>";
}

// Check functions
if (function_exists('file_get_contents')) {
    echo "<p class='success'>✓ file_get_contents() available</p>";
} else {
    echo "<p class='error'>✗ file_get_contents() not available</p>";
}

if (function_exists('file_put_contents')) {
    echo "<p class='success'>✓ file_put_contents() available</p>";
} else {
    echo "<p class='error'>✗ file_put_contents() not available</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php' style='color:#c8f53a;'>← Back to Dashboard</a></p>";
echo "<p><a href='generate-qr-batch.php' style='color:#c8f53a;'>→ Generate QR for All Members</a></p>";
echo "</body></html>";
?>
