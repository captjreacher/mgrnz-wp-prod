<?php
/**
 * Clear OPcache
 * Visit: https://mgrnz.com/clear-opcache.php
 */

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared successfully!";
} else {
    echo "⚠️ OPcache is not enabled or not available";
}

echo "<br><br>";
echo "Now try completing the wizard again.";
