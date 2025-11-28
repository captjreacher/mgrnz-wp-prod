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
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/templates/wizard/wizard-styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include get_template_directory() . '/templates/wizard/wizard-main.php'; ?>
</body>
</html>
