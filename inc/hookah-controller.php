<?php

class HookahController {

  function __construct($id, $post){
    $this->type = get_post_type($id);
    $hooks = get_hooks_by_type($this->type);

    foreach($hooks as $hook){
      $action = $hook->action;
      switch($action){
        case 'CREATE':
          if($this->is_created($post)) $this->handle_send($hook, $id, $post);
          break;
        case 'UPDATE':
          if($this->is_updated($post)) $this->handle_send($hook, $id, $post);
          break;
        case 'DELETE':
          if($this->is_deleted($post)) $this->handle_send($hook, $id, $post);
          break;
      }
    }
  }

  // TODO: figure out why this is inconsistent
  function is_created($post){
    if($post->post_status == 'auto-draft' || wp_is_post_revision($post_ID)) return false;
    return $post->post_status === 'publish' && ($post->post_modified_gmt === $post->post_date_gmt);
  }

  function is_updated($post){
    if($post->post_status == 'auto-draft' || wp_is_post_revision($post_ID)) return false;
    return $post->post_status === 'publish' && ($post->post_modified_gmt !== $post->post_date_gmt);
  }

  function is_deleted($post){
    return $post->post_status === 'trash';
  }

  function format_data($transform, $id, $post){
    global $hookah_transforms;

    if($transform === 'default'){
      return $post;
    } else {
      $available_transforms = $hookah_transforms[$this->type];
      foreach($available_transforms as $the_transform){
        if($the_transform->alias === $transform){
          $callback = $the_transform->callback;
          return $callback($id);
        }
      }
      return new WP_Error('bad_transform', "Specified transform `$transform` not initialized");
    }
  }

  function handle_send($hook, $id, $post){
    $data = $this->format_data($hook->transform, $id, $post);

    $auth = false;
    if($hook->authmethod && $hook->authtoken){
      $auth = ['authmethod' => $hook->authmethod, 'authtoken'  => $hook->authtoken];
    }

    include('hookah-send.php');
    hookah_send($data, $hook->endpoint, $auth, $id);
  }

}

$controller = new HookahController($id, $post);
