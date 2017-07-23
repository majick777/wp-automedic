<?php

/*
Plugin Name: WP AutoMedic
Plugin URI: http://wordquest.org/plugins/wp-automedic/
Description: Reloads broken images, stylesheets, scripts, iframes cross-browser with plain javascript. Reduce site load problems and bounce rates.
Version: 1.4.0
Author: Tony Hayes
Author URI: http://dreamjester.net
@fs_premium_only pro-functions.php
*/

if (!function_exists('add_action')) {exit;}

/* for Limitations, Known Issues, Planned Improvements see the readme.txt */

// --------------------
// === Plugin Setup ===
// --------------------

// -----------------
// Set Plugin Values
// -----------------
global $wordquestplugins, $vautomedicslug, $vautomedicversion;
$vslug = $vautomedicslug = 'wp-automedic';
$wordquestplugins[$vslug]['version'] = $vautomedicversion = '1.4.0';
$wordquestplugins[$vslug]['title'] = 'WP AutoMedic';
$wordquestplugins[$vslug]['namespace'] = 'automedic';
$wordquestplugins[$vslug]['settings'] = 'am';
$wordquestplugins[$vslug]['hasplans'] = false;
// $wordquestplugins[$vslug]['wporgslug'] = 'wp-automedic';

// ------------------------
// Check for Update Checker
// ------------------------
// note: lack of updatechecker.php file indicates WordPress.Org SVN version
// presence of updatechecker.php indicates site download or GitHub version
$vfile = __FILE__; $vupdatechecker = dirname($vfile).'/updatechecker.php';
if (!file_exists($vupdatechecker)) {$wordquestplugins[$vslug]['wporg'] = true;}
else {include($vupdatechecker); $wordquestplugins[$vslug]['wporg'] = false;}

// ----------------------------------
// Load WordQuest Admin/Pro Functions
// ----------------------------------
$wordquest = dirname(__FILE__).'/wordquest.php';
if ( (is_admin()) && (file_exists($wordquest)) ) {include($wordquest);}
$vprofunctions = dirname(__FILE__).'/pro-functions.php';
if (file_exists($vprofunctions)) {include($vprofunctions); $wordquestplugins[$vslug]['plan'] = 'premium';}
else {$wordquestplugins[$vslug]['plan'] = 'free';}

// -------------
// Load Freemius
// -------------
function automedic_freemius($vslug) {
    global $wordquestplugins, $automedic_freemius;
    $vwporg = $wordquestplugins[$vslug]['wporg'];
	if ($wordquestplugins[$vslug]['plan'] == 'premium') {$vpremium = true;} else {$vpremium = false;}
	$vhasplans = $wordquestplugins[$vslug]['hasplans'];

	// redirect for support forum
	if ( (is_admin()) && (isset($_REQUEST['page'])) ) {
		if ($_REQUEST['page'] == $vslug.'-wp-support-forum') {
			if (!function_exists('wp_redirect')) {include(ABSPATH.WPINC.'/pluggable.php');}
			wp_redirect('http://wordquest.org/quest/quest-category/plugin-support/'.$vslug.'/'); exit;
		}
	}

    if (!isset($automedic_freemius)) {
        if (!class_exists('Freemius')) {require_once(dirname(__FILE__).'/freemius/start.php');}

		$automedic_settings = array(
            'id'                => '141',
            'slug'              => $vslug,
            'public_key'        => 'pk_443cc309c3298fe00933e523b38c8',
            'is_premium'        => $vpremium,
            'has_addons'        => false,
            'has_paid_plans'    => $vhasplans,
            'is_org_compliant'  => $vwporg,
            'menu'              => array(
                'slug'       	=> $vslug,
                'first-path' 	=> 'admin.php?page='.$vslug.'&welcome=true',
                'parent'		=> array('slug'=>'wordquest'),
                'contact'		=> $vpremium,
                // 'support'    => false,
                // 'account'    => false,
            )
        );
        $automedic_freemius = fs_dynamic_init($automedic_settings);
    }
    return $automedic_freemius;
}
// initialize Freemius
$automedic_freemius = automedic_freemius($vslug);

// Custom Freemius Connect Message
function automedic_freemius_connect($message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link) {
	return sprintf(
		__fs('hey-x').'<br>'.
		__("If you want to more easily provide feedback for this plugins features and functionality, %s can connect your user, %s at %s, to %s", 'wp-automedic'),
		$user_first_name, '<b>'.$plugin_title.'</b>', '<b>'.$user_login.'</b>', $site_link, $freemius_link
	);
}
// 1.4.0: added object and method check
if ( (is_object($automedic_freemius)) && (method_exists($automedic_freemius,'add_filter')) ) {
	$automedic_freemius->add_filter('connect_message', 'automedic_freemius_connect', WP_FS__DEFAULT_PRIORITY, 6);
}

