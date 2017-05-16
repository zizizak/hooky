<?php

function get_hooks(){
  global $wpdb;
  $hooks_table = $wpdb->prefix . 'hookah_hooks';
  $sql = "SELECT * from $hooks_table";
  return $wpdb->get_results($sql, OBJECT);
}

function get_hooks_by_type($type){
  global $wpdb;
  $hooks_table = $wpdb->prefix . 'hookah_hooks';
  $sql = "SELECT action, transform, authmethod, authtoken, endpoint from $hooks_table WHERE `type` LIKE '$type'";
  return $wpdb->get_results($sql, OBJECT);
}
