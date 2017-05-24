<?php

/**
 * This file adds help functions for getting information about hooks from database.
 */

/**
 * Returns all hooks from database.
 * @return array     Array of hooks.
 */
function get_hooks(){
  global $wpdb;
  $hooks_table = $wpdb->prefix . 'hooky_hooks';
  $sql = "SELECT * from $hooks_table";
  return $wpdb->get_results($sql, OBJECT);
}

/**
 * Returns the hooks associated with a particular post type.
 * @param  string    $type Post type alias.
 * @return array     Array of hooks.
 */
function get_hooks_by_type($type){
  global $wpdb;
  $hooks_table = $wpdb->prefix . 'hooky_hooks';
  $sql = "SELECT * from $hooks_table WHERE `type` LIKE '$type'";
  return $wpdb->get_results($sql, OBJECT);
}
