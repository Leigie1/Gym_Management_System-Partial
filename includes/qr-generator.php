<?php
// ============================================================
// QR Code Generator
// ============================================================

/**
 * Generate QR code for member using multiple API fallbacks
 * @param string $member_id - Member ID code
 * @param string $save_path - Path to save QR code image
 * @return bool - Success status
 */
function generate_member_qr($member_id, $save_path) {
    // Try multiple QR code APIs in order of preference
    $apis = [
        // API 1: QR Server (free, no limits)
        "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($member_id),
        
        // API 2: GoQR.me (free, reliable)
        "https://api.qr-code-generator.com/v1/create?access-token=free&qr_code_text=" . urlencode($member_id) . "&image_format=PNG&image_width=200",
        
        // API 3: QuickChart (free, reliable)
        "https://quickchart.io/qr?text=" . urlencode($member_id) . "&size=200",
    ];
    
    foreach ($apis as $qr_url) {
        // Try to get QR code image
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $qr_image = @file_get_contents($qr_url, false, $context);
        
        if ($qr_image !== false && strlen($qr_image) > 100) {
            // Save to file
            $result = file_put_contents($save_path, $qr_image);
            if ($result !== false) {
                return true;
            }
        }
    }
    
    // All APIs failed, return false
    return false;
}

/**
 * Get QR code path for member
 * @param string $member_id - Member ID code
 * @return string - Path to QR code image
 */
function get_qr_path($member_id) {
    return "assets/qrcodes/" . $member_id . ".png";
}

/**
 * Check if QR code exists for member
 * @param string $member_id - Member ID code
 * @return bool - True if exists
 */
function qr_exists($member_id) {
    return file_exists(get_qr_path($member_id));
}
?>
