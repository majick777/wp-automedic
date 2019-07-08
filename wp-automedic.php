<?php

/*
Plugin Name: WP AutoMedic
Plugin URI: http://wpmedic.tech/wp-automedic/
Description: Reloads broken images and stylesheets cross-browser with plain javascript. Reduces site load problems and visitor bounce rates.
Version: 1.5.0
Author: WP Medic
Author URI: http://wpmedic.tech
GitHub Plugin URI: majick777/wp-automedic
@fs_premium_only wp-automedic-pro.php
*/

if (!function_exists('add_action')) {exit;}

/* For Limitations, Known Issues and Planned Improvements see readme.txt */

// Development TODOs
// -----------------
// ? maybe use bwp_minify_ignore filter for Better WordPress Minify integration ?
// ? maybe add no minify filter for autoptimize and w3 total cache integration ?


// ====================
// --- Plugin Setup ---
// ====================
// 1.5.0: updated options to use plugin loader

// --------------
// Plugin Options
// --------------
$options = array(
	'switch'	=>	array(
						'type'		=> 'checkbox',
						'default'	=> '1',
					),
	'selfcheck'	=>	array(
						'type'		=> 'checkbox',
						'default'	=> '1',
					),
	'images'	=>	array(
						'type'		=> 'special',
						'default'	=> array(
							'reload' => 'frontend', 'delay' => 5, 'cycle' => '30', 'attempts' => 2,
							'external' => 1, 'cache' => 1, 'import' => 0, 'debug' => 0
						),
					),
	'styles'	=>	array(
						'type'		=> 'special',
						'default'	=> array(
							'reload' => 'frontend', 'delay' => 2, 'cycle' => '20', 'attempts' => 3,
							'external' => 1, 'cache' => 1, 'import' => 0, 'debug' => 0
						),
					),
);

// ---------------
// Loader Settings
// ---------------
// 1.5.0: updated settings to use plugin loader
$slug = 'wp-automedic';
$args = array(
	// --- Plugin Info ---
	'slug'			=> $slug,
	'file'			=> __FILE__,
	'version'		=> '0.0.1',

	// --- Menus and Links ---
	'title'			=> 'WP AutoMedic',
	'parentmenu'	=> 'wordquest',
	'home'			=> 'http://wpmedic.tech/wp-automedic/',
	'support'		=> 'http://wordquest.org/quest-category/'.$slug.'/',
	'share'			=> 'http://wpmedic.tech/wp-automedic/#share',
	'donate'		=> 'https://patreon.com/wpmedic',
	'donatetext'	=> __('Support WP Medic'),
	'welcome'		=> '',	// TODO

	// --- Options ---
	'namespace'		=> 'automedic',
	'option'		=> 'wp_automedic',
	'options'		=> $options,
	'settings'		=> 'am',

	// --- WordPress.Org ---
	'wporgslug'		=> 'wp-automedic',
	'textdomain'	=> 'wp-automedic',
	'wporg'			=> false,

	// --- Freemius ---
	'freemius_id'	=> '141',
	'freemius_key'	=> 'pk_443cc309c3298fe00933e523b38c8',
	'hasplans'		=> false,
	'hasaddons'		=> false,
	'plan'			=> 'free',
);

// ----------------------------
// Start Plugin Loader Instance
// ----------------------------
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'loader.php');
$instance = new automedic_loader($args);

// ------------------------------
// Remove Bonus Offer Sidebar Box
// ------------------------------
function am_sidebar_bonus_offer() {return;}


// -----------------------
// === Plugin Settings ===
// -----------------------

// ---------------------
// Transfer Old Settings
// ---------------------
// 1.4.0: transfer to global plugin option with indexed settings
function automedic_transfer_settings($settings=false) {
	if (!get_option('wp_automedic') && get_option('automedic_switch')) {

		$settings['switch'] = get_option('automedic_switch'); delete_option('automedic_switch');
		$settings['selfcheck'] = get_option('automedic_selfcheck'); delete_option('automedic_selfcheck');

		$temp['images'] = explode(',', get_option('automedic_images')); delete_option('automedic_images');
		$temp['styles'] = explode(',', get_option('automedic_stylesheets')); delete_option('automedic_stylesheets');

		foreach ($temp as $key => $values) {
			$automedic[$key] = array(
				'reload' => $values[1], 'delay' => $values[2],
				'cycle' => $values[3], 'attempts' => $values[4], 'external' => $values[5],
				'cache' => $values[6], 'import' => '0', 'debug' => $values[7]
			);
		}
		update_option('wp_automedic', $settings);

		// 1.4.5: merge with existing settings
		global $automedic; foreach ($settings as $key => $value) {$automedic[$key] = $value;}
	}
}

