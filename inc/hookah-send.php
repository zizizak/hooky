<?php

function hookah_send($data, $endpoint, $auth = false, $id = false){
  $headers = ['Content-Type' => 'application/json; charset=utf-8'];
  if($auth) $headers['Authorization'] = $auth['authmethod'] . $auth['authtoken'];

  if($id) $endpoint = str_replace('<id>', $id, $endpoint);

  $submit = wp_remote_post($endpoint, [
    'headers' => $headers,
    'body'    => json_encode($data)
  ]);

}
