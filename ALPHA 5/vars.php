<?php
global $wpdb;

$BWPS_limitlogin_table_hostfailstable = $wpdb->prefix . "BWPS_bad_host_attempts";
$BWPS_limitlogin_table_userfailstable = $wpdb->prefix . "BWPS_bad_user_attempts";
$BWPS_limitlogin_table_lockouthosttable = $wpdb->prefix . "BWPS_lockouthost";
$BWPS_limitlogin_table_lockoutusertable = $wpdb->prefix . "BWPS_lockoutuser";

$BWPS_currentVersion = 'alpha4'; //Define current version variable