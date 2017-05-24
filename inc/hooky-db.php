<?php

// TODO: documentation

global $hooky_db_version;

$hooky_db_version = '1.0';

function hooky_db_install(){
  global $hooky_db_version;
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $hooks_table = $wpdb->prefix . 'hooky_hooks';
  $hooks_sql   = "CREATE TABLE $hooks_table (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    type text NOT NULL,
    endpoint text NOT NULL,
    endpoint_filter text NOT NULL,
    action text NOT NULL,
    filter text NOT NULL,
    success_callback text NOT NULL,
    authmethod text,
    authtoken text,
    PRIMARY KEY  (id)
  ) $charset_collate";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  $db = dbDelta($hooks_sql);
  add_option('hooky_db_version', $hooky_db_version);
}
