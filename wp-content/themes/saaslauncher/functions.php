<?php
if (! defined('SAASLAUNCHER_VERSION')) {
	// Replace the version number of the theme on each release.
	define('SAASLAUNCHER_VERSION', wp_get_theme()->get('Version'));
}
define('SAASLAUNCHER_DEBUG', defined('WP_DEBUG') && WP_DEBUG === true);
define('SAASLAUNCHER_DIR', trailingslashit(get_template_directory()));
define('SAASLAUNCHER_URL', trailingslashit(get_template_directory_uri()));

if (! function_exists('saaslauncher_support')) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since walker_fse 1.0.0
	 *
	 * @return void
	 */
	function saaslauncher_support()
	{
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');
		// Add support for block styles.
		add_theme_support('wp-block-styles');
		add_theme_support('post-thumbnails');
		// Enqueue editor styles.
		add_editor_style('style.css');
		// Removing default patterns.
		remove_theme_support('core-block-patterns');

		load_theme_textdomain('saaslauncher', get_template_directory());
	}

endif;
add_action('after_setup_theme', 'saaslauncher_support');

// print_r( get_template_directory() );

/*
----------------------------------------------------------------------------------
Enqueue Styles
-----------------------------------------------------------------------------------*/
// Completely disable emoji scripts (they're causing syntax errors)
if (defined('DISABLE_WP_EMOJIS') && DISABLE_WP_EMOJIS) {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', function($plugins) {
		return array_diff($plugins, array('wpemoji'));
	});
	add_filter('wp_resource_hints', function($urls, $relation_type) {
		if ('dns-prefetch' === $relation_type) {
			$urls = array_diff($urls, array('//s.w.org'));
		}
		return $urls;
	}, 10, 2);
}

if (! function_exists('saaslauncher_styles')) :
	function saaslauncher_styles()
	{
		// Skip ALL scripts on wizard page to avoid conflicts
		if (is_page('start-using-ai') || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'start-using-ai') !== false)) {
			// Dequeue all scripts
			wp_dequeue_script('jquery');
			wp_dequeue_script('jquery-migrate');
			// Only load essential styles, no JavaScript
			wp_enqueue_style('saaslauncher-style', get_stylesheet_uri(), array(), SAASLAUNCHER_VERSION);
			return;
		}
		
		// registering style for theme
		wp_enqueue_style('saaslauncher-style', get_stylesheet_uri(), array(), SAASLAUNCHER_VERSION);
		wp_enqueue_style('saaslauncher-blocks-style', get_template_directory_uri() . '/assets/css/blocks.css');
		wp_enqueue_style('saaslauncher-aos-style', get_template_directory_uri() . '/assets/css/aos.css');
		wp_enqueue_style('saaslauncher-custom-style', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.1.0');
		if (is_rtl()) {
			wp_enqueue_style(
				'saaslauncher-rtl-css',
				get_template_directory_uri() . '/assets/css/rtl.css',
				array(),
				SAASLAUNCHER_VERSION
			);
		}
		wp_enqueue_script('saaslauncher-aos-scripts', get_template_directory_uri() . '/assets/js/aos.js', array('jquery'), SAASLAUNCHER_VERSION, true);
		wp_enqueue_script('saaslauncher-scripts', get_template_directory_uri() . '/assets/js/saaslauncher-scripts.js', array('jquery'), SAASLAUNCHER_VERSION, true);
	}
endif;
// === Match Gutenberg + Site Editor background to MGRNZ site colors ===
add_action('enqueue_block_editor_assets', function () {
    ?>
    <style>
      /* Editor canvas background (post editor + Site Editor) */
      .editor-styles-wrapper {
        background-color: #0f172a !important; /* MGRNZ dark navy */
        color: #ffffff !important;
      }

      .editor-styles-wrapper p,
      .editor-styles-wrapper h1,
      .editor-styles-wrapper h2,
      .editor-styles-wrapper h3,
      .editor-styles-wrapper h4,
      .editor-styles-wrapper h5,
      .editor-styles-wrapper h6 {
        color: #ffffff !important;
      }

      .editor-styles-wrapper a {
        color: #ff4f00 !important;
      }

      .editor-styles-wrapper a:hover {
        color: #ff6a26 !important;
      }

      /* Placeholder text visibility */
      .editor-styles-wrapper .block-editor-rich-text__editable[contenteditable]:empty:before {
        color: rgba(255, 255, 255, 0.4) !important;
      }
    </style>
    <?php
});

add_action('wp_enqueue_scripts', 'saaslauncher_styles');

/**
 * Enqueue scripts for admin area
 */
