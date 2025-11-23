<?php
/**
 * Create database tables for AI Workflow plugin
 * Run this once, then delete the file
 */

// Load WordPress
require_once(__DIR__ . '/../wp-load.php');

// Load the required classes
require_once(__DIR__ . '/mu-plugins/includes/class-error-logger.php');
require_once(__DIR__ . '/mu-plugins/includes/class-conversation-manager.php');
require_once(__DIR__ . '/mu-plugins/includes/class-conversation-analytics.php');

// Create the tables
MGRNZ_Error_Logger::create_table();
MGRNZ_Conversation_Manager::create_tables();
MGRNZ_Conversation_Analytics::create_table();

echo "Database tables created successfully!\n";
echo "Tables created:\n";
echo "- " . $GLOBALS['wpdb']->prefix . "mgrnz_ai_workflow_logs\n";
echo "- " . $GLOBALS['wpdb']->prefix . "mgrnz_conversation_sessions\n";
echo "- " . $GLOBALS['wpdb']->prefix . "mgrnz_chat_messages\n";
echo "- " . $GLOBALS['wpdb']->prefix . "mgrnz_conversation_analytics\n";
