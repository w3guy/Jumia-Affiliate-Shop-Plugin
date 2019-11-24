<?php
//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( 'ju_settings_data' );

/**
 * @todo delete cron when uninstalled.
 */