function saaslauncher_admin_style()
{
	if (function_exists('get_current_screen')) {
		$saaslauncher_notice_current_screen = get_current_screen();
	}
	if ((! empty($_GET['page']) && 'about-saaslauncher' === $_GET['page']) || $saaslauncher_notice_current_screen->id === 'themes' || $saaslauncher_notice_current_screen->id === 'dashboard' || $saaslauncher_notice_current_screen->id === 'plugins') {
		wp_enqueue_style('saaslauncher-admin-style', get_template_directory_uri() . '/inc/admin/css/admin-style.css', array(), SAASLAUNCHER_VERSION, 'all');
		wp_enqueue_script('saaslauncher-admin-scripts', get_template_directory_uri() . '/inc/admin/js/saaslauncher-admin-scripts.js', array('jquery'), SAASLAUNCHER_VERSION, true);
		wp_localize_script(
			'saaslauncher-admin-scripts',
			'saaslauncher_admin_localize',
			array(
				'ajax_url'     => admin_url('admin-ajax.php'),
				'nonce'        => wp_create_nonce('saaslauncher_admin_nonce'),
				'welcomeNonce' => wp_create_nonce('saaslauncher_welcome_nonce'),
				'redirect_url' => admin_url('themes.php?page=about-saaslauncher'),
				'scrollURL'    => admin_url('plugins.php?cozy-addons-scroll=true'),
				'demoURL'      => admin_url('themes.php?page=advanced-import'),
			)
		);
	}
}
add_action('admin_enqueue_scripts', 'saaslauncher_admin_style');

/**
 * Enqueue assets scripts for both backend and frontend
 */
function saaslauncher_block_assets()
{
	wp_enqueue_style('saaslauncher-blocks-style', get_template_directory_uri() . '/assets/css/blocks.css');
}
add_action('enqueue_block_assets', 'saaslauncher_block_assets');

/**
 * Load core file.
 */
require_once get_template_directory() . '/inc/core/init.php';

/**
 * Load welcome page file.
 */
require_once get_template_directory() . '/inc/admin/welcome-notice.php';

if (! function_exists('saaslauncher_excerpt_more_postfix')) {
	function saaslauncher_excerpt_more_postfix($more)
	{
		if (is_admin()) {
			return $more;
		}
		return '...';
	}
	add_filter('excerpt_more', 'saaslauncher_excerpt_more_postfix');
}
function saaslauncher_add_woocommerce_support()
{
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'saaslauncher_add_woocommerce_support');

// Old inline wizard JavaScript removed - now using external wizard-scripts.js file

/**
 * AI Workflow Wizard Shortcode (Modular Version)
 * 
 * Renders the complete AI workflow wizard using modular template files
 * Usage: [ai_workflow_wizard]
 */
function mgrnz_ai_workflow_wizard_shortcode() {
    ob_start();
    
    // Get the modular wizard PHP file
    $wizard_path = get_template_directory() . '/templates/wizard/wizard-main.php';
    
    if (file_exists($wizard_path)) {
        include $wizard_path;
    } else {
        echo '<div class="wizard-error" style="padding: 2rem; background: #fee; border: 1px solid #fcc; border-radius: 8px; color: #c00;">';
        echo '<strong>Error:</strong> Wizard template not found at: <code>templates/wizard/wizard-main.php</code>';
        echo '</div>';
    }
    
    return ob_get_clean();
}
add_shortcode('ai_workflow_wizard', 'mgrnz_ai_workflow_wizard_shortcode');

/**
 * Register AI Workflow Wizard Block Pattern
 * 
 * Makes the wizard available as a reusable block pattern in Gutenberg
 */
function mgrnz_register_wizard_pattern() {
    register_block_pattern(
        'mgrnz/ai-workflow-wizard',
        array(
            'title'       => __('AI Workflow Wizard', 'saaslauncher'),
            'description' => __('Complete 5-step AI workflow wizard with blueprint generation', 'saaslauncher'),
            'content'     => '<!-- wp:shortcode -->[ai_workflow_wizard]<!-- /wp:shortcode -->',
            'categories'  => array('featured', 'mgrnz'),
            'keywords'    => array('wizard', 'ai', 'workflow', 'form'),
        )
    );
}
add_action('init', 'mgrnz_register_wizard_pattern');

/**
 * Register Custom Block Pattern Category
 * 
 * Creates a custom category for MGRNZ patterns
 */
function mgrnz_register_pattern_category() {
    register_block_pattern_category(
        'mgrnz',
        array(
            'label' => __('MGRNZ', 'saaslauncher'),
        )
    );
}
add_action('init', 'mgrnz_register_pattern_category');