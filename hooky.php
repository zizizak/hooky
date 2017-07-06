<?php

/**
 * Plugin Name: Hooky
 * Author: Ethan Butler
 *
 * TODO: License and contact info
 */

global $wpdb;
define('HOOKY_TABLE', $wpdb->prefix . 'hooky_hooks');
define('HOOKY_NAMESPACE', 'hooky/v1');
define('HOOKY_SESSION_ERR', 'hooky_errs');
define('HOOKY_SESSION_SUCCESS', 'hooky_succeess');

require_once('inc/hooky-public.php');
require_once('inc/hooky-hooks.php');
require_once('inc/hooky-controller.php');

class Hooky {
  function __construct(){
    if(!session_id()) session_start();

    add_action('admin_enqueue_scripts', array($this, 'hooky_admin_scripts'));
    add_action('admin_menu',            array($this, 'hooky_options_page'));
    add_action('admin_notices',         array($this, 'hooky_admin_notices'));
    add_action('rest_api_init',         array($this, 'hooky_rest_api'));
    add_action('save_post',             array($this, 'hooky_save'), 10, 3);

    register_activation_hook(__FILE__, array($this, 'hooky_db'));

    require_once('inc/hooky-callbacks.php');
    require_once('inc/hooky-send.php');
  }

  /**
   * Adds options page to dashboard
   * @return void
   */
  function hooky_options_page(){
    include_once('inc/hooky-options.php');
  }

  /**
   * Enqueues JS needed by plugin and adds data via wp_localize_script
   * @param  String $hook WordPress admin hook
   * @return void
   */
  function hooky_admin_scripts($hook){
    if($hook != 'settings_page_hooky') return;
    wp_enqueue_script('hooky/js', plugins_url('assets/dist/hooky.min.js', __FILE__), [], null, true);

    global $hooky_filters, $hooky_endpoint_filters, $hooky_success_callbacks;
    wp_localize_script('hooky/js', 'hooky_filters', $hooky_filters);
    wp_localize_script('hooky/js', 'hooky_endpoint_filters', $hooky_endpoint_filters);
    wp_localize_script('hooky/js', 'hooky_success_callbacks', $hooky_success_callbacks);
    wp_localize_script('hooky/js', 'site_url', site_url() . '/wp-json/' . HOOKY_NAMESPACE);
    wp_localize_script('hooky/js', 'hooky_hooks', hooky_get_hooks());
    wp_localize_script('hooky/js', 'hooky_types', hooky_get_types());
  }

  /**
   * Register REST endpoints used by plugin
   * @return void
   */
  function hooky_rest_api(){
    require_once('inc/hooky-api.php');
  }

  /**
   * Initialize database
   * @return void
   */
  function hooky_db(){
    require_once('inc/hooky-db.php');
    hooky_db_install();
  }

  /**
   * Creates an instance of HookyController that will fire appropriate callbacks based
   * those that have been registered.
   *
   * Must be called in save_post hook.
   *
   * @param  Int      $id   Post ID.
   * @param  WP_Post  $post Post object.
   * @return void
   *
   * TODO: Add ability to register other actions that can occur during runtime
   * that will work with HookyController.
   */
  function hooky_save($id, $post){
    $controller = new HookyController($id, $post);
  }

  /**
   * Notifies success or failure for post saves.
   */
  function hooky_admin_notices(){
    require_once('inc/hooky-notices.php');
  }

}

$hooky = new Hooky();