// ---------------
// Add Admin Pages
// ---------------
add_action('admin_menu','automedic_settings_menu',1);
function automedic_settings_menu() {

	// maybe add Wordquest top level menu
	if (empty($GLOBALS['admin_page_hooks']['wordquest'])) {
		$vicon = plugins_url('images/wordquest-icon.png',__FILE__); $vposition = apply_filters('wordquest_menu_position','3');
		add_menu_page('WordQuest Alliance', 'WordQuest', 'manage_options', 'wordquest', 'wqhelper_admin_page', $vicon, $vposition);
	}

	// add Plugin Submenu
	add_submenu_page('wordquest', 'WP AutoMedic', 'WP AutoMedic', 'manage_options', 'wp-automedic', 'automedic_settings_page');

	// Add icons and styling to the plugin submenu :-)
	add_action('admin_footer','automedic_admin_javascript');
	function automedic_admin_javascript() {
		global $vautomedicslug; $vslug = $vautomedicslug; $vcurrent = '0';
		$vicon = plugins_url('images/icon.png',__FILE__);
		if ( (isset($_REQUEST['page'])) && ($_REQUEST['page'] == $vslug) ) {$vcurrent = '1';}
		echo "<script>jQuery(document).ready(function() {if (typeof wordquestsubmenufix == 'function') {
		wordquestsubmenufix('".$vslug."','".$vicon."','".$vcurrent."');} });</script>";
	}

	// Plugin Page Settings Link
	add_filter('plugin_action_links', 'automedic_plugin_action_links', 10, 2);
	function automedic_plugin_action_links($vlinks, $vfile) {
		global $vautomedicslug;
		$vthisplugin = plugin_basename(__FILE__);
		if ($vfile == $vthisplugin) {
			$vsettingslink = "<a href='".admin_url('admin.php')."?page=".$vautomedicslug."'>".__('Settings','wp-automedic')."</a>";
			array_unshift($vlinks, $vsettingslink);
		}
		return $vlinks;
	}
}

// ===============
// Plugin Settings
// ===============

// get Plugin Settings
// -------------------
global $vautomedic; $vautomedic = get_option('wp_automedic');

// get a Plugin Setting
// --------------------
function automedic_get_option($vkey,$vfilter=true) {
	global $vautomedic;
	$vkey = str_replace('am_','',$vkey);
	if (isset($vautomedic[$vkey])) {
		if ($vfilter) {return apply_filters('automedic_'.$vkey,$vvalue);}
		else {return $vautomedic[$vkey];}
	} else {
		// 1.3.5: use default fallbacks
		if ($vkey == 'images') {return automedic_get_defaults('images');}
		if ($vkey == 'styles') {return automedic_get_defaults('styles');}
		return '';
	}
}

// get Defaults Settings
// ---------------------
// 1.4.0: set keyed default options
function automedic_get_defaults($vtype) {
	// keys: 'context', 'delay', 'cycle', 'attempts', 'external', 'cache', 'debug'
	if ($vtype == 'images') {
		// return 'both,5,30,2,1,1,0';
		return array('reload' => '1', 'context' => 'both', 'delay' => 5, 'cycle' => '30',
		'attempts' => 2, 'external' => 1, 'cache' => 1, 'debug' => 0);
	}
	if ($vtype == 'styles') {
		// return 'both,2,20,3,1,1,0';
		return array('reload' => '1', 'context' => 'both', 'delay' => 2, 'cycle' => '20',
		'attempts' => 3, 'external' => 1, 'cache' => 1, 'debug' => 0);
	}
}

// transfer Old Settings
// ---------------------
// 1.4.0: transfer to global plugin option
if ( (!$vautomedic) && (get_option('automedic_images')) ) {

	$vautomedic['switch'] = get_option('automedic_switch'); delete_option('automedic_switch');
	$vautomedic['selfcheck'] = get_option('automedic_selfcheck'); delete_option('automedic_selfcheck');

	$vtemp['images'] = explode(',',get_option('automedic_images')); delete_option('automedic_images');
	$vtemp['styles'] = explode(',',get_option('automedic_stylesheets')); delete_option('automedic_stylesheets');
	delete_option('automedic_scripts'); delete_option('automedic_iframes'); delete_option('automedic_embeds');

	foreach ($vtemp as $vkey => $vsettings) {
		$vautomedic[$vkey] = array('reload' => $vsettings[0], 'context' => $vsettings[1],
			'delay' => $vsettings[2], 'cycle' => $vsettings[3], 'attempts' => $vsettings[4],
			'external' => $vsettings[5], 'cache' => $vsettings[6], 'debug' => $vsettings[7]);
	}
	update_option('wp_automedic', $vautomedic);
}

