<?php

/*
Plugin Name: Jumia Shop
Plugin URI: http://affiliateshop.com.ng/pricing/jumia
Description: Build a Jumia affiliate shop in minutes.
Version: 1.3.1
Author: Collins Agbonghama
Author URI: https://w3guy.com
License: GPL2
*/

define( 'JU_SYSTEM_FILE_PATH', __FILE__ );
define( 'JU_ROOT', plugin_dir_path( __FILE__ ) );
define( 'JU_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'JU_TEMPLATES', JU_ROOT . 'includes/' );
define( 'JU_INCLUDES', JU_ROOT . 'includes' );
define( 'JU_INCLUDES_URL', JU_ROOT_URL . 'includes' );

// EDD ish
define( 'JU_STORE_URL', 'https://affiliateshop.com.ng' );
define( 'JU_ITEM_NAME', 'Jumia Affiliate Shop Plugin' );
define( 'JU_PLUGIN_DEVELOPER', 'Collins Agbonghama' );
define( 'JU_VERSION_NUMBER', '1.3.1' );

require_once JU_ROOT . '/includes/vendor/autoload.php';
require_once JU_ROOT . '/includes/functions.php';

// instantiate settings page class
Jumia\Settings_Page::get_instance();