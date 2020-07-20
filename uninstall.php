<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('csp_manager_admin');
delete_option('csp_manager_loggedin');
delete_option('csp_manager_frontend');