// add Default Options
// -------------------
register_activation_hook(__FILE__,'automedic_add_default_settings');
function automedic_add_default_settings() {

	// 1.4.0: use global plugin option
	global $vautomedic;

	$vautomedic['switch'] = '1';
	$vautomedic['selfcheck'] = '1';
	$vautomedic['images'] = automedic_get_defaults('images');
	$vautomedic['styles'] = automedic_get_defaults('styles');
	// 1.4.0: add save time for reloader script cachebusting
	$vautomedic['savetime'] = time();
	add_option('wp_automedic',$vautomedic);

	if (file_exists(dirname(__FILE__).'/updatechecker.php')) {$vadsboxoff = '';} else {$vadsboxoff = 'checked';}
	$sidebaroptions = array('adsboxoff'=>$vadsboxoff,'donationboxoff'=>'','reportboxoff'=>'','installdate'=>date('Y-m-d'));
	add_option('am_sidebar_options',$sidebaroptions);

}
// 1.4.0: maybe trigger pro version options
if (function_exists('automedic_pro_add_default_settings')) {
	register_activation_hook(__FILE__,'automedic_pro_add_default_settings');
}

// Update Options Trigger
// ----------------------
if ( (isset($_POST['am_save_options'])) && ($_POST['am_save_options'] == 'yes') ) {
	add_action('init','automedic_update_options');
}

// Update Options
// --------------
function automedic_update_options() {

	if (!current_user_can('manage_options')) {exit;}

	// 1.4.0: check nonce value
	check_admin_referer('wp-automedic');
	// 1.4.0: use global option value
	global $vautomedic;

	if (isset($_POST['am_automedic_switch'])) {
		$vautomedicswitch = $_POST['am_automedic_switch'];
		if ($vautomedicswitch != '1') {$vautomedicswitch = '0';}
	} else {$vautomedicswitch = '0';}
	$vautomedic['switch'] = $vautomedicswitch;

	if (isset($_POST['am_automedic_selfcheck'])) {
		$vautomedicselfcheck = $_POST['am_automedic_selfcheck'];
		if ($vautomedicselfcheck != '1') {$vautomedicselfcheck = '0';}
	} else {$vautomedicselfcheck = '0';}
	$vautomedic['selfcheck'] = $vautomedicselfcheck;

	$vprevious['images'] = $vautomedic['images'];
	$vprevious['styles'] = $vautomedic['styles'];
	$vdefaults['images'] = automedic_get_defaults('images');
	$vdefaults['styles'] = automedic_get_defaults('styles');

	$vtypes = array('images','styles');
	$vkeys = array('reload','delay','cycle','attempts','external','cache','debug');
	$vinputs = array('delay','cycle','attempts');
	$vcheckboxes = array('external','cache','debug');
	$vscopes = array('frontend','backend','both','off');

	foreach ($vtypes as $vtype) {
		foreach ($vkeys as $vkey) {
			$vpostkey = 'am_'.$vtype.'_'.$vkey;
			if (isset($_POST[$vpostkey])) {$vvalue = $_POST[$vpostkey];} else {$vvalue = '';}
			$debugposted[$vtype][$vpostkey] = $vvalue;

			if (in_array($vkey,$vinputs)) {
				if (!is_numeric($vvalue)) {$vvalue = $vprevious[$vtype][$vi];}
				if (!is_numeric($vvalue)) {$vvalue = $vdefault[$vtype][$vi];}
			}
			elseif (in_array($vkey,$vcheckboxes)) {
				if ($vvalue == '') {$vvalue = '0';}
			}
			elseif ($vkey == 'reload') {
				// 1.4.0: validate exact scope options
				if (!in_array($vvalue,$vscopes)) {$vvalue = 'off';}
			}
			$vautomedic[$vtype][$vkey] = $vvalue;
		}
	}

	// 1.4.0: add savetime for reloader script cachebusting
	$vautomedic['savetime'] = time();

	// ob_start();
	// echo "Posted Values: "; print_r($vdebugposted); echo PHP_EOL;
	// echo "Validated Values: "; print_r($vautomedic);
	// $debug = ob_get_contents(); ob_end_clean();

	update_option('wp_automedic',$vautomedic);

	// 1.4.0: maybe update pro options also
	if (function_exists('automedic_pro_update_options')) {automedic_pro_update_options();}
}

