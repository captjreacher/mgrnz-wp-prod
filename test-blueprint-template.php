<?php
/**
 * Test Blueprint Template
 * Visit: http://mgrnz.local/test-blueprint-template.php
 */

require_once __DIR__ . '/wp-load.php';

echo "<h1>Blueprint Template Test</h1>";
echo "<hr>";

// Load the template
$template_path = __DIR__ . '/wp-content/mu-plugins/includes/blueprint-template.php';

if (file_exists($template_path)) {
    echo "<p style='color: green;'>✓ Template file exists</p>";
    
    $template = include $template_path;
    
    echo "<h2>Blueprint Structure:</h2>";
    echo "<ol>";
    foreach ($template['blueprint_structure'] as $section) {
        echo "<li><strong>{$section['number']}. {$section['title']}</strong>";
        echo "<ul>";
        foreach ($section['guidelines'] as $guideline) {
            echo "<li>" . esc_html(substr($guideline, 0, 100)) . (strlen($guideline) > 100 ? '...' : '') . "</li>";
        }
        echo "</ul>";
        echo "</li>";
    }
    echo "</ol>";
    
    // Check if the new section exists
    $found = false;
    foreach ($template['blueprint_structure'] as $section) {
        if (strpos($section['title'], '2-MINUTE AUTOMATION') !== false) {
            $found = true;
            echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✓ NEW SECTION FOUND: {$section['title']}</p>";
            break;
        }
    }
    
    if (!$found) {
        echo "<p style='color: red; font-size: 18px; font-weight: bold;'>✗ New section NOT found in template</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Template file not found at: $template_path</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<p>If the new section is found above, it means the template is updated correctly.</p>";
echo "<p>To see it in a blueprint:</p>";
echo "<ol>";
echo "<li>Clear your browser cache and localStorage (Ctrl+Shift+Delete)</li>";
echo "<li>Start a NEW wizard session (don't reuse an old one)</li>";
echo "<li>Complete the wizard and generate a blueprint</li>";
echo "<li>The new 'About Your 2-minute Automation' section should appear after the Executive Summary</li>";
echo "</ol>";
