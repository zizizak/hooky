<?php

/**
 * This file adds help functions for getting information about hooks from database.
 */

/**
 * Returns all hooks from database.
 * @return array     Array of hooks.
 */
function hooky_get_hooks(){
  global $wpdb;
  $hooks_table = $wpdb->prefix . 'hooky_hooks';
  $sql = "SELECT * from $hooks_table";
  return $wpdb->get_results($sql, OBJECT);
}

/**
 * Returns all public post types.
 * @return array     Public post types.
 */
function hooky_get_types(){
  return get_post_types(['public' => 'ture']);
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