// Settings Page
// -------------
function automedic_settings_page() {

	// TODO: Special Options
	// Import External Stylesheets? (automedic_import_external_styles)

	// 1.4.0: use global plugin option
	global $vautomedic, $vautomedicversion;

	$vimages = $vautomedic['images'];
	$vstyles = $vautomedic['styles'];

	// maybe reset to defaults
	if (!is_array($vimages)) {$vautomedic['images'] = $vimages = automedic_get_defaults('image');}
	if (!is_array($vstyles)) {$vautomedic['styles'] = $vstyles = automedic_get_defaults('style');}

	// 1.4.0: added pagewrap styles
	echo '<div id="pagewrap" class="wrap" style="width:100%;margin-right:0px !important;">';

	// Sidebar Floatbox
	// ----------------
	// $vargs = array('am','wp-automedic','free','wp-automedic','','WP AutoMedic',$vautomedicversion);
	$vargs = array('wp-automedic','yes'); // trimmed settings
	if (function_exists('wqhelper_sidebar_floatbox')) {
		wqhelper_sidebar_floatbox($vargs);

		// 1.4.0: replace floatmenu with stickykit
		echo wqhelper_sidebar_stickykitscript();
		echo '<style>#floatdiv {float:right;}</style>';
		echo '<script>jQuery("#floatdiv").stick_in_parent();
		wrapwidth = jQuery("#pagewrap").width(); sidebarwidth = jQuery("#floatdiv").width();
		newwidth = wrapwidth - sidebarwidth;
		jQuery("#wrapbox").css("width",newwidth+"px");
		jQuery("#adminnoticebox").css("width",newwidth+"px");
		</script>';

		// $vfloatmenuscript = wqhelper_sidebar_floatmenuscript(); echo $vfloatmenuscript;
		// echo '<script language="javascript" type="text/javascript">
		// floatingMenu.add("floatdiv", {targetRight: 10, targetTop: 20, centerX: false, centerY: false});
		// function move_upper_right() {
		//	floatingArray[0].targetTop=20;
		//	floatingArray[0].targetBottom=undefined;
		//	floatingArray[0].targetLeft=undefined;
		//	floatingArray[0].targetRight=10;
		//	floatingArray[0].centerX=undefined;
		//	floatingArray[0].centerY=undefined;
		// }
		// move_upper_right();</script>';
	}

	// Admin Notices Boxer
	// -------------------
	if (function_exists('wqhelper_admin_notice_boxer')) {wqhelper_admin_notice_boxer();}

	// Plugin Admin Header
	// -------------------
	$viconurl = plugins_url('images/wp-automedic.png',__FILE__);
	echo "<table><tr><td><img src='".$viconurl."'></td>";
	echo "<td width='20'></td><td>";
		echo "<table><tr><td><h2>WP AutoMedic</h2></td><td width='20'></td>";
		echo "<td><h3>v".$vautomedicversion."</h3></td></tr>";
		echo "<tr><td colspan='3' align='center'>".__('by','wp-automedic');
		echo " <a href='http://wordquest.org/' style='text-decoration:none;' target=_blank><b>WordQuest Alliance</b></a>";
		echo "</td></tr></table>";
	echo "</td><td width='50'></td>";
	// 1.4.0: added welcome message
	if ( (isset($_REQUEST['welcome'])) && ($_REQUEST['welcome'] == 'true') ) {
		echo "<td><table style='background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;'>";
		echo "<tr><td><div class='message' style='margin:0.25em;'><font style='font-weight:bold;'>";
		echo __('Welcome! For usage see','wp-automedic')." <i>readme.txt</i> FAQ</font></div></td></tr></table></td>";
	}
	if ( (isset($_REQUEST['updated'])) && ($_REQUEST['updated'] == 'yes') ) {
		echo "<td><table style='background-color: lightYellow; border-style:solid; border-width:1px; border-color: #E6DB55; text-align:center;'>";
		echo "<tr><td><div class='message' style='margin:0.25em;'><font style='font-weight:bold;'>";
		echo __('Settings Updated.','wp-automedic')."</font></div></td></tr></table></td>";
	}
	echo "</tr></table><br>";

	// Start Form
	// ----------
	echo "<form method='post'>";
	echo "<input type='hidden' name='am_save_options' value='yes'>";
	// 1.4.0: added nonce field(s)
	wp_nonce_field('wp-automedic');

	// 1.4.0: added wrap box
	echo "<div id='wrapbox' class='postbox' style='width:680px;line-height:2em;'><div class='inner' style='padding-left:20px;'>";

	// Main Switches
	// -------------
	echo "<table><tr><td><b>".__('Enable WP AutoMedic?','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><input name='am_automedic_switch' type='checkbox' value='1'";
	if ($vautomedic['switch'] == '1') {echo " checked";}
	echo ">";
	echo "<td colspan='7' align='right'>".__('Do self-load check?','wp-automedic')."</td>";
	echo "<td align='center'><input name='am_automedic_selfcheck' type='checkbox' value='1'";
	if ($vautomedic['selfcheck'] == '1') {echo " checked";}
	echo "></td></tr>";
	echo "<tr height='20'><td></td></tr>";

	// Table Headers
	// -------------
	echo "<tr><td>".__('Resource Type','wp-automedic')."</td><td width='10'></td>";
	echo "<td align='center'><b>".__('Context','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Delay','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Cycle','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Attempts','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Externals','wp-automedic')."</b></td><td width='10'></td>";
	// echo "<td align='center'><b>".__('Cache','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><b>".__('Debug','wp-automedic')."</b></td></tr>";

	// Images
	// ------
	echo "<tr><td><b>".__('Image Reloader','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><select name='am_images_reload'>";
	echo "<option value='frontend' name='Frontend'";
	if ($vimages['reload'] == 'frontend') {echo " selected='selected'";}
	echo ">".__('Frontend','wp-automedic')."</option>";
	echo "<option value='admin'";
	if ($vimages['reload'] == 'admin') {echo " selected='selected'";}
	echo ">".__('Admin','wp-automedic')."</option>";
	echo "<option value='both'";
	if ($vimages['reload'] == 'both') {echo " selected='selected'";}
	echo ">".__('Both','wp-automedic')."</option>";
	echo "<option value='off'";
	if ($vimages['reload'] == 'off') {echo " selected='selected'";}
	echo ">".__('Off','wp-automedic')."</option>";
	echo "</select></td><td width='10'></td>";
	// inputs
	echo "<td align='center'><input name='am_images_delay' type='text' style='width:30px;' value='".$vimages['delay']."'>s</td><td width='10'></td>";
	echo "<td align='center'><input name='am_images_cycle' type='text' style='width:30px;' value='".$vimages['cycle']."'>s</td><td width='10'></td>";
	echo "<td align='center'><input name='am_images_attempts' type='text' style='width:30px;' value='".$vimages['attempts']."'></td><td width='10'></td>";

	// checkboxes
	echo "<td align='center'><input name='am_images_external' type='checkbox' value='1'";
	if ($vimages['external'] == '1') {echo " checked";}
	echo "></td><td width='10'></td>";

	echo "<input name='am_image_cache' type='hidden' value='0'>";
	// echo "<td align='center'>dev";
	// echo "<input name='am_images_cache' type='checkbox' value='1'";
	// if ($vimages['cache'] == '1') {echo " checked";}
	// echo ">";
	// echo "</td><td width='10'></td>";

	echo "<td align='center'><input name='am_images_debug' type='checkbox' value='1'";
	if ($vimages['debug'] == '1') {echo " checked";}
	echo "></td></tr>";

	// Stylesheets
	// -----------
	echo "<tr><td><b>".__('Stylesheet Reloader','wp-automedic')."</b></td><td width='10'></td>";
	echo "<td align='center'><select name='am_styles_reload'>";
	echo "<option value='frontend'";
	if ($vstyles['reload'] == 'frontend') {echo " selected='selected'";}
	echo ">".__('Frontend','wp-automedic')."</option>";
	echo "<option value='admin'";
	if ($vstyles['reload'] == 'admin') {echo " selected='selected'";}
	echo ">".__('Admin','wp-automedic')."</option>";
	echo "<option value='both'";
	if ($vstyles['reload'] == 'both') {echo " selected='selected'";}
	echo ">".__('Both','wp-automedic')."</option>";
	echo "<option value='off'";
	if ($vstyles['reload'] == 'off') {echo " selected='selected'";}
	echo ">".__('Off','wp-automedic')."</option>";
	echo "</select></td><td width='10'></td>";
	// inputs
	echo "<td align='center'><input name='am_styles_delay' type='text' style='width:30px;' value='".$vstyles['delay']."'>s</td><td width='10'></td>";
	echo "<td align='center'><input name='am_styles_cycle' type='text' style='width:30px;' value='".$vstyles['cycle']."'>s</td><td width='10'></td>";
	echo "<td align='center'><input name='am_styles_attempts' type='text' style='width:30px;' value='".$vstyles['attempts']."'></td><td width='10'></td>";
	// checkboxes
	echo "<td align='center'><input name='am_styles_external' type='checkbox' value='1'";
	if ($vstyles['external'] == '1') {echo " checked";}
	echo "></td><td width='10'></td>";

	echo "<input name='am_styles_cache' type='hidden' value='0'>";
	// echo "<td align='center'>dev";
	// echo "<input name='am_style_cache' type='checkbox' value='1'";
	// if ($vstyles['cache'] == '1') {echo " checked";}
	// echo ">";
	// echo "</td><td width='10'></td>";

	echo "<td align='center'><input name='am_styles_debug' type='checkbox' value='1'";
	if ($vstyles['debug'] == '1') {echo " checked";}
	echo "></td></tr>";

	// 1.4.0: maybe output pro settings inputs
	if (function_exists('automedic_pro_settings_page')) {automedic_pro_settings_page();}

	echo "<tr height='20'><td> </td></tr>";
	echo "<tr><td colspan='15' align='center'>";

	// TODO: Restore Default Settings Button?
	// echo "<td colspan='7'>";
	// echo "<input class='button-secondary' type='reset' value='".__('Reset Options','wp-automedic')."'>";
	// echo "</td><td></td>";
	// echo "<td colspan='7'>";

	echo "<input class='button-primary' type='submit' value='".__('Update Options','wp-automedic')."'>";
	echo "</td></tr>";

	echo "</table></form><br>";

	echo '</div></div>'; // close #wrapbox

	echo '</div>'; // close #pagewrap
}


// ===============
// Enqueue Scripts
// ===============

add_action('wp_enqueue_scripts','automedic_enqueue_script');
// 1.4.0: also add action to admin_enqueue_scripts
add_action('admin_enqueue_scripts','automedic_enqueue_script');

// Enqueue Reloader Script
// -----------------------
function automedic_enqueue_script() {

	global $vautomedic;
	if (!$vautomedic['switch']) {return;}

	// get standard options
	$vimages = automedic_get_option('images');
	$vstyles = automedic_get_option('styles');

	// $vdeps = array('jquery'); // jquery dependency not needed
	$vdeps = array(); // javascript only is needed

	// set version as current time to avoid caching
	// 1.4.0: set to settings save time for efficiency
	$vver = automedic_get_option('savetime');
	if (!$vver) {$vver = time();}

	// 1.4.0: default global script URL to false
	global $vautomedicscript; $vautomedicscript = false;

	// 1.4.0: moved debug switches to variable output
	// 1.4.0: moved load variable printing to script loader tag
	// 1.4.0: check the context for image and stylesheet reloading
	if (is_admin()) {
		if ( ($vimages['reload'] == 'admin') || ($vimages['reload'] == 'both') ||
			 ($vstyles['reload'] == 'admin') || ($vstyles['reload'] == 'both') ) {
			$vautomedicscript = plugins_url('automedic.js',__FILE__);
		}
	} else {
		if ( ($vimages['reload'] == 'frontend') || ($vimages['reload'] == 'both') ||
			 ($vstyles['reload'] == 'frontend') || ($vstyles['reload'] == 'both') ) {
			$vautomedicscript = plugins_url('automedic.js',__FILE__);
		}
	}

	// 1.4.0: maybe just enqueue the reloader javascript now
	if ($vautomedicscript) {wp_enqueue_script('automedic-reloader', $vautomedicscript, $vdeps, $vver);}

}


// ========================================
// AutoMedic Reloader and Cycler Javascript
// ========================================

function automedic_script_variables() {

	if (is_admin()) {$vcontext = 'admin';} else {$vcontext = 'frontend';}
	$vimages = automedic_get_option('images');
	$vstyles = automedic_get_option('styles');

	// set console logging debug switches
	// 1.4.0: simplified debug switch overrides
	if ( (isset($_REQUEST['imagedebug'])) && ($_REQUEST['imagedebug'] == '1') ) {$vimages['debug'] = '1';}
	if ( (isset($_REQUEST['styledebug'])) && ($_REQUEST['styledebug'] == '1') ) {$vstyles['debug'] = '1';}

	// Start Javascript
	// ----------------
	global $vautomedicversion;
	echo "/* WP AutoMedic ".$vautomedicversion." ";
	echo " - http://wordquest.org/plugins/wp-automedic/ */".PHP_EOL;

	// Set Site URL for external checks
	// --------------------------------
	$vsitehost = $_SERVER['HTTP_HOST'];
	echo "var amSiteHost = '".$vsitehost."'; ";

	// Set Admin AJAX URL
	// ------------------
	$vadminajaxurl = admin_url('admin-ajax.php');
	echo "var amAjaxUrl = '".$vadminajaxurl."'; ";

	// Output Config Variables
	// -----------------------
	if ( ($vimages['reload'] == 'both')
	  || ( ($vcontext == 'admin') && ($vimages['reload'] == 'admin') )
	  || ( ($vcontext == 'frontend') && ($vimages['reload'] == 'frontend') ) ) {

		$vimagecycling = '0';
		if ( ($vimages['cycle'] > 0) && ($vimages['attempts'] > 0) ) {$vimagecycling = '1';}

		echo " var amImageReload = '1';";
		echo " var amImageCycling = '".$vimagecycling."';";
		echo " var amImageCycles = '".$vimages['cycle']."';";
		echo " var amImageDelay = '".$vimages['delay']."';";
		echo " var amImageAttempts = '".$vimages['attempts']."';";
		echo " var amImageExternal = '".$vimages['external']."';";
		echo " var amImageCache = '".$vimages['cache']."';";
		echo " var amImageDebug = '".$vimages['debug']."';";
	} else {echo " var amImageReload = '0';";}

	if ( ($vstyles['reload'] == 'both')
	  || ( ($vcontext == 'admin') && ($vstyles['reload'] == 'admin') )
	  || ( ($vcontext == 'frontend') && ($vstyles['reload'] == 'frontend') ) ) {

		$vstylecycling = '0';
		if ( ($vstyles['cycle'] > 0) && ($vstyles['attempts'] > 0) ) {$vstylecycling = '1';}

		echo " var amStyleReload = '1';";
		echo " var amStyleCycling = '".$vstylecycling."';";
		echo " var amStyleCycles = '".$vstyles['cycle']."';";
		echo " var amStyleDelay = '".$vstyles['delay']."';";
		echo " var amStyleAttempts = '".$vstyles['attempts']."';";
		echo " var amStyleExternal = '".$vstyles['external']."';";
		echo " var amStyleCache = '".$vstyles['cache']."';";
		echo " var amStyleDebug = '".$vstyles['debug']."';";
	}  else {echo " var amStyleReload = '0';";}

	// 1.4.0: maybe output javascript variable for pro version
	if (function_exists('automedic_pro_script_variables')) {automedic_pro_script_variables($vcontext);}

}

// AJAX Call for AutoMedic Script
// ------------------------------
// 1.4.0: [deprecated] AJAX load method
// add_action('wp_ajax_am_reloader', 'automedic_reload_script');
// add_action('wp_ajax_nopriv_am_reloader', 'automedic_reload_script');

// Output Reloader Script
// ----------------------
// 1.4.0: [deprecated] PHP to JS file method
// function automedic_reload_script() {
//	$vreloaderjs = dirname(__FILE__).'/automedic-reloader.js.php';
//	require($vreloaderjs); exit;
// }

// ---------------
// Self Load Check
// ---------------
if ( ($vautomedic['switch']) && ($vautomedic['selfcheck']) ) {

	add_action('wp_footer','automedic_self_load_check',99);
	// 1.4.0: add admin_footer action for backend self check
	add_action('admin_footer','automedic_self_load_check',99);

	function automedic_self_load_check() {

		// 1.4.0: check/use enqueued script URL global
		global $vautomedicscript;
		if (!$vautomedicscript) {return;}

		// 1.4.0: manually append time for cachebusting
		$vautomedicscript .= '&version='.time();

		// so much simpler to check than for dynamic scripts,
		// as we actually know a function name to test for..!
		echo "<script>if (typeof amCacheBust != 'function') {
			ams = document.createElement('script');
			ams.src = '".$vautomedicscript."';
			document.body.appendChild(ams);
		}</script>";
	}
}


// ================
// Wordpress Styles
// ================

// Process Wordpress Style Tags
// ----------------------------
// 1.4.0: convert to init action
add_action('init', 'automedic_check_style_reload');
function automedic_check_style_reload() {

	// 1.4.0: remove unneeded global flag
	$vstyles = automedic_get_option('styles');
	$vreloadstyles = $vstyles['reload'];

	if ( ($vstyles['reload'] == 'both')
	  || ( ($vstyles['reload'] == 'frontend') && (!is_admin()) )
	  || ( ($vstyles['reload'] == 'admin') && (is_admin()) ) ) {
		add_filter('style_loader_tag','automedic_process_style_tags', 11, 2);
	}

}

// Process All WordPress Style Tags
// --------------------------------
function automedic_process_style_tags($vlink,$vhandle) {

	global $vautomedicstylenum;

	// do a filter check using the style handle
	// (returning false for a handle will skip this tag)
	$vdoautomedic = apply_filters('automedic_style_check',$vhandle);
	if (!$vdoautomedic) {return $vlink;}

	$vstyle[$vautomedicstylenum] = $vlink;
	$vstylekeys = automedic_extract_tag_attributes($vstyle,'style');

	// skip tag if extraction failed
	if (!is_array($vstylekeys)) {return $vlink;}

	// skip if not to process or already processed
	if ( (isset($vstylekeys[$vautomedicstylenum]['noautomedic']))
	  || (isset($vstylekeys[$vautomedicstylenum]['automedicated'])) ) {return $vlink;}

	// skip tag if no href value found
	if (!isset($vstylekeys[$vautomedicstylenum]['href'])) {return $vlink;}
	if ($vstylekeys[$vautomedicstylenum]['href'] == '') {return $vlink;}
	$vstylehref = $vstylekeys[$vautomedicstylenum]['href'];

	// check tag for external stylesheet
	$vexternal = false;
	if ( (!stristr($vstylehref,$_SERVER['HTTP_HOST']))
	  && ( (stristr($vstylehref,'http:')) || (stristr($vstylehref,'https:')) ) ) {
	  	$visexternal = true;
	}

	// skip if not to reload external stylesheets
	// 1.4.0: use keyed external option for stylesheets
	$vstyles = automedic_get_option('styles');
	if ($visexternal && (!$vstyles['external']) ) {return $vlink;}

	// special: maybe convert external stylesheet href to internal @import
	$vimportexternal = get_option('automedic_import_external_styles');

	if ($vimportexternal && $visexternal) {
		$vnulink = automedic_rebuild_style_tag($vstylekeys,'style',$vautomedicstylenum,true);
	} else {
		$vnulink = automedic_rebuild_style_tag($vstylekeys,'style',$vautomedicstylenum);
	}

	$vautomedicstylenum++;

	// TODO: revalidate the final tag again just in case?
	// print_r($vnulink); // debug point

	return $vnulink;
}



// ========================
// Tag Processing Functions
// ========================

// Extract Attribute Keys from Tag
// -------------------------------
function automedic_extract_tag_attributes($tagelements,$tag) {

	$vi = 0;
	foreach ($tagelements as $element) {
		$element = str_ireplace('</'.$tag.'>','',$element); // '>
		$element = str_ireplace('<'.$tag,'',$element); // '>
		$element = trim($element);
		if (substr($element,-1) == '>') {$element = substr($element,0,strlen($element)-1);}

		// if has quotes, replace spaces inside them
		if ( (strstr($element,'"')) || (strstr($element,"'")) ) {
			// replace spaces inside double quotes with a placeholder
			$tempelement = $element;
			if (strstr($tempelement,'"')) {
				while (strstr($tempelement,'"')) {
					$pos = strpos($tempelement,'"') + 1;
					$chunks = str_split($tempelement,$pos);
					unset($chunks[0]);
					$temp = implode('',$chunks);
					if (strstr($temp,'"')) {
						$pos = strpos($temp,'"');
						$chunks = str_split($temp,$pos);
						$inside = $chunks[0];
						$nuinside = str_replace(' ','|---|',$inside);
						// another important fix, replace = inside quotes!
						$nuinside = str_replace('=','|-|-|',$nuinside);
						$element = str_replace('"'.$inside.'"','"'.$nuinside.'"',$element);
						$tempelement = str_replace('"'.$inside.'"','',$tempelement);
					} else {continue;} // bug out for unclosed quotes
					// TODO: better fix for unclosed quotes?
					// ...but this is above and beyond the call of duty
				}
			}
			// replace spaces inside single quotes with a placeholder
			$tempelement = $element;
			if (strstr($tempelement,"'")) {
				while (strstr($tempelement,"'")) {
					$pos = strpos($tempelement,"'") + 1;
					$chunks = str_split($tempelement,$pos);
					unset($chunks[0]);
					$temp = implode('',$chunks);
					if (strstr($temp,"'")) {
						$pos = strpos($temp,"'");
						$chunks = str_split($temp,$pos);
						$inside = $chunks[0];
						$nuinside = str_replace(' ','|---|',$inside);
						// another important fix, replace = inside quotes!
						$nuinside = str_replace('=','|-|-|',$nuinside);
						$element = str_replace("'".$inside."'","'".$nuinside."'",$element);
						$tempelement = str_replace("'".$inside."'","",$tempelement);
					} else {continue;} // bug out for unclosed quotes
					// TODO: better fix for unclosed quotes?
					// (this is above and beyond the call of duty tho)
				}
			}
		}

		// replace all other spaces not in quotes
		$element = str_replace(" ","|||",$element);

		// print_r($element); // debug point

		// split the tag string at our replaced spaces
		if (strstr($element,'|||')) {$chunks = explode('|||',$element);}
			else {$chunks[0] = $element;}

		// print_r($chunks); // debug point

		foreach ($chunks as $chunk) {

			if (strstr($chunk,'=')) {
				$parts = explode('=',$chunk);
				$thiskey = trim($parts[0]);

				$thisvalue = trim($parts[1]);
				if ( (substr($thisvalue,0,1) == '"') && (substr($thisvalue,-1) == '"') ) {
					$thisvalue = substr($thisvalue,1,strlen($vthisvalue)-1);
					// put back the spaces and = inside quotes
					$thisvalue = str_replace('|---|',' ',$thisvalue);
					$thisvalue = str_replace('|-|-|','=',$thisvalue);
				}
				elseif ( (substr($thisvalue,0,1) == "'") && (substr($thisvalue,-1) == "'") ) {
					$thisvalue = substr($thisvalue,1,strlen($vthisvalue)-1);
					// put back the spaces and = inside quotes
					$thisvalue = str_replace('|---|',' ',$thisvalue);
					$thisvalue = str_replace('|-|-|','=',$thisvalue);
				}
				// special: fix case variations of to specific case version for later
				if (strtolower($thiskey) == 'src') {$thiskey = 'src';}
				if (strtolower($thiskey) == 'onload') {$thiskey = 'onLoad';}
				if (strtolower($thiskey) == 'onerror') {$thiskey = 'onError';}
				if (strtolower($thiskey) == 'onreadystatechange') {$thiskey = 'onReadyStateChange';}
				$tagkeys[$vi][$thiskey] = $thisvalue;
			} else {
				// single attribute no value, and ignore final /
				if ($chunk != '/') {$tagkeys[$vi][$chunk] = '';}
			}
		}
		$vi++;
	}
	return $tagkeys;
}

// Rebuild Style Tag from Extracted Keys
// -------------------------------------
// 1.4.0: separate stylesheet tag rebuild function
function automedic_rebuild_style_tag($tagkeys,$tag,$vnum,$special=false) {

	$nutag = '<'.$tag; 	// '>
	foreach ($tagkeys[$vnum] as $key => $value) {
		if ($value == '') {$nutag .= ' '.$key;}
		elseif ( (stristr($key,'href')) && ($special) ) {
			// for importing external stylesheets
			$href = $value;
		} else {
			$useouterquote = '"';
			// if (strstr($value,"'")) {$useouterquote = '"';}
			if (strstr($value,'"')) {$useouterquote = "'";}
			$nutag .= ' '.$key.'='.$useouterquote.$value.$useouterquote;
		}
	}

	$nutag .= ' automedicated'; // add the automedicated attribute
	$nutag .= ' />';
	if ($special) {$nutag .= '@import("'.$href.'");';}
	$nutag .= '</'.$tag.'>';
	return $nutag;
}


// --------------------------
// AutoMedic Test Page Output
// --------------------------
add_shortcode('automedic-test','automedic_test_html_shortcode_output');

function automedic_test_html_shortcode_output() {

	// $output = '<!doctype html>';

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

<br>'; // end HTML;

	return $output;
}

?>