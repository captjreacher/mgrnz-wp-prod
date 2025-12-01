<?php
/**
 * MU-Plugins Loader
 * 
 * Ensures all critical mu-plugins files are loaded
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load MailerLite webhook handler
require_once __DIR__ . '/mailerlite-webhook-handler.php';

// Load other critical files
require_once __DIR__ . '/mgrnz-ai-workflow-endpoint.php';
