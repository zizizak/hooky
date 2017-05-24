<?php

/**
 * This file is responsible for registering various REST endpoints
 * that are used by the dashboard UI. This
 *
 * TODO: Rewrite dashboard JS to make use of these endpoints
 * rather than relying on variables from wp_localize_script.
 */

/**
 * Handles GET requests, returns array of available hooks. May be
 * used by dashboard UI in the future.
 */
register_rest_route(HOOKY_NAMESPACE, 'hooks', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    global $wpdb;
    $hooks_table = HOOKY_TABLE;
    $sql = "SELECT * from $hooks_table";
    $results = $wpdb->get_results($sql, OBJECT);
    return new WP_REST_Response($results);
  }
]);

/**
 * Handles POST requests for adding hooks. Used by dashboard UI.
 */
register_rest_route(HOOKY_NAMESPACE, 'hooks', [
  'methods'  => WP_REST_Server::CREATABLE,
  'callback' => function($req){
    global $wpdb;
    $params = $req->get_json_params();
    $params['id'] = NULL;
    $rows = $wpdb->insert(HOOKY_TABLE, $params);

    return is_numeric($rows) ?
      new WP_REST_Response(array('affected_rows' => $rows, 'id' => $wpdb->insert_id)) :
      new WP_Error('err', 'Error inserting hook', array('status' => 500));
  }
]);

/**
 * Handles POST or PATCH requests for updating an existing hook. Used
 * by dashboard UI.
 */
register_rest_route(HOOKY_NAMESPACE, 'hooks/(?P<id>\d+)', [
  'methods'  => WP_REST_Server::EDITABLE,
  'callback' => function($req){
    global $wpdb;
    $params = $req->get_json_params();
    $id = (int) $req->get_params()['id'];

    $rows = $wpdb->update(HOOKY_TABLE, $params, ['id' => $id]);

    return is_numeric($rows) ?
      new WP_REST_Response(array('affected_rows' => $rows, 'id' => $id)) :
      new WP_Error('err', 'Error updating hook', array('status' => 500));
  }
]);

/**
 * Handles DELETE requests for de-registering hooks. Used by dashboard UI.
 */
register_rest_route(HOOKY_NAMESPACE, 'hooks/(?P<id>\d+)', [
  'methods'  => WP_REST_Server::DELETABLE,
  'callback' => function($req){
    global $wpdb;
    $id   = $req->get_params()['id'];
    $rows = $wpdb->delete(HOOKY_TABLE, ['id' => $id]);
    return $rows ?
      new WP_REST_Response(array('affected_rows' => $rows)) :
      new WP_Error('err', 'Hook not found', array('status' => 404));
  }
]);

/**
 * Handle GET requests for retrieving array of post types. May be
 * used by dashboard UI in the future.
 */

$post_types = get_post_types(['public' => true]);

register_rest_route(HOOKY_NAMESPACE, 'types', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req) use($post_types) {
    return new WP_REST_Response($post_types);
  }
]);

/**
 * Loop over post types array to form regex that can be used
 * to validate requests for filters by type.
 */
$post_types_regex = '';

foreach($post_types as $post_type){
  if($i) $post_types_regex .= '|';
  $post_types_regex .= $post_type;
  $i = true;
}

/**
 * Handles GET requests for retrieving an array of filter
 * aliases available for a specific post type. May be used
 * by dashboard UI in the future.
 */
register_rest_route(HOOKY_NAMESPACE, "filters/(?P<type>$post_types_regex)", [
  'methods'  => WP_Rest_Server::READABLE,
  'callback' => function($req){
    global $hooky_filters;
    $type = $req->get_params()['type'];
    $filters = ['default'];
    foreach($hooky_filters[$type] as $hooky_filter){
      $filters[] = (string) $hooky_filter->alias;
    }
    return new WP_REST_Response($filters);
  }
]);

/**
 * Handles GET requests for retrieving an array of available
 * authentication methods for outgoing hooks. May be used by
 * dashboard UI in the future.
 *
 * TODO: Add functionality to extend available authentication
 * methods.
 */
register_rest_route(HOOKY_NAMESPACE, 'auth', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    return new WP_REST_Response(['Basic', 'None']);
  }
]);

/**
 * Handles GET requests for available trigger aliases.
 *
 * TODO: Add functionality to extend available trigger actions.
 */
register_rest_route(HOOKY_NAMESPACE, 'actions', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    return new WP_REST_Response(['CREATE', 'UPDATE', 'DELETE']);
  }
]);
