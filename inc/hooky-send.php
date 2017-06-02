<?php

// This file contains functions to extend Hooky's functionality.

/**
 * Can be used to register custom filter for data being sent as part of a hook.
 * @param string       $alias    Name of filter.
 * @param array|string $types    Post type or types for this filter. Can be string or array of strings.
 * @param function     $callback Closure that will return filter value. Receives ID argument.
 * @return void
 */
function hooky_add_filter($alias = '', $types = [], $callback = NULL){
  global $hooky_filters;

  if($types === 'all'){
    $types = get_post_types();
  }

  if(is_array($types)){
    foreach($types as $type){
      $hooky_filters[$type][] = new HookyCallback($alias, $callback);
    }
  } else {
    $hooky_filters[$types][] = new HookyCallback($alias, $callback);
  }
}

/**
 * Can be used to register custom filters for endpoints that data can be send to.
 * @param string       $alias    Name of filter.
 * @param array|string $types    Post type or types for this filter. Can be string or array of strings.
 * @param function     $callback Closure that will return filter value. Receives ID argument.
 * @return void
 */
function hooky_add_endpoint_filter($alias = '', $types = [], $callback = NULL){
  global $hooky_endpoint_filters;

  if($types === 'all'){
    $types = get_post_types();
  }

  if(is_array($types)){
    foreach($types as $type){
      $hooky_endpoint_filters[$type][] = new HookyCallback($alias, $callback);
    }
  } else {
    $hooky_endpoint_filters[$types][] = new HookyCallback($alias, $callback);
  }
}

/**
 * Can be used to register custom callbacks for when data has been successfully POSTed.
 * @param string       $alias    Name of filter.
 * @param array|string $types    Post type or types for this filter. Can be string or array of strings.
 * @param function     $callback Closure that will handle succes. Receives ID and response body arguments.
 * @return void
 */
function hooky_add_success_callback($alias = '', $types = [], $callback = NULL){
  global $hooky_success_callbacks;

    if($types === 'all'){
      $types = get_post_types();
    }

    if(is_array($types)){
      foreach($types as $type){
        $hooky_success_callbacks[$type][] = new HookyCallback($alias, $callback);
      }
    } else {
      $hooky_success_callbacks[$types][] = new HookyCallback($alias, $callback);
    }
}