// ------------------------------------
// Filter Reload Keys (for Back Compat)
// ------------------------------------
// 1.5.0: added this filter
add_filter('automedic_images', 'automedic_reload_key_fix');
add_filter('automedic_styles', 'automedic_reload_key_fix');
function automedic_reload_key_fix($value) {
	if (isset($value['reload']) && ($value['reload'] == '1')) {$value['reload'] = 'both';}
	return $value;
}

// -----------------------
// Process Settings Update
// -----------------------
function automedic_process_settings($options=false) {

	// 1.4.0: use global option value
	global $automedic; $settings = $automedic;

	// 1.4.5: use simplified defaults array
	$defaults = automedic_default_settings();
	// get existing settings for fallbacks
	$previous['images'] = $automedic['images'];
	$previous['styles'] = $automedic['styles'];

	// automedic switch
	if (isset($_POST['am_automedic_switch'])) {
		$automedicswitch = $_POST['am_automedic_switch'];
		if ($automedicswitch != '1') {$automedicswitch = '0';}
	} else {$automedicswitch = '0';}
	$settings['switch'] = $automedicswitch;

	// self-check switch
	if (isset($_POST['am_automedic_selfcheck'])) {
		$automedicselfcheck = $_POST['am_automedic_selfcheck'];
		if ($automedicselfcheck != '1') {$automedicselfcheck = '0';}
	} else {$automedicselfcheck = '0';}
	$settings['selfcheck'] = $automedicselfcheck;

	$types = array('images', 'styles');
	// 1.4.5: added file import settings key
	$keys = array('reload', 'delay', 'cycle', 'attempts', 'external', 'cache', 'import', 'debug');
	$inputs = array('delay', 'cycle', 'attempts');
	$checkboxes = array('external', 'cache', 'import', 'debug');
	// 1.4.5: fix to admin-only saving key
	$scopes = array('frontend', 'admin', 'both', 'off');

	foreach ($types as $type) {
		foreach ($keys as $key) {
			$postkey = 'am_'.$type.'_'.$key;
			if (isset($_POST[$postkey])) {$value = $_POST[$postkey];} else {$value = '';}
			$debugposted[$type][$key] = $value;

			// validate settings
			if (in_array($key, $inputs)) {
				// 1.4.5: fix to fallback array key index
				if (!is_numeric($value)) {$value = $previous[$type][$key];}
				if (!is_numeric($value)) {$value = $defaults[$type][$key];}
			} elseif (in_array($key, $checkboxes)) {
				if ($value == '') {$value = '0';}
			} elseif ($key == 'reload') {
				// 1.4.0: validate exact scope options
				if (!in_array($value, $scopes)) {$value = 'off';}
			}
			$settings[$type][$key] = $value;
		}
		// 1.4.5: removed unused context key (reload key is now used for contexts)
		if (isset($settings[$type]['context'])) {unset($settings[$type]['context']);}
	}

	// 1.4.0: maybe update pro options also
	if (function_exists('automedic_pro_update_options')) {automedic_pro_update_options();}

	// $debug = "Posted Values: ".print_r($debugposted,true).PHP_EOL;
	// $debug .= "Validated Values: ".print_r($automedic,true).PHP_EOL;
	// error_log($debug, 3, dirname(__FILE__).'/save-debug.log');

	return $settings;

}

