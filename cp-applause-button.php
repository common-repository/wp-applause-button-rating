<?php
/*
 * Plugin Name: WP APPLAUSE BUTTON & RATING PLUGIN
 * Description: Add an applause button to your website and let users like your content by clapping. Simply paste this shortcode where you want the applause button to appear: [cp-applause-button]
 * Author: WEBSEITENHELD.DE
 * Author URI: https://webseitenheld.de
 * Plugin URI: https://webseitenheld.de/plugin
 * Version: 1.0.3
 */

define('CP_Applause_Button_URL',plugins_url('',__FILE__));

require_once dirname(__FILE__) . '/includes/class-cp-applause-core.php';