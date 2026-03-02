<?php
/**
 * Batch QR Code Generator for Existing Members
 * Run this script once to generate QR codes for all existing members
 */

require_once 'includes/config.php';
require_once 'includes/qr-generator.php';

// Get all members without QR codes
$query = "SELECT id, member_id_code, first_name, last_name FROM members ORDER BY id ASC";
$result = mysqli_query($conn, $query);

$generated = 0;
$skipped = 0;
$failed = 0;

echo "<!DOCTYPE html><html><head><title>QR Batch Generator</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#1a1a1a;color:#fff;}";
echo ".success{color:#c8f53a;}.error{color:#ff4444;}.info{color:#888;}.warning{color:#ffa500;}</style></head><body>";
echo "<h1>QR Code Batch Generator</h1>";
echo "<p>Generating QR codes for existing members...</p>";
echo "<p class='warning'>⚠️ This requires internet connection to generate QR codes</p><hr>";

// Check internet connectivity
$connected = @file_get_contents("https://www.google.com", false, stream_context_create(['http' => ['timeout' => 5]]));
if ($connected === false) {
    echo "<p class='error'>✗ No internet connection detected. QR generation requires internet access.</p>";
    echo "<p class='info'>Please check your internet connection and try again.</p>";
    echo "<hr><p><a href='dashboard.php' style='color:#c8f53a;'>← Back to Dashboard</a></p></body></html>";
    exit;
}
echo "<p class='success'>✓ Internet connection OK</p><hr>";

while ($member = mysqli_fetch_assoc($result)) {
    $member_id = $member['member_id_code'];
    $qr_path = get_qr_path($member_id);
    
    echo "<p>";
    
    // Check if QR already exists
    if (qr_exists($member_id)) {
        echo "<span class='info'>⊘ Skipped: {$member['first_name']} {$member['last_name']} ({$member_id}) - QR already exists</span>";
        $skipped++;
    } else {
        // Generate QR code
        $success = generate_member_qr($member_id, $qr_path);
        
        if ($success) {
            echo "<span class='success'>✓ Generated: {$member['first_name']} {$member['last_name']} ({$member_id})</span>";
            $generated++;
        } else {
            echo "<span class='error'>✗ Failed: {$member['first_name']} {$member['last_name']} ({$member_id}) - API error or network issue</span>";
            $failed++;
        }
    }
    
    echo "</p>";
    flush();
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p class='success'>Generated: {$generated}</p>";
echo "<p class='info'>Skipped: {$skipped}</p>";
echo "<p class='error'>Failed: {$failed}</p>";

if ($failed > 0) {
    echo "<hr>";
    echo "<h3>Troubleshooting Failed Generations:</h3>";
    echo "<ul style='color:#ffa500;'>";
    echo "<li>Check your internet connection</li>";
    echo "<li>Verify the assets/qrcodes/ folder is writable</li>";
    echo "<li>Try refreshing the page to retry</li>";
    echo "<li>Check if firewall is blocking API requests</li>";
    echo "<li>Try running test-qr.php to diagnose the issue</li>";
    echo "</ul>";
}

echo "<p><a href='dashboard.php' style='color:#c8f53a;'>← Back to Dashboard</a></p>";
echo "<p><a href='test-qr.php' style='color:#c8f53a;'>→ Test QR Generation</a></p>";
echo "</body></html>";

mysqli_close($conn);
?>
