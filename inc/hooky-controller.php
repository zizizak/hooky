<?php

/**
 * HookyController is instantiated when a post is saved.
 * It takes care of POSTing data, running filters, and running success calbbacks.
 * @param Int     $id    Post ID
 * @param WP_Post $post Post Object
 */

class HookyController {

  function __construct($id, $post){
    $this->id = $id;
    $this->post = $post;
    $this->type = get_post_type($id);
    $hooks = get_hooks_by_type($this->type);

    // For each registered hook, check against post status and on match, send POST request
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

  /**
   * Determines if a post has been newly created.
   *
   * @param  WP_Post  $post    A WordPress post object.
   * @return boolean           True if newly created, false if not.
   */
  private function is_created($post){
    return ($post->post_status === 'publish') && ($post->post_date_gmt === $post->post_modified_gmt);
  }

  /**
   * Determines if a post has been updated (NOT published for first time).
   *
   * @param  WP_Post  $post    A WordPress post object.
   * @return boolean           True if updated, false if not.
   */
  private function is_updated($post){
    return ($post->post_status === 'publish') && ($post->post_date_gmt !== $post->post_modified_gmt);
  }

  /**
   * Determines if a post has been deleted.
   *
   * @param  WP_Post  $post    A WordPress post object.
   * @return boolean           True if deleted, false if not.
   */
  private function is_deleted($post){
    return $post->post_status === 'trash';
  }

  /**
   * Prepare the data from our post object to be POSTed as an HTTP callback,
   * based on the filter that has been specified for a hook.
   * Returns a standard REST response in case of default,
   * the return value of specified transfrom in case of non-default filter,
   * or WP Error if a non-default filter was specified but doesn't exist.
   *
   * @param  String    $filter       Alias for filter to be used.
   * @param  Int       $id           Post ID.
   * @param  WP_Post   $post         Post object.
   * @return mixed
   *
   */
  private function format_data($filter, $id, $post){

    // If default filter is to be used, return the default REST response.
    if($filter === 'default'){

      $controller = new WP_Rest_Posts_Controller($this->type);
      $response = $controller->prepare_item_for_response($post, null);
      return $response->data;

    } else {
      global $hooky_filters;

      // Get all hooks associated with this type.
      $available_filters = $hooky_filters[$this->type];

      foreach($available_filters as $available_filter){

        if($available_filter->alias === $filter){
          $callback = $available_filter->callback;
          return $callback($id);
        }

      }

      return new WP_Error('bad_filter', "Specified filter `$filter` not initialized");

    }
  }

  /**
   * Fire off a POST request as part of callback. Calls internally defined success and error callbacks.
   * Success callback can be extended with hooky_add_success_callback. Callbacks will set a session
   * variable used to display success and error messages for post.
   * @param  Object   $hook     Hook object
   * @param  Int      $id       ID of post
   * @param  WP_Post  $post     WP Post object
   * @return void
   *
   */
  private function handle_send($hook, $id, $post){
    $data = $this->format_data($hook->filter, $id, $post);

    $auth = false;
    if($hook->authmethod && $hook->authtoken){
      $auth = ['authmethod' => $hook->authmethod, 'authtoken'  => $hook->authtoken];
    }

    global $hooky_endpoint_filters;
    $endpoint_filters = $hooky_endpoint_filters[$this->type];

    foreach($endpoint_filters as $endpoint_filter){
      if($endpoint_filter->alias === $hook->endpoint_filter){
        $callback = $endpoint_filter->callback;
        if(!$callback) continue;
        $hook->endpoint = $callback($hook->endpoint, $post);
        break;
      }
    }

    $sender = new HookySend($data, $hook->endpoint, $auth);

    $sender->send(
      // Success
      function($response) use ($id, $hook) {
        $label = "$hook->endpoint on $hook->action";
        $message = __('Posted to') . ' ' . $hook->endpoint . ' ' . __('successfully');
        $_SESSION[HOOKY_SESSION_SUCCESS][] = "<strong>$label</strong>: $message";

        if($hook->success_callback){
          global $hooky_success_callbacks;
          $success_callbacks = $hooky_success_callbacks[$this->type];
          if(!$success_callbacks) return;
          foreach($success_callbacks as $success_callback){
            if($success_callback->alias === $hook->success_callback){
              $callback = $success_callback->callback;
              if(!$callback) continue;
              $callback($id, $response);
            }
          }
        }
      },
      // Error
      function($err) use ($hook) {
        $label = "$hook->endpoint on $hook->action";
        $message = $err['body'];
        $_SESSION[HOOKY_SESSION_ERR][] = "<strong>$label</strong>: $message";
      }
    );

  }

}
