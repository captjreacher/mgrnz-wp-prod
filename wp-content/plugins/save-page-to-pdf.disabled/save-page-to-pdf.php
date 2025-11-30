<?php
/*
Plugin Name: Save Page to PDF
Plugin URI: http://www.api2pdf.com
Description: Allows visitors of your website to download the current page as a PDF
Version: 1.0
Author: API2PDF
*/

// Specify Hooks/Filters
register_activation_hook(__FILE__, 'api2pdf_add_defaults_fn');
add_action('admin_init', 'api2pdf_savetopdf_init_fn' );
add_action('admin_menu', 'api2pdf_savetopdf_add_page_fn');

// Define default option settings
function api2pdf_add_defaults_fn() {
	
}

// Register our settings. Add the settings section, and settings fields
function api2pdf_savetopdf_init_fn(){
	register_setting('savePageToPdf_options', 'savePageToPdf_options', 'api2pdf_savePageToPdf_options_validate' );
	add_settings_section('main_section', 'Main Settings', 'api2pdf_section_text_fn', __FILE__);
	add_settings_field('savePageToPdf_apiKey', 'API Key', 'api2pdf_setting_string_fn', __FILE__, 'main_section');
	add_settings_field('savePageToPdf_support', 'Support Api2PDF', 'api2pdf_setting_chk1_fn', __FILE__, 'main_section');	
}

// Add sub page to the Settings Menu
function api2pdf_savetopdf_add_page_fn() {
	add_options_page('PDF Settings', 'PDF Settings', 'administrator', 'save-page-to-pdf', 'api2pdf_options_page_fn');
}

// ************************************************************************************************************

// Callback functions

// Section HTML, displayed before the first option
function  api2pdf_section_text_fn() {
	echo '<p>This plugin requires the use of an API key from API2PDF.com, get your <a href="https://portal.api2pdf.com" target=_blank>api key here</a>.</p>';
}


// TEXTBOX - Name: savePageToPdf_options[text_string]
function api2pdf_setting_string_fn() {
	$options = get_option('savePageToPdf_options');
	echo "<input id='savePageToPdf_apiKey' name='savePageToPdf_options[savePageToPdf_apiKey]' size='40' type='text' value='{$options['savePageToPdf_apiKey']}' />";
}


// CHECKBOX - Name: savePageToPdf_options[chkbox1]
function api2pdf_setting_chk1_fn() {
	$options = get_option('savePageToPdf_options');
	if($options['savePageToPdf_support']) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." id='savePageToPdf_support' name='savePageToPdf_options[savePageToPdf_support]' type='checkbox' /> Yes (Allow this plugin to place a small attribution link on my website)";
}

// Display the admin options page
function api2pdf_options_page_fn() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Save Page to PDF Options</h2>
		<form action="options.php" method="post">
		<?php settings_fields('savePageToPdf_options'); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}

// Validate user data for some/all of your input fields
function api2pdf_savePageToPdf_options_validate($input) {
	// Check our textbox option field contains no HTML tags - if so strip them out
	$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
	return $input; // return validated input
}


function api2pdf_savePageToPdf_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=save-page-to-pdf">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'api2pdf_savePageToPdf_add_settings_link' );



function api2pdf_savePageToPdf_enqueue_script() {   
    wp_enqueue_script( 'api2pdf', plugin_dir_url( __FILE__ ) . 'api2pdf.js', array('jquery'), '1.0' );
}
add_action('wp_enqueue_scripts', 'api2pdf_savePageToPdf_enqueue_script');


add_action( 'wp_ajax_api2pdf_ajax', 'api2pdf_ajax' );
add_action( 'wp_ajax_nopriv_api2pdf_ajax', 'api2pdf_ajax' );




add_action( 'wp_footer', 'api2pdf_addAjaxUrlToFooter' );
add_action( 'wp_footer', 'api2pdf_addPoweredByLinkToFooter' );

function api2pdf_addAjaxUrlToFooter() { 
    $url = admin_url("admin-ajax.php");
    echo "<script>window.Api2PdfWpAjaxUrl = '$url';</script>";

}

function api2pdf_addPoweredByLinkToFooter() { 
	if (get_option('savePageToPdf_options')["savePageToPdf_support"])
	{
		echo '<style>.api2PdfSupport {display: block; width:100%; font-size:x-small;}</style>';
		echo '<div class ="api2PdfSuport"><a href="https://www.api2pdf.com">WordPress PDF Generation Powered by Api2Pdf.com</a></div>';
	}

}


function api2pdf_shortcode()
{
   echo '<a class = "savePageToPdf" href="javascript:void(0);" onclick="window.SavePageToPdf();">Save to Pdf</a>';
}
add_shortcode('savepagetopdf', 'api2pdf_shortcode');



require ('api2pdf.php');
function api2pdf_ajax() {
	global $wpdb; // this is how you get access to the database
	$url = esc_url_raw($_POST['url']);
	$a2p_client = new Api2Pdf_Library(get_option('savePageToPdf_options')["savePageToPdf_apiKey"]);

	$result =  $a2p_client->api2pdf_wkhtmltopdf_from_url($url);
    echo json_encode($result);
	wp_die(); // this is required to terminate immediately and return a proper response
}
