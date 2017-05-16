<?php

global $hookah_db_version;

$hookah_db_version = '1.0';

// TODO: add support for non-json data formats
function hookah_db_install(){
  global $hookah_db_version;
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  $hooks_table = $wpdb->prefix . 'hookah_hooks';
  $hooks_sql   = "CREATE TABLE $hooks_table (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    type text NOT NULL,
    endpoint text NOT NULL,
    action text NOT NULL,
    transform text NOT NULL,
    authmethod text,
    authtoken text,
    PRIMARY KEY  (id)
  ) $charset_collate";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  $db = dbDelta($hooks_sql);
  add_option('hookah_db_version', $hookah_db_version);
}

function hookah_db_install_data(){
  global $wpdb;
  $type       = 'post';
  $endpoint   = 'http://localhost:8100';
  $trigger    = 'CREATE';
  $transform  = 'default';
  $authmethod = 'Basic';
  $authtoken  = 'helloworld';

  $hooks_table = $wpdb->prefix . 'hookah_hooks';
  $wpdb->insert(
    $hooks_table,
    [
      'id'         => NULL,
      'type'       => $type,
      'endpoint'   => $endpoint,
      'action'     => $action,
      'transform'  => $transform,
      'authmethod' => $authmethod,
      'authtoken'  => $authtoken
    ]
  );
  $wpdb->print_error();
}
