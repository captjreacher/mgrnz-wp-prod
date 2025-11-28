<?php
/**
 * AI Workflow Wizard - Main Container
 * This is the main wrapper that includes all wizard components
 */
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/templates/wizard/wizard-styles.css?v=<?php echo time(); ?>">
<div class="wizard-container">
    <!-- Wizard Header -->
    <header class="wizard-header">
      <h1>AI Workflow Wizard</h1>
      <p>Let's build your personalized AI workflow in 3 simple steps</p>
      <div id="debug-button-container"></div>
    </header>

    <!-- Wizard Form -->
    <form class="wizard-form" id="wizard-form">
      <!-- Progress Indicator -->
      <div class="wizard-progress">
        <div class="wizard-progress-bar" id="progress-bar" style="width: 33%;"></div>
        <div class="wizard-progress-text">Step <span id="current-step">1</span> of 3</div>
      </div>

      <?php 
      // Include individual step files
      include __DIR__ . '/wizard-step-1.php';
      include __DIR__ . '/wizard-step-2.php';
      include __DIR__ . '/wizard-step-3-chat.php';
      ?>
    </form>

    <!-- Progress Animation (Hidden Initially) -->
    <div class="progress-container" id="progress-animation">
      <div class="progress-messages" id="progress-messages"></div>
      <div class="progress-bar-container">
        <div class="progress-bar-fill" id="progress-fill"></div>
      </div>
    </div>

    <!-- Blueprint Section (Hidden Initially) -->
    <div class="blueprint-section" id="blueprint-section">
      <header class="blueprint-header">
        <h2>Your 2-minute AI Automation Blueprint</h2>
        <p>Here's your personalized plan to transform your workflow with AI.</p>
      </header>
      <div class="blueprint-content" id="blueprint-content"></div>
    </div>

  <?php include __DIR__ . '/wizard-completion.php'; ?>
</div>

<?php
// Calculate API settings safely in PHP
$api_root = get_rest_url();
// Fallback: If get_rest_url() is empty or doesn't include wp-json, use site_url
if (empty($api_root) || strpos($api_root, 'wp-json') === false) {
    $api_root = site_url('wp-json/');
}
// Ensure it ends with a slash
$api_root = trailingslashit($api_root);

$wizard_api_settings = [
    'root' => $api_root,
    'nonce' => wp_create_nonce('wp_rest')
];
?>
<script>
window.wpApiSettings = <?php echo json_encode($wizard_api_settings); ?>;
console.log('Wizard API Settings loaded:', window.wpApiSettings);
</script>
<script src="<?php echo get_template_directory_uri(); ?>/templates/wizard/wizard-scripts.js?v=<?php echo time(); ?>-<?php echo rand(1000, 9999); ?>"></script>
