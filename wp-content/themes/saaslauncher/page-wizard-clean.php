<?php
/**
 * Template Name: Wizard (Clean - No WP Scripts)
 * Description: Clean wizard page without WordPress scripts
 */

// Disable all WordPress scripts and styles
remove_action('wp_head', 'wp_enqueue_scripts', 1);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_head', 'wp_print_styles', 8);
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_footer', 'wp_print_footer_scripts', 20);

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo get_permalink(); ?>">
    <meta property="og:title" content="Try Our 2-Minute AI Workflow Generator - MGRNZ">
    <meta property="og:description" content="Get a personalized automation blueprint in minutes. Our AI analyzes your workflow and creates a custom implementation plan using the DRIVE framework.">
    <meta property="og:image" content="https://mgrnz.com/wp/wp-content/uploads/2025/12/Picture1.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="MGRNZ - AI Workflow Automation">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo get_permalink(); ?>">
    <meta name="twitter:title" content="Try Our 2-Minute AI Workflow Generator - MGRNZ">
    <meta name="twitter:description" content="Get a personalized automation blueprint in minutes. Our AI analyzes your workflow and creates a custom implementation plan.">
    <meta name="twitter:image" content="https://mgrnz.com/wp/wp-content/uploads/2025/12/Picture1.png">
    
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/templates/wizard/wizard-styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include get_template_directory() . '/templates/wizard/wizard-main.php'; ?>
</body>
</html>
