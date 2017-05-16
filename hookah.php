<?php

/**
 * Plugin Name: Hookah
 * Author: Ethan Butler
 */

define('HOOKAH_NAMESPACE', 'hookah/v1');

class Hookah {
  function __construct(){
    add_action('admin_menu', array($this, 'hookah_options_page'));
    add_action('admin_enqueue_scripts', array($this, 'hookah_admin_scripts'));
    add_action('rest_api_init', array($this, 'hookah_rest_api'));
    add_action('save_post', array($this, 'hookah_save'), 10, 2);

    // DB initialization
    register_activation_hook(__FILE__, array($this, 'hookah_db'));

    $this->hookah_transform();
    $this->hookah_hooks();
  }

  // Register options page
  function hookah_options_page(){
    include_once('inc/hookah-options.php');
  }

  function hookah_admin_scripts($hook){
    if($hook != 'settings_page_hookah') return;
    wp_enqueue_script('hookah/js', plugins_url('assets/dist/hookah.min.js', __FILE__), [], null, true);

    global $hookah_transforms;
    wp_localize_script('hookah/js', 'hookah_transforms', $hookah_transforms);
    wp_localize_script('hookah/js', 'site_url', site_url() . '/wp-json/' . HOOKAH_NAMESPACE);
    wp_localize_script('hookah/js', 'hookah_hooks', get_hooks());
  }

  // Register endpoints
  function hookah_rest_api(){
    require_once('inc/hookah-api.php');
  }

  // Initialize database
  function hookah_db(){
    require_once('inc/hookah-db.php');
    hookah_db_install();
  }

  function hookah_save($id, $post){
    require_once('inc/hookah-controller.php');
  }

  function hookah_transform(){
    require_once('inc/hookah-transform.php');
  }

  function hookah_hooks(){
    require_once('inc/hookah-hooks.php');
  }
}

$hookah = new Hookah();

hookah_add_transform('just_the_title', ['post'], function($id){
  return ['title' => get_the_title($id)];
});

hookah_add_transform('title_and_author', ['post'], function($id){
  return ['title' => get_the_title($id), 'author' => get_the_author($id)];
});