// -------------
// Settings Page
// -------------
function automedic_settings_page() {

	// 1.4.0: use global plugin option
	global $automedic; $settings = $automedic;
	$slug = $settings['slug']; $namespace = $settings['namespace'];

	// 1.4.5: use get_setting to handle defaults (not filtered)
	$images = automedic_get_setting('images', false);
	$styles = automedic_get_setting('styles', false);

	// 1.4.0: added pagewrap styles
	echo '<div id="pagewrap" class="wrap" style="width:100%;margin-right:0px !important;">';

	// Sidebar Floatbox
	// ----------------
	$args = array($slug, 'yes'); // trimmed settings
	if (function_exists('wqhelper_sidebar_floatbox')) {
		wqhelper_sidebar_floatbox($args);

		// 1.4.0: replace floatmenu with stickykit
		echo wqhelper_sidebar_stickykitscript();
		echo '<style>#floatdiv {float:right;}</style>';
		echo '<script>jQuery("#floatdiv").stick_in_parent();
		wrapwidth = jQuery("#pagewrap").width(); sidebarwidth = jQuery("#floatdiv").width();
		newwidth = wrapwidth - sidebarwidth;
		jQuery("#wrapbox").css("width",newwidth+"px");
		jQuery("#adminnoticebox").css("width",newwidth+"px");
		</script>';
	}

	// Admin Notices Boxer
	// -------------------
	if (function_exists('wqhelper_admin_notice_boxer')) {wqhelper_admin_notice_boxer();}

	// Plugin Admin Settings Header
	// ----------------------------
	automedic_settings_header();

	// 1.4.5: moved welcome message here
	// if (isset($_REQUEST['welcome']) && ($_REQUEST['welcome'] == 'true')) {
	// }

	// 1.4.5: added reset to defaults script
	$confirmreset = __('Are you sure you want to reset this plugin to default settings?','wp-automedic');
	echo "<script>function resettodefaults() {
		agree = confirm('".$confirmreset."'); if (!agree) {return false;}
		document.getElementById('settings-update-action').value = 'reset';
		document.getElementById('settings-update-form').submit();
	}</script>";

	// Start Form
	// ----------
	echo "<form method='post' id='settings-update-form'>";
	echo "<input type='hidden' id='settings-update-action' name='".$namespace."_update_settings' value='yes'>";
	// 1.4.0: added nonce field(s)
	// 1.4.5: use plugin slug for nonce key
	wp_nonce_field('wp-automedic');

	// 1.4.0: added wrap box
	echo "<div id='wrapbox' class='postbox' style='width:680px;line-height:2em;'><div class='inner' style='padding-left:20px;'>";

	// Main Switches
	// -------------
	// 1.4.5: use get_setting to handle defaults (not filtered)
	echo "<table><tr height='20'><td></td></tr>";
	echo "<tr><td colspan='3'><b>".__('Enable WP AutoMedic?','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><input name='am_automedic_switch' type='checkbox' value='1'";
		if (automedic_get_setting('switch', false) == '1') {echo " checked";}
	echo "></td><td></td>";
	echo "<td colspan='8' align='right'>".__('Do self-load check?','wp-automedic')."</td>";
	echo "<td align='center'><input name='am_automedic_selfcheck' type='checkbox' value='1'";
		if (automedic_get_setting('selfcheck', false) == '1') {echo " checked";}
	echo "></td></tr>";
	echo "<tr height='20'><td></td></tr>";

	// Table Headers
	// -------------
	// 1.4.5: added import setting column
	echo "<tr><td>".__('Resource Type','wp-automedic')."</td><td width='10'></td>";
	echo "<td align='center'><b>".__('Context','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Delay','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Cycle','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Attempts','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Externals','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Import','wp-automedic')."</b></td><td width='10'></td>";
	// echo "<td align='center'><b>".__('Cache','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Debug','wp-automedic')."</b></td></tr>";

	// Images
	// ------
	echo "<tr><td><b>".__('Image Reloader','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><select name='am_images_reload'>";
	echo "<option value='frontend'";
		if ($images['reload'] == 'frontend') {echo " selected='selected'";}
	echo ">".__('Frontend','wp-automedic')."</option>";
	echo "<option value='admin'";
		if ($images['reload'] == 'admin') {echo " selected='selected'";}
	echo ">".__('Admin','wp-automedic')."</option>";
	echo "<option value='both'";
		if ($images['reload'] == 'both') {echo " selected='selected'";}
	echo ">".__('Both','wp-automedic')."</option>";
	echo "<option value='off'";
		if ( ($images['reload'] == 'off') || ($images['reload'] == '') ) {echo " selected='selected'";}
	echo ">".__('Off','wp-automedic')."</option>";
	echo "</select></td><td width='10'></td>";

	// --- image reload delay ---
	echo "<td align='center'><input name='am_images_delay' type='text' style='width:30px;' value='".$images['delay']."'>s</td><td width='10'></td>";
	// --- image reload cycling ---
	echo "<td align='center'><input name='am_images_cycle' type='text' style='width:30px;' value='".$images['cycle']."'>s</td><td width='10'></td>";
	// --- image reload attempts ---
	echo "<td align='center'><input name='am_images_attempts' type='text' style='width:30px;' value='".$images['attempts']."'></td><td width='10'></td>";
	// --- external images ---
	echo "<td align='center'><input name='am_images_external' type='checkbox' value='1'";
		if ($images['external'] == '1') {echo " checked";}
	echo "></td><td width='10'></td>";
	// --- import external images ---
	// 1.4.5: added this hidden option (not implemented yet)
	echo "<td align='center'><input name='am_images_import' type='hidden' value='0'></td><td width='10'></td>";
	// --- image caching ---
	echo "<input name='am_image_cache' type='hidden' value='0'>";
	// echo "<td align='center'>dev";
	// echo "<input name='am_images_cache' type='checkbox' value='1'";
	// if (images['cache'] == '1') {echo " checked";}
	// echo "></td><td width='10'></td>";
	// --- debug image reloading ---
	echo "<td align='center'><input name='am_images_debug' type='checkbox' value='1'";
		if ($images['debug'] == '1') {echo " checked";}
	echo "></td></tr>";

	// Stylesheets
	// -----------
	echo "<tr><td><b>".__('Stylesheet Reloader','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><select name='am_styles_reload'>";
	echo "<option value='frontend'";
		if ($styles['reload'] == 'frontend') {echo " selected='selected'";}
	echo ">".__('Frontend','wp-automedic')."</option>";
	echo "<option value='admin'";
		if ($styles['reload'] == 'admin') {echo " selected='selected'";}
	echo ">".__('Admin','wp-automedic')."</option>";
	echo "<option value='both'";
		if ($styles['reload'] == 'both') {echo " selected='selected'";}
	echo ">".__('Both','wp-automedic')."</option>";
	echo "<option value='off'";
		if ( ($styles['reload'] == 'off') || ($styles['reload'] == '') ) {echo " selected='selected'";}
	echo ">".__('Off','wp-automedic')."</option>";
	echo "</select></td><td width='10'></td>";

	// --- stylesheet reload delay ---
	echo "<td align='center'><input name='am_styles_delay' type='text' style='width:30px;' value='".$styles['delay']."'>s</td><td width='10'></td>";
	// --- stylesheet reload cycling ---
	echo "<td align='center'><input name='am_styles_cycle' type='text' style='width:30px;' value='".$styles['cycle']."'>s</td><td width='10'></td>";
	// --- stylesheet reload attempts
	echo "<td align='center'><input name='am_styles_attempts' type='text' style='width:30px;' value='".$styles['attempts']."'></td><td width='10'></td>";
	// --- reload external styles ---
	echo "<td align='center'><input name='am_styles_external' type='checkbox' value='1'";
		if ($styles['external'] == '1') {echo " checked";}
	echo "></td><td width='10'></td>";
	// --- import external stylesheets ---
	// 1.4.5: added this new setting for style @imports
	echo "<td align='center'><input name='am_styles_import' type='checkbox' value='1'";
		if ($styles['import'] == '1') {echo " checked";}
	echo "></td><td width='10'></td>";
	// --- stylesheet caching ---
	echo "<input name='am_styles_cache' type='hidden' value='0'>";
	// echo "<td align='center'>dev";
	// echo "<input name='am_style_cache' type='checkbox' value='1'";
		// if ($styles['cache'] == '1') {echo " checked";}
	// echo "></td><td width='10'></td>";
	// --- stylesheet reload debugging ---
	echo "<td align='center'><input name='am_styles_debug' type='checkbox' value='1'";
		if ($styles['debug'] == '1') {echo " checked";}
	echo "></td></tr>";

	// 1.4.0: maybe output pro settings inputs
	if (function_exists('automedic_pro_settings_page')) {automedic_pro_settings_page();}

	echo "<tr height='20'><td> </td></tr>";
	echo "<tr><td></td>";

	// 1.4.5: add settings reset button
	echo "<td colspan='7' align='center'>";
	echo "<input class='button-secondary' type='button' onclick='return resettodefaults();' value='".__('Reset Settings','wp-automedic')."'>";
	echo "</td><td></td>";
	echo "<td colspan='7' align='center'>";
	echo "<input class='button-primary' type='submit' value='".__('Update Settings','wp-automedic')."'>";
	echo "</td></tr>";

	echo "</table></form><br>"; // close table form

	echo '</div></div>'; // close #wrapbox

	echo '</div>'; // close #pagewrap
}


// =======================
// --- Enqueue Scripts ---
// =======================

// -----------------------
// Enqueue Reloader Script
// -----------------------
// 1.4.0: also add action to admin_enqueue_scripts
add_action('admin_enqueue_scripts', 'automedic_enqueue_script');
add_action('wp_enqueue_scripts', 'automedic_enqueue_script');
function automedic_enqueue_script() {

	global $automedic;
	if ($automedic['switch'] != '1') {return;}

	// get standard options
	$images = automedic_get_setting('images');
	$styles = automedic_get_setting('styles');

	// $deps = array('jquery'); // jquery dependency no longer needed
	$deps = array(); // javascript only is needed now

	// set version as current time to avoid caching
	// 1.4.0: set to settings save time for efficiency
	$ver = automedic_get_setting('savetime');
	// 1.4.5: only fallback to current time if null
	if (is_null($ver)) {$ver = time();}

	// 1.4.0: default global script URL to false
	global $automedic; $automedic['script'] = false;

	// 1.4.0: moved debug switches to variable output
	// 1.4.0: moved load variable printing to script loader tag
	// 1.4.0: check the context for image and stylesheet reloading
	// 1.4.5: simplified and simplified context checking
	$contexts = array('both');
	if (is_admin()) {$contexts[] = 'admin';} else {$contexts[] = 'frontend';}
	if (in_array($images['reload'], $contexts) || in_array($styles['reload'], $contexts)) {
		// 1.5.0: move script to scripts directory
		$automedic['script'] = plugins_url('scripts/automedic.js', __FILE__);
	}

	// 1.4.0: maybe just enqueue the reloader javascript now
	// 1.4.2: remove the -reloader suffix from script handle
	if ($automedic['script']) {
		wp_enqueue_script('automedic', $automedic['script'], $deps, $ver);

		// --- add script variables in footer ---
		// 1.5.0: added these actions here ---
		add_action('wp_footer', 'automedic_script_variables');
		add_action('admin_footer', 'automedic_script_variables');
	}
}

// --------------------
// Set Script Variables
// --------------------
function automedic_script_variables() {

	// 1.4.5: set context array for easy checking
	$contexts = array('both');
	if (is_admin()) {$context = $contexts[] = 'admin';} else {$context = $contexts[] = 'frontend';}

	$images = automedic_get_setting('images');
	$styles = automedic_get_setting('styles');

	// set console logging debug switches
	// 1.4.0: simplified debug switch overrides
	if ( (isset($_REQUEST['imagedebug'])) && ($_REQUEST['imagedebug'] == '1') ) {$images['debug'] = '1';}
	if ( (isset($_REQUEST['styledebug'])) && ($_REQUEST['styledebug'] == '1') ) {$styles['debug'] = '1';}

	// Start Javascript
	// ----------------
	global $automedic;

	echo "<script>";
	echo "/* WP AutoMedic ".$automedic['version']." - ".$automedic['home']." */".PHP_EOL;

	// Set Site URL for external checks
	// --------------------------------
	$sitehost = $_SERVER['HTTP_HOST'];
	echo "var amSiteHost = '".$sitehost."'; ";

	// Set Admin AJAX URL
	// ------------------
	$adminajaxurl = admin_url('admin-ajax.php');
	echo "var amAjaxUrl = '".$adminajaxurl."'; ";

	// Output Config Variables
	// -----------------------
	// 1.4.5: simplified and streamlined context checking
	// TODO: compress settings to single javascript variable
	if (in_array($images['reload'], $contexts)) {

		$imagecycling = '0';
		if ( ($images['cycle'] > 0) && ($images['attempts'] > 0) ) {$imagecycling = '1';}

		// 1.5.0: removed single quotes from all values
		echo " var amImageReload = 1;";
		echo " var amImageCycling = ".$imagecycling.";";
		echo " var amImageCycles = ".$images['cycle'].";";
		echo " var amImageDelay = ".$images['delay'].";";
		echo " var amImageAttempts = ".$images['attempts'].";";
		echo " var amImageExternal = ".$images['external'].";";
		echo " var amImageCache = ".$images['cache'].";";
		echo " var amImageDebug = ".$images['debug'].";";
	} else {echo " var amImageReload = 0;";}

	if (in_array($styles['reload'], $contexts)) {

		$stylecycling = '0';
		if ( ($styles['cycle'] > 0) && ($styles['attempts'] > 0) ) {$stylecycling = '1';}

		// 1.5.0: removed single quotes from values
		echo " var amStyleReload = 1;";
		echo " var amStyleCycling = ".$stylecycling.";";
		echo " var amStyleCycles = ".$styles['cycle'].";";
		echo " var amStyleDelay = ".$styles['delay'].";";
		echo " var amStyleAttempts = ".$styles['attempts'].";";
		echo " var amStyleExternal = ".$styles['external'].";";
		echo " var amStyleCache = ".$styles['cache'].";";
		echo " var amStyleDebug = ".$styles['debug'].";";
	}  else {echo " var amStyleReload = 0;";}

	// 1.4.0: maybe output javascript variables for pro version
	if (function_exists('automedic_pro_script_variables')) {automedic_pro_script_variables($context);}

	echo "</script>";

}

// ---------------
// Self Load Check
// ---------------
if ( ($automedic['switch']) && ($automedic['selfcheck']) ) {

	add_action('wp_footer', 'automedic_self_load_check',99);
	// 1.4.0: add admin_footer action for backend self check
	add_action('admin_footer', 'automedic_self_load_check',99);

	function automedic_self_load_check() {

		// 1.4.0: check/use enqueued script URL global
		global $automedic; if (!$automedic['script']) {return;}

		// 1.4.0: manually append current time for cachebusting
		// 1.4.5: use of add_query_arg here
		$automedic['script'] = add_query_arg('version', time(), $automedic['script']);

		// so much simpler to check than for dynamic scripts,
		// as we actually know a function name to test for..!
		echo "<script>if (typeof amCacheBust != 'function') {".PHP_EOL;
		echo "	ams = document.createElement('script');".PHP_EOL;
		echo "	ams.src = '".$automedic['script']."';".PHP_EOL;
		echo "	document.body.appendChild(ams);".PHP_EOL;
		echo "}</script>".PHP_EOL;
	}
}

// -----------------------------------
// Better WordPress Minify Integration
// -----------------------------------
// TODO: maybe use bwp_minify_ignore filter for Better WordPress Minify integration ?
// 1.4.2: automatically ignore some styles for BWP plugin
add_action('wp_enqueue_scripts', 'automedic_bwp');
function automedic_bwp() {
	global $bwp_minify;
	if ( (is_object($bwp_minify)) && (property_exists($bwp_minify, 'print_positions')) ) {
		$positions = $bwp_minify->print_positions;
		if ( (is_array($positions)) && (isset($positions['ignore'])) ) {
			$handles = $positions['ignore'];
			$nominifyscripts = array('automedic');
			foreach ($nominifyscripts as $handle) {
				if (!in_array($handle, $handles)) {$handles[] = $handle;}
			}
			if ($handles != $positions['ignore']) {
				$positions['ignore'] = $handles;
				$bwp_minify->print_positions = $positions;
			}
		}
	}
}

// -----------------------------
// AJAX Load of AutoMedic Script
// -----------------------------
// 1.4.0: [deprecated] AJAX load method
// add_action('wp_ajax_am_reloader', 'automedic_reload_script');
// add_action('wp_ajax_nopriv_am_reloader', 'automedic_reload_script');

// ----------------------
// Output Reloader Script
// ----------------------
// 1.4.0: [deprecated] PHP to JS file method
// function automedic_reload_script() {
//	$reloaderjs = plugins_url('scripts/automedic-reloader.js.php', __FILE__);
//	if (file_exists($reloaderjs)) {require($reloaderjs);} exit;
// }


// ========================
// --- Wordpress Styles ---
// ========================

// ----------------------------
// Process Wordpress Style Tags
// ----------------------------
// 1.4.0: converted to init action
add_action('init', 'automedic_check_style_reload');
function automedic_check_style_reload() {

	// 1.4.0: removed unneeded global flag
	$styles = automedic_get_setting('styles');

	// 1.4.5: simplify and streamline context checking
	$contexts = array('both');
	if (is_admin()) {$contexts[] = 'admin';} else {$contexts[] = 'frontend';}
	if (in_array($styles['reload'], $contexts)) {
		add_filter('style_loader_tag', 'automedic_process_style_tags', 11, 2);
	}

}

// ----------------------------
// Process WordPress Style Tags
// ----------------------------
function automedic_process_style_tags($link, $handle) {

	global $automedic;

	// 1.5.0: maybe set stylenum start
	if (!isset($automedic['stylenum'])) {$automedic['stylenum'] = 1;}

	// do a filter check using the style handle
	// (returning false for a handle will skip this tag)
	$doautomedic = apply_filters('automedic_style_check', $handle);
	if (!$doautomedic) {return $link;}

	$style[$automedic['stylenum']] = $link;
	$stylekeys = automedic_extract_tag_attributes($style, 'style');

	// skip tag if extraction failed
	if (!is_array($stylekeys)) {return $link;}

	// skip if not to process or already processed
	if ( (isset($stylekeys[$automedic['stylenum']]['noautomedic']))
	  || (isset($stylekeys[$automedic['stylenum']]['automedicated'])) ) {return $link;}

	// skip tag if no href value found
	if (!isset($stylekeys[$automedic['stylenum']]['href'])) {return $link;}
	elseif ($stylekeys[$automedic['stylenum']]['href'] == '') {return $link;}
	$stylehref = $stylekeys[$automedic['stylenum']]['href'];

	// check tag for external stylesheet
	// 1.4.1: improved check for external stylesheets
	// 1.4.5: fix to mismatch variable name for external styles
	$externalstyle = false;
	if ( (strpos($stylehref, $_SERVER['HTTP_HOST']) !== 0)
	  && ( (strpos($stylehref, 'http:') === 0) || (strpos($stylehref, 'https:') === 0) ) ) {
	  	$externalstyle = true;
	}

	// skip if not to reload external stylesheets
	// 1.4.0: use keyed external option for stylesheets
	$styles = automedic_get_setting('styles');
	if ($externalstyle && (!$styles['external']) ) {return $link;}

	// special: maybe convert external stylesheet href to internal @import
	if ($externalstyle && ($styles['import'] == 'yes')) {
		$nulink = automedic_rebuild_style_tag($stylekeys, 'style', $automedic['stylenum'], true);
	} else {
		$nulink = automedic_rebuild_style_tag($stylekeys, 'style', $automedic['stylenum']);
	}

	$automedic['stylenum']++;

	// TODO: maybe revalidate the final tag (just in case?)

	return $nulink;
}

// -------------------------------
// Extract Attribute Keys from Tag
// -------------------------------
function automedic_extract_tag_attributes($tagelements, $tag) {

	$i = 0;
	foreach ($tagelements as $element) {
		$element = str_ireplace('</'.$tag.'>', '', $element); // '>
		$element = str_ireplace('<'.$tag, '', $element); // '>
		$element = trim($element);
		if (substr($element,-1) == '>') {$element = substr($element, 0, strlen($element)-1);}

		// if has quotes, replace spaces inside them
		if ( (strstr($element, '"')) || (strstr($element, "'")) ) {
			// replace spaces inside double quotes with a placeholder
			$tempelement = $element;
			if (strstr($tempelement, '"')) {
				while (strstr($tempelement, '"')) {
					$pos = strpos($tempelement, '"') + 1;
					$chunks = str_split($tempelement, $pos);
					unset($chunks[0]);
					$temp = implode('', $chunks);
					if (strstr($temp, '"')) {
						$pos = strpos($temp, '"');
						$chunks = str_split($temp, $pos);
						$inside = $chunks[0];
						$nuinside = str_replace(' ', '|---|', $inside);
						// another important fix, replace = inside quotes!
						$nuinside = str_replace('=', '|-|-|', $nuinside);
						$element = str_replace('"'.$inside.'"', '"'.$nuinside.'"', $element);
						$tempelement = str_replace('"'.$inside.'"', '', $tempelement);
					} else {continue;} // bug out for unclosed quotes
					// TODO: better fix for unclosed quotes?
					// ...but this is way above and beyond the call of duty
				}
			}
			// replace spaces inside single quotes with a placeholder
			$tempelement = $element;
			if (strstr($tempelement, "'")) {
				while (strstr($tempelement, "'")) {
					$pos = strpos($tempelement, "'") + 1;
					$chunks = str_split($tempelement, $pos);
					unset($chunks[0]);
					$temp = implode('', $chunks);
					if (strstr($temp, "'")) {
						$pos = strpos($temp, "'");
						$chunks = str_split($temp, $pos);
						$inside = $chunks[0];
						$nuinside = str_replace(' ', '|---|',$inside);
						// another important fix, replace = inside quotes!
						$nuinside = str_replace('=', '|-|-|', $nuinside);
						$element = str_replace("'".$inside."'", "'".$nuinside."'", $element);
						$tempelement = str_replace("'".$inside."'", "", $tempelement);
					} else {continue;} // bug out for unclosed quotes
				}
			}
		}

		// replace all other spaces not in quotes
		$element = str_replace(" ", "|||", $element);
		// print_r($element); // debug point

		// split the tag string at our replaced spaces
		if (strstr($element,'|||')) {$chunks = explode('|||', $element);}
		else {$chunks[0] = $element;}
		// print_r($chunks); // debug point

		foreach ($chunks as $chunk) {

			if (strstr($chunk, '=')) {
				$parts = explode('=', $chunk);
				$thiskey = trim($parts[0]);

				$thisvalue = trim($parts[1]);
				if ( (substr($thisvalue, 0, 1) == '"') && (substr($thisvalue, -1) == '"') ) {
					$thisvalue = substr($thisvalue, 1, strlen($thisvalue)-1);
					// put back the spaces and = inside quotes
					$thisvalue = str_replace('|---|', ' ', $thisvalue);
					$thisvalue = str_replace('|-|-|', '=', $thisvalue);
				}
				elseif ( (substr($thisvalue, 0, 1) == "'") && (substr($thisvalue, -1) == "'") ) {
					$thisvalue = substr($thisvalue, 1, strlen($thisvalue)-1);
					// put back the spaces and = inside quotes
					$thisvalue = str_replace('|---|', ' ', $thisvalue);
					$thisvalue = str_replace('|-|-|', '=', $thisvalue);
				}
				// special: fix case variations of to specific cased version for later
				if (strtolower($thiskey) == 'src') {$thiskey = 'src';}
				if (strtolower($thiskey) == 'onload') {$thiskey = 'onLoad';}
				if (strtolower($thiskey) == 'onerror') {$thiskey = 'onError';}
				if (strtolower($thiskey) == 'onreadystatechange') {$thiskey = 'onReadyStateChange';}
				$tagkeys[$i][$thiskey] = $thisvalue;
			} else {
				// single attribute no value, and ignore final /
				if ($chunk != '/') {$tagkeys[$i][$chunk] = '';}
			}
		}
		$i++;
	}
	return $tagkeys;
}

// -------------------------------------
// Rebuild Style Tag from Extracted Keys
// -------------------------------------
// 1.4.0: separate stylesheet tag rebuild function
function automedic_rebuild_style_tag($tagkeys, $tag, $num, $import=false) {

	$nutag = '<'.$tag; 	// '>
	foreach ($tagkeys[$num] as $key => $value) {
		if ($value == '') {$nutag .= ' '.$key;}
		elseif ( (stristr($key, 'href')) && ($import) ) {
			// for importing external stylesheets
			$href = $value;
		} else {
			$useouterquote = '"';
			// if (strstr($value,"'")) {$useouterquote = '"';}
			if (strstr($value, '"')) {$useouterquote = "'";}
			$nutag .= ' '.$key.'='.$useouterquote.$value.$useouterquote;
		}
	}

	$nutag .= ' automedicated'; // add the automedicated attribute
	$nutag .= ' />';
	if ($import) {$nutag .= '@import("'.$href.'");';}
	$nutag .= '</'.$tag.'>';
	return $nutag;
}


// --------------------------
// AutoMedic Test Page Output
// --------------------------
add_shortcode('automedic-test', 'automedic_test_shortcode_output');

function automedic_test_shortcode_output() {

$output = '

<script>function iframeloaded(url) {console.log(\'Child Frame says it is loaded:\'+url);}
function pingback() {alert(\'yes\');}</script>

<input type="hidden" id="childtest" value="" onchange="alert(this.value);">


<h3>Images</h3>

<img src="test.png">

<br><br>

<img src="icon.png">


<h3>Stylesheets</h3>

Test StyleSheet<br>
<link rel="stylesheet" href="test.css" title="mainsheet" onLoad="console.log(\'Stylesheet Load Callback\');" onReadyStateChange="console.log(\'Stylesheet onReadyStateChange Callback\');">
Alternate StyleSheet<br>
<link rel="alternate stylesheet" href="alternative.css" title="fallbacksheet">

Broken StyleSheet<br>
<link rel="stylesheet" href="brokensheet.css" title="maintest" onError="console.log(\'Stylesheet Error Callback\');">
Alternate StyleSheet<br>
<link rel="alternate stylesheet" href="alternative.css" title="fallbacksheet">

<br>'; // end test HTML;

	return $output;
}

