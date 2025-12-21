<?php
/**
 * DEBUG SESSION SCRIPT
 * Upload ke public/ dan akses via browser
 * https://tandur.online/debug-session.php
 *
 * Test apakah PHP session work tanpa Laravel
 */

session_start();

// Set test data
if (!isset($_SESSION['test_count'])) {
    $_SESSION['test_count'] = 0;
}
$_SESSION['test_count']++;
$_SESSION['last_access'] = date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h2 { color: #4ec9b0; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
    </style>
</head>
<body>
    <h1>🔍 PHP Session Debug</h1>

    <h2>Session Status</h2>
    <pre><?php
    echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '<span class="success">ACTIVE ✓</span>' : '<span class="error">INACTIVE ✗</span>') . "\n";
    echo "Session ID: " . session_id() . "\n";
    echo "Session Name: " . session_name() . "\n";
    ?></pre>

    <h2>Session Data</h2>
    <pre><?php print_r($_SESSION); ?></pre>

    <h2>Session Configuration</h2>
    <pre><?php
    echo "Save Path: " . session_save_path() . "\n";
    echo "Cookie Path: " . ini_get('session.cookie_path') . "\n";
    echo "Cookie Domain: " . ini_get('session.cookie_domain') . "\n";
    echo "Cookie Secure: " . (ini_get('session.cookie_secure') ? 'Yes' : 'No') . "\n";
    echo "Cookie HTTPOnly: " . (ini_get('session.cookie_httponly') ? 'Yes' : 'No') . "\n";
    echo "Cookie SameSite: " . ini_get('session.cookie_samesite') . "\n";
    echo "GC Maxlifetime: " . ini_get('session.gc_maxlifetime') . " seconds\n";
    ?></pre>

    <h2>Cookies</h2>
    <pre><?php print_r($_COOKIE); ?></pre>

    <h2>Test Result</h2>
    <pre><?php
    if ($_SESSION['test_count'] > 1) {
        echo '<span class="success">✓ Session WORKING! Count: ' . $_SESSION['test_count'] . '</span>';
        echo "\nSession persists across page refreshes.";
    } else {
        echo '<span class="error">⚠ First visit or session reset</span>';
        echo "\nRefresh page to test if session persists.";
    }
    ?></pre>

    <p><a href="?" style="color: #4ec9b0;">🔄 Refresh Page</a> | <a href="?clear=1" style="color: #f48771;">🗑️ Clear Session</a></p>

    <?php
    if (isset($_GET['clear'])) {
        session_destroy();
        echo '<script>alert("Session cleared! Refresh page."); window.location.href = "?";</script>';
    }
    ?>
</body>
</html>
