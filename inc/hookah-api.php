<?php

global $wpdb;
define('HOOKAH_TABLE', $wpdb->prefix . 'hookah_hooks');

$post_types = get_post_types(['public' => true]);
$post_types_regex = '';

foreach($post_types as $post_type){
  if($i) $post_types_regex .= '|';
  $post_types_regex .= $post_type;
  $i = true;
}

register_rest_route(HOOKAH_NAMESPACE, 'hooks', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    global $wpdb;
    $hooks_table = $wpdb->prefix . 'hookah_hooks';
    $sql = "SELECT * from $hooks_table";
    $results = $wpdb->get_results($sql, OBJECT);
    return new WP_REST_Response($results);
  }
]);

register_rest_route(HOOKAH_NAMESPACE, 'hooks', [
  'methods'  => WP_REST_Server::CREATABLE,
  'callback' => function($req){
    global $wpdb;
    $params = $req->get_json_params();
    $params['id'] = NULL;
    $rows = $wpdb->insert(HOOKAH_TABLE, $params);

    return is_numeric($rows) ?
      new WP_REST_Response(array('affected_rows' => $rows, 'id' => $wpdb->insert_id)) :
      new WP_Error('err', 'Error inserting hook', array('status' => 500));
  }
]);

register_rest_route(HOOKAH_NAMESPACE, 'hooks/(?P<id>\d+)', [
  'methods'  => WP_REST_Server::EDITABLE,
  'callback' => function($req){
    global $wpdb;
    $params = $req->get_json_params();
    $id = (int) $req->get_params()['id'];

    $rows = $wpdb->update(HOOKAH_TABLE, $params, ['id' => $id]);

    return is_numeric($rows) ?
      new WP_REST_Response(array('affected_rows' => $rows, 'id' => $id)) :
      new WP_Error('err', 'Error updating hook', array('status' => 500));
  }
]);

register_rest_route(HOOKAH_NAMESPACE, 'hooks/(?P<id>\d+)', [
  'methods'  => WP_REST_Server::DELETABLE,
  'callback' => function($req){
    global $wpdb;
    $id   = $req->get_params()['id'];
    $rows = $wpdb->delete(HOOKAH_TABLE, ['id' => $id]);
    return $rows ?
      new WP_REST_Response(array('affected_rows' => $rows)) :
      new WP_Error('err', 'Hook not found', array('status' => 404));
  }
]);

register_rest_route(HOOKAH_NAMESPACE, 'types', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req) use($post_types) {
    return new WP_REST_Response($post_types);
  }
]);

register_rest_route(HOOKAH_NAMESPACE, "transforms/(?P<type>$post_types_regex)", [
  'methods'  => WP_Rest_Server::READABLE,
  'callback' => function($req){
    global $hookah_transforms;
    $type = $req->get_params()['type'];
    $transforms = ['default'];
    foreach($hookah_transforms[$type] as $hookah_transform){
      $transforms[] = (string) $hookah_transform->alias;
    }
    return new WP_REST_Response($transforms);
  }
]);

// TODO: add alternative auth methods
register_rest_route(HOOKAH_NAMESPACE, 'auth', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    return new WP_REST_Response(['Basic', 'None']);
  }
]);

// TODO: add ability to extend these options
register_rest_route(HOOKAH_NAMESPACE, 'actions', [
  'methods'  => WP_REST_Server::READABLE,
  'callback' => function($req){
    return new WP_REST_Response(['CREATE', 'UPDATE', 'DELETE']);
  }
]);